<?php

namespace App\Services;

use App\Models\PengajuanSkpi;
use Illuminate\Support\Facades\DB;

class NomorSkpiGenerator
{
    private const BULAN_ROMAWI = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    public function generate(PengajuanSkpi $pengajuan): string
    {
        return DB::transaction(function () use ($pengajuan) {
            $year = now()->year;

            $count = PengajuanSkpi::query()
                ->whereYear('nomor_skpi_generated_at', $year)
                ->lockForUpdate()
                ->count();

            $urutan = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
            $bulanRomawi = self::BULAN_ROMAWI[now()->month];
            $kodeProdi = $pengajuan->mahasiswa->programStudi->kode_prodi;

            return "{$urutan}/SKPI/{$kodeProdi}/{$bulanRomawi}/{$year}";
        });
    }
}
