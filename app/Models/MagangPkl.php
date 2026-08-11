<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagangPkl extends Model
{
    protected $table = 'magang_pkls';

    protected $fillable = [
        'pengajuan_skpi_id',
        'nama_instansi',
        'posisi',
        'periode_mulai',
        'periode_selesai',
        'dokumen_bukti_path',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
        ];
    }

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }
}
