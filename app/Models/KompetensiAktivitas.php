<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KompetensiAktivitas extends Model
{
    protected $table = 'kompetensi_aktivitas';

    protected $fillable = [
        'pengajuan_skpi_id',
        'nama_kegiatan',
        'keterangan',
        'tahun',
        'dokumen_bukti_path',
    ];

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }
}
