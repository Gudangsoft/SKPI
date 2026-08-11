<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\PejabatPenandatangan;
use Illuminate\Database\Seeder;

class PejabatPenandatanganSeeder extends Seeder
{
    public function run(): void
    {
        PejabatPenandatangan::firstOrCreate(
            ['nama' => 'Prof. Dr. Bambang Wicaksono, M.Si.'],
            ['jabatan' => 'Rektor', 'fakultas_id' => null, 'aktif' => true],
        );

        $dekans = [
            'FT' => 'Dr. Ir. Sutrisno Hadi, M.T.',
            'FEB' => 'Dr. Ratna Kusumawati, S.E., M.M.',
            'FIKOM' => 'Dr. Eng. Yulianto Nugroho, S.Kom., M.Kom.',
        ];

        foreach ($dekans as $kodeFakultas => $nama) {
            $fakultas = Fakultas::where('kode_fakultas', $kodeFakultas)->first();

            if (! $fakultas) {
                continue;
            }

            PejabatPenandatangan::firstOrCreate(
                ['nama' => $nama],
                ['jabatan' => 'Dekan', 'fakultas_id' => $fakultas->id, 'aktif' => true],
            );
        }
    }
}
