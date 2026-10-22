<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriLayanan extends Model
{
    protected $table = 'kategori_layanan';

    protected $fillable = [
        'nama_layanan',
        'status_aktif',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'id_kategori_api');
    }
}
