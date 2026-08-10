<?php

namespace Database\Seeders;

use App\Enums\Jenjang;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['fakultas' => 'FT', 'kode_prodi' => 'TI', 'nama_prodi' => 'Teknik Informatika', 'jenjang' => Jenjang::S1],
            ['fakultas' => 'FT', 'kode_prodi' => 'TE', 'nama_prodi' => 'Teknik Elektro', 'jenjang' => Jenjang::S1],
            ['fakultas' => 'FEB', 'kode_prodi' => 'MJ', 'nama_prodi' => 'Manajemen', 'jenjang' => Jenjang::S1],
            ['fakultas' => 'FEB', 'kode_prodi' => 'AK', 'nama_prodi' => 'Akuntansi', 'jenjang' => Jenjang::S1],
            ['fakultas' => 'FIKOM', 'kode_prodi' => 'SI', 'nama_prodi' => 'Sistem Informasi', 'jenjang' => Jenjang::S1],
        ];

        foreach ($items as $item) {
            $fakultas = Fakultas::where('kode_fakultas', $item['fakultas'])->firstOrFail();

            ProgramStudi::firstOrCreate(
                ['kode_prodi' => $item['kode_prodi']],
                [
                    'fakultas_id' => $fakultas->id,
                    'nama_prodi' => $item['nama_prodi'],
                    'jenjang' => $item['jenjang'],
                ]
            );
        }
    }
}
