<?php

namespace App\Models;

use App\Enums\PengajuanStatus;
use App\Enums\PredikatKelulusan;
use App\Observers\PengajuanStatusObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[ObservedBy(PengajuanStatusObserver::class)]
class PengajuanSkpi extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'status',
        'ipk',
        'judul_skripsi',
        'tanggal_lulus',
        'no_ijazah',
        'predikat_kelulusan',
        'catatan_revisi',
        'diajukan_at',
        'diverifikasi_prodi_by',
        'diverifikasi_prodi_at',
        'disetujui_fakultas_by',
        'disetujui_fakultas_at',
        'pejabat_penandatangan_id',
        'nomor_skpi',
        'nomor_skpi_generated_at',
        'pdf_path',
        'verification_token',
        'published_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (PengajuanSkpi $pengajuan) {
            $pengajuan->verification_token ??= (string) Str::uuid();
            $pengajuan->status ??= PengajuanStatus::Draft;
        });
    }

    protected function casts(): array
    {
        return [
            'status' => PengajuanStatus::class,
            'predikat_kelulusan' => PredikatKelulusan::class,
            'ipk' => 'decimal:2',
            'tanggal_lulus' => 'date',
            'diajukan_at' => 'datetime',
            'diverifikasi_prodi_at' => 'datetime',
            'disetujui_fakultas_at' => 'datetime',
            'nomor_skpi_generated_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function diverifikasiProdiBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_prodi_by');
    }

    public function disetujuiFakultasBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_fakultas_by');
    }

    public function pejabatPenandatangan(): BelongsTo
    {
        return $this->belongsTo(PejabatPenandatangan::class);
    }

    public function prestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class);
    }

    public function organisasis(): HasMany
    {
        return $this->hasMany(Organisasi::class);
    }

    public function sertifikasis(): HasMany
    {
        return $this->hasMany(Sertifikasi::class);
    }

    public function pelatihanSeminars(): HasMany
    {
        return $this->hasMany(PelatihanSeminar::class);
    }

    public function magangPkls(): HasMany
    {
        return $this->hasMany(MagangPkl::class);
    }

    public function kompetensiAktivitas(): HasMany
    {
        return $this->hasMany(KompetensiAktivitas::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(PengajuanStatusHistory::class)->latest('id');
    }

    public function isEditableByMahasiswa(): bool
    {
        return in_array($this->status, PengajuanStatus::editableByMahasiswa(), true);
    }
}
