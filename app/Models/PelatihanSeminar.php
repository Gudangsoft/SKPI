<?php

namespace App\Models;

use App\Enums\PeranPelatihan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelatihanSeminar extends Model
{
    protected $table = 'pelatihan_seminars';

    protected $fillable = [
        'pengajuan_skpi_id',
        'nama_kegiatan',
        'penyelenggara',
        'peran',
        'tahun',
        'jumlah_jam',
        'dokumen_bukti_path',
    ];

    protected function casts(): array
    {
        return [
            'peran' => PeranPelatihan::class,
        ];
    }

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }
}
