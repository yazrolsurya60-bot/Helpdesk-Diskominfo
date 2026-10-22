<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\SlaService;

#[Signature('tickets:auto-release')]
#[Description('Release stuck tickets that have been in progress for more than 2 working hours (Mon-Fri 07:30-15:45).')]
class AutoReleaseTickets extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Filter awal: ambil tiket progress dengan assigned_at minimal 2 jam kalender yang lalu
        $threshold = Carbon::now()->subHours(2);
        
        $candidates = DB::table('tickets')
            ->where('status', 'progress')
            ->whereNotNull('assigned_at')
            ->where('assigned_at', '<=', $threshold)
            ->get();
            
        // Filter akurat: hanya tiket yang telah melebihi batas 2 jam kerja aktif (Senin - Jumat, 07:30 - 15:45)
        $stuckTickets = $candidates->filter(function ($ticket) {
            return SlaService::isPastSla($ticket->assigned_at);
        });

        if ($stuckTickets->isEmpty()) {
            $this->info('No stuck tickets found to auto-release.');
            return;
        }
        
        $count = 0;
        
        foreach ($stuckTickets as $ticket) {
            DB::transaction(function () use ($ticket) {
                // Update ticket
                DB::table('tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'status' => 'open',
                        'user_id' => null,
                        'assigned_at' => null,
                        'updated_at' => Carbon::now(),
                    ]);
                    
                // Record to riwayat_tiket
                DB::table('riwayat_tiket')->insert([
                    'ticket_id' => $ticket->id,
                    'user_id' => null, // Sistem yang melakukan
                    'aksi' => 'dialihkan',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Add notification if staff was assigned
                if ($ticket->user_id) {
                    DB::table('notifikasi_internal')->insert([
                        'user_id' => $ticket->user_id,
                        'judul' => "Tugas tiket #{$ticket->id} dicabut otomatis (melebihi 2 jam).",
                        'is_read' => false,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            });
            
            $count++;
        }
        
        $this->info("Successfully auto-released {$count} tickets.");
    }
}
