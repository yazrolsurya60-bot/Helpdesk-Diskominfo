<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_wa',
        'nama_pemohon',
        'keluhan',
        'id_kategori_api',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class, 'id_kategori_api');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatTiket::class, 'ticket_id');
    }

    public function waLogs(): HasMany
    {
        return $this->hasMany(WaLog::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(LampiranTiket::class, 'ticket_id');
    }
}
