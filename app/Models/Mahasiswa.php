<?php

namespace App\Models;

use App\Enums\JenisKelamin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_studi_id',
        'nim',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'angkatan',
        'tahun_lulus',
        'no_hp',
        'alamat',
        'foto_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'jenis_kelamin' => JenisKelamin::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function pengajuanSkpis(): HasMany
    {
        return $this->hasMany(PengajuanSkpi::class);
    }
}
