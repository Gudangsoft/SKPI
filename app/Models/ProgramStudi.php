<?php

namespace App\Models;

use App\Enums\Jenjang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $fillable = [
        'fakultas_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];

    protected function casts(): array
    {
        return [
            'jenjang' => Jenjang::class,
        ];
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama_prodi} ({$this->jenjang->value})";
    }
}
