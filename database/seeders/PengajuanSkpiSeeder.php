<?php

namespace Database\Seeders;

use App\Enums\PengajuanStatus;
use App\Enums\PredikatKelulusan;
use App\Enums\TingkatPrestasi;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengajuanSkpiSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedFor('2021010001', PengajuanStatus::Diajukan, function ($pengajuan) {
            $pengajuan->update([
                'ipk' => 3.75,
                'judul_skripsi' => 'Sistem Informasi Akademik Berbasis Web',
                'tanggal_lulus' => now()->subMonths(2),
                'predikat_kelulusan' => PredikatKelulusan::Pujian,
            ]);

            $pengajuan->prestasis()->createMany([
                ['nama_prestasi' => 'Juara 1 Hackathon Nasional', 'tingkat' => TingkatPrestasi::Nasional, 'penyelenggara' => 'Kemendikbud', 'tahun' => 2023],
                ['nama_prestasi' => 'Finalis Lomba Karya Tulis Ilmiah', 'tingkat' => TingkatPrestasi::Wilayah, 'penyelenggara' => 'Dikti Wilayah III', 'tahun' => 2022],
            ]);

            $pengajuan->organisasis()->create([
                'nama_organisasi' => 'Himpunan Mahasiswa Teknik Informatika',
                'jabatan' => 'Ketua',
                'periode_mulai' => '2022-01-01',
                'periode_selesai' => '2023-01-01',
            ]);
        });

        $this->seedFor('2021010002', PengajuanStatus::Draft, function ($pengajuan) {
            $pengajuan->update([
                'ipk' => 3.60,
                'judul_skripsi' => 'Aplikasi Mobile Presensi Karyawan',
            ]);
        });

        $this->seedFor('2022020001', PengajuanStatus::Revisi, function ($pengajuan) {
            $pengajuan->update([
                'ipk' => 3.40,
                'judul_skripsi' => 'Analisis Sentimen Media Sosial Menggunakan Machine Learning',
                'tanggal_lulus' => now()->subMonth(),
                'predikat_kelulusan' => PredikatKelulusan::SangatMemuaskan,
                'catatan_revisi' => 'Mohon lampirkan dokumen bukti sertifikasi yang masih terbaca jelas.',
            ]);
        });

        $this->seedFor('2020010003', PengajuanStatus::DisetujuiProdi, function ($pengajuan) {
            $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->first();

            $pengajuan->update([
                'ipk' => 3.85,
                'judul_skripsi' => 'Optimasi Algoritma Penjadwalan pada Sistem Terdistribusi',
                'tanggal_lulus' => now()->subMonths(3),
                'predikat_kelulusan' => PredikatKelulusan::Pujian,
                'diverifikasi_prodi_by' => $adminProdi?->id,
                'diverifikasi_prodi_at' => now()->subDays(3),
            ]);

            $pengajuan->sertifikasis()->create([
                'nama_sertifikat' => 'AWS Certified Cloud Practitioner',
                'penerbit' => 'Amazon Web Services',
                'tahun' => 2024,
            ]);
        });
    }

    protected function seedFor(string $nim, PengajuanStatus $status, \Closure $callback): void
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (! $mahasiswa) {
            return;
        }

        $pengajuan = $mahasiswa->pengajuanSkpis()->firstOrCreate([], [
            'status' => PengajuanStatus::Draft,
        ]);

        $callback($pengajuan);

        $pengajuan->update(['status' => $status]);
    }
}
