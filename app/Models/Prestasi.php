<?php

namespace App\Models;

use App\Enums\TingkatPrestasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestasi extends Model
{
    protected $fillable = [
        'pengajuan_skpi_id',
        'nama_prestasi',
        'tingkat',
        'peringkat',
        'penyelenggara',
        'tahun',
        'dokumen_bukti_path',
    ];

    protected function casts(): array
    {
        return [
            'tingkat' => TingkatPrestasi::class,
        ];
    }

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }
}
