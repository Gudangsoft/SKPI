<?php

namespace App\Support;

use App\Enums\PeranPelatihan;
use App\Enums\TingkatPrestasi;
use App\Models\KompetensiAktivitas;
use App\Models\MagangPkl;
use App\Models\Organisasi;
use App\Models\PelatihanSeminar;
use App\Models\Prestasi;
use App\Models\Sertifikasi;

/**
 * Field/model configuration for each repeatable pengajuan category, shared by the
 * generic RepeatableCategoryManager Livewire component and its Blade view so the
 * six categories don't each need their own hand-written CRUD component.
 */
class PengajuanKategori
{
    public static function definitions(): array
    {
        return [
            'prestasi' => [
                'label' => 'Prestasi',
                'model' => Prestasi::class,
                'fields' => [
                    ['name' => 'nama_prestasi', 'label' => 'Nama Prestasi', 'type' => 'text', 'required' => true],
                    ['name' => 'tingkat', 'label' => 'Tingkat', 'type' => 'select', 'required' => true, 'options' => TingkatPrestasi::class],
                    ['name' => 'peringkat', 'label' => 'Peringkat', 'type' => 'text', 'required' => false],
                    ['name' => 'penyelenggara', 'label' => 'Penyelenggara', 'type' => 'text', 'required' => false],
                    ['name' => 'tahun', 'label' => 'Tahun', 'type' => 'number', 'required' => true],
                ],
            ],
            'organisasi' => [
                'label' => 'Organisasi',
                'model' => Organisasi::class,
                'fields' => [
                    ['name' => 'nama_organisasi', 'label' => 'Nama Organisasi', 'type' => 'text', 'required' => true],
                    ['name' => 'jabatan', 'label' => 'Jabatan', 'type' => 'text', 'required' => true],
                    ['name' => 'periode_mulai', 'label' => 'Periode Mulai', 'type' => 'date', 'required' => true],
                    ['name' => 'periode_selesai', 'label' => 'Periode Selesai', 'type' => 'date', 'required' => false],
                ],
            ],
            'sertifikasi' => [
                'label' => 'Sertifikasi',
                'model' => Sertifikasi::class,
                'fields' => [
                    ['name' => 'nama_sertifikat', 'label' => 'Nama Sertifikat', 'type' => 'text', 'required' => true],
                    ['name' => 'penerbit', 'label' => 'Penerbit', 'type' => 'text', 'required' => true],
                    ['name' => 'no_sertifikat', 'label' => 'No. Sertifikat', 'type' => 'text', 'required' => false],
                    ['name' => 'tahun', 'label' => 'Tahun', 'type' => 'number', 'required' => true],
                ],
            ],
            'pelatihan-seminar' => [
                'label' => 'Pelatihan / Seminar',
                'model' => PelatihanSeminar::class,
                'fields' => [
                    ['name' => 'nama_kegiatan', 'label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                    ['name' => 'penyelenggara', 'label' => 'Penyelenggara', 'type' => 'text', 'required' => false],
                    ['name' => 'peran', 'label' => 'Peran', 'type' => 'select', 'required' => true, 'options' => PeranPelatihan::class],
                    ['name' => 'tahun', 'label' => 'Tahun', 'type' => 'number', 'required' => true],
                    ['name' => 'jumlah_jam', 'label' => 'Jumlah Jam/SKS', 'type' => 'number', 'required' => false],
                ],
            ],
            'magang-pkl' => [
                'label' => 'Magang / PKL',
                'model' => MagangPkl::class,
                'fields' => [
                    ['name' => 'nama_instansi', 'label' => 'Nama Instansi', 'type' => 'text', 'required' => true],
                    ['name' => 'posisi', 'label' => 'Posisi', 'type' => 'text', 'required' => true],
                    ['name' => 'periode_mulai', 'label' => 'Periode Mulai', 'type' => 'date', 'required' => true],
                    ['name' => 'periode_selesai', 'label' => 'Periode Selesai', 'type' => 'date', 'required' => false],
                ],
            ],
            'kompetensi-aktivitas' => [
                'label' => 'Kompetensi / Aktivitas Pendukung',
                'model' => KompetensiAktivitas::class,
                'fields' => [
                    ['name' => 'nama_kegiatan', 'label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea', 'required' => false],
                    ['name' => 'tahun', 'label' => 'Tahun', 'type' => 'number', 'required' => true],
                ],
            ],
        ];
    }

    public static function forSlug(string $slug): ?array
    {
        return static::definitions()[$slug] ?? null;
    }

    public static function slugs(): array
    {
        return array_keys(static::definitions());
    }
}
