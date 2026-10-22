<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiInternal;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai sudah dibaca dan arahkan ke tiket terkait (jika ada).
     */
    public function markAsRead(Request $request, NotifikasiInternal $notifikasi)
    {
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah ke notifikasi ini.');
        }

        if (!$notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // Jika notifikasi merujuk ke tiket tertentu (misal: "Tiket #1042...")
        if (preg_match('/#([0-9]+)/', $notifikasi->judul, $matches)) {
            $ticketId = (int) $matches[1];
            $ticket = Ticket::find($ticketId);

            if ($ticket) {
                $userRole = Auth::user()->role;
                if ($userRole === 'staf_bidang') {
                    return redirect()->route('staf.tickets.chat', $ticket->id);
                } elseif ($userRole === 'front_desk') {
                    return redirect()->route('frontdesk.tickets.chat', $ticket->id);
                }
            }
        }

        return back();
    }

    /**
     * Tandai seluruh notifikasi milik pengguna saat ini sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        NotifikasiInternal::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}
