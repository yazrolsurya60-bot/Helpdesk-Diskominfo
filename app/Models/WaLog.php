<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaLog extends Model
{
    protected $table = 'wa_logs';

    protected $fillable = [
        'ticket_id',
        'sender_type',
        'message',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * The "booted" method of the model.
     * Meneruskan setiap balasan yang dikirim oleh petugas helpdesk dari dashboard ke WhatsApp pemohon.
     */
    protected static function booted(): void
    {
        static::created(function (WaLog $waLog) {
            if ($waLog->sender_type === 'pegawai') {
                try {
                    app(\App\Services\FoonteService::class)->sendOfficerReply($waLog);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Gagal meneruskan balasan petugas ke Foonte: ' . $e->getMessage());
                }
            }
        });
    }
}
