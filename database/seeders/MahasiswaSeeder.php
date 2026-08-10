<?php

namespace Database\Seeders;

use App\Enums\JenisKelamin;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['nim' => '2021010001', 'nama' => 'Andi Pratama', 'prodi' => 'TI', 'jk' => JenisKelamin::L, 'angkatan' => 2021],
            ['nim' => '2021010002', 'nama' => 'Bunga Lestari', 'prodi' => 'TI', 'jk' => JenisKelamin::P, 'angkatan' => 2021],
            ['nim' => '2022020001', 'nama' => 'Citra Ayu Dewi', 'prodi' => 'SI', 'jk' => JenisKelamin::P, 'angkatan' => 2022],
            ['nim' => '2020010003', 'nama' => 'Dimas Setiawan', 'prodi' => 'TI', 'jk' => JenisKelamin::L, 'angkatan' => 2020, 'tahun_lulus' => 2024],
        ];

        foreach ($students as $s) {
            $prodi = ProgramStudi::where('kode_prodi', $s['prodi'])->first();

            if (! $prodi) {
                continue;
            }

            $email = strtolower(str_replace(' ', '.', $s['nama'])).'@student.skpi.test';

            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $s['nama'], 'password' => 'password']
            );
            $user->syncRoles([Roles::MAHASISWA]);

            Mahasiswa::firstOrCreate(
                ['nim' => $s['nim']],
                [
                    'user_id' => $user->id,
                    'program_studi_id' => $prodi->id,
                    'nama_lengkap' => $s['nama'],
                    'jenis_kelamin' => $s['jk'],
                    'angkatan' => $s['angkatan'],
                    'tahun_lulus' => $s['tahun_lulus'] ?? null,
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => now()->subYears(22)->startOfYear(),
                    'no_hp' => '081234567890',
                    'alamat' => 'Jl. Contoh No. 1, Jakarta',
                ]
            );
        }
    }
}
