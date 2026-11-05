<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LampiranTiket extends Model
{
    protected $table = 'lampiran_tiket';

    // Sesuaikan dengan Class Diagram murni (hanya mencatat created_at)
    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'file_path',
    ];

    /**
     * File upload instance (temporary property for OOP parameterless uploadFile method).
     */
    public ?UploadedFile $file = null;

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Upload berkas foto fisik ke storage dan simpan record ke database (sesuai Class Diagram).
     */
    public function uploadFile(?UploadedFile $file = null): self
    {
        $fileToUpload = $file ?? $this->file ?? request()->file('lampiran');

        if ($fileToUpload) {
            $path = $fileToUpload->store('lampiran_tiket', 'public');
            $this->file_path = $path;
            $this->save();
        }

        return $this;
    }

    /**
     * Download berkas foto fisik secara aman (sesuai Class Diagram).
     */
    public function downloadFile(): StreamedResponse
    {
        if (!Storage::disk('public')->exists($this->file_path)) {
            abort(404, 'Berkas foto lampiran tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($this->file_path, basename($this->file_path));
    }
}
