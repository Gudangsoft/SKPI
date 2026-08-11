<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang SKPI',
                'slug' => 'tentang-skpi',
                'content' => '<p>Surat Keterangan Pendamping Ijazah (SKPI) adalah dokumen resmi yang menjelaskan capaian akademik dan non-akademik mahasiswa selama masa studi, mendampingi ijazah sebagai bukti kompetensi tambahan.</p><p>Portal ini memudahkan mahasiswa mengajukan, melacak, dan mengunduh SKPI secara daring tanpa perlu datang ke kampus.</p>',
                'meta_description' => 'Penjelasan singkat mengenai Surat Keterangan Pendamping Ijazah (SKPI).',
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<p><strong>Berapa lama proses pengajuan SKPI?</strong></p><p>Proses verifikasi prodi dan validasi kaprodi umumnya selesai dalam beberapa hari kerja setelah pengajuan lengkap.</p><p><strong>Apa yang terjadi jika pengajuan saya perlu revisi?</strong></p><p>Anda akan menerima catatan revisi pada halaman status pengajuan dan dapat memperbaikinya langsung dari portal.</p>',
                'meta_description' => 'Pertanyaan yang sering diajukan seputar pengajuan SKPI.',
            ],
            [
                'title' => 'Panduan Pengajuan',
                'slug' => 'panduan-pengajuan',
                'content' => '<p>Lengkapi data akademik Anda, lalu isi kategori pendukung (prestasi, organisasi, sertifikasi, pelatihan/seminar, magang/PKL, dan kompetensi lain) beserta bukti pendukungnya sebelum mengirim pengajuan untuk diverifikasi.</p>',
                'meta_description' => 'Langkah-langkah mengajukan SKPI melalui portal.',
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                [...$page, 'published_at' => now()],
            );
        }
    }
}
