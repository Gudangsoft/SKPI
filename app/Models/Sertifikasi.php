<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikasi extends Model
{
    protected $fillable = [
        'pengajuan_skpi_id',
        'nama_sertifikat',
        'penerbit',
        'no_sertifikat',
        'tahun',
        'dokumen_bukti_path',
    ];

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }
}
