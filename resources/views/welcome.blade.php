<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $setting = \App\Models\Setting::current(); @endphp

        <title>{{ $setting->app_name }} — Surat Keterangan Pendamping Ijazah</title>

        @if ($setting->faviconUrl())
            <link rel="icon" href="{{ $setting->faviconUrl() }}">
        @endif

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-white text-slate-800">

        @php
            $user = auth()->user();
            $dashboardUrl = null;

            if ($user) {
                $dashboardUrl = $user->hasRole(\App\Support\Roles::MAHASISWA)
                    ? route('dashboard')
                    : '/admin';
            }
        @endphp

        @include('partials.site-header')

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-indigo-50 via-white to-white"></div>
            <div class="absolute -top-24 right-[-10%] -z-10 h-96 w-96 rounded-full bg-indigo-100 blur-3xl"></div>
            <div class="absolute -bottom-32 left-[-10%] -z-10 h-96 w-96 rounded-full bg-teal-50 blur-3xl"></div>

            <div class="mx-auto max-w-6xl px-6 pb-20 pt-16 sm:pt-24">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                        <x-heroicon-o-sparkles class="h-3.5 w-3.5" />
                        Layanan Surat Keterangan Pendamping Ijazah
                    </span>

                    <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                        Ajukan SKPI Anda, <span class="text-indigo-600">tanpa antre ke kampus</span>
                    </h1>

                    <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-slate-500 sm:text-lg">
                        Satu portal untuk mengajukan, melacak, dan menerbitkan Surat Keterangan Pendamping Ijazah —
                        mulai dari prestasi, organisasi, sertifikasi, hingga pengalaman magang mahasiswa
                        {{ $setting->tagline }}.
                    </p>

                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="{{ $dashboardUrl ?? route('login') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 sm:w-auto">
                            Mulai Pengajuan
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                        <a href="#alur" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 sm:w-auto">
                            Lihat Alur Pengajuan
                        </a>
                    </div>

                    <p class="mt-6 text-xs text-slate-400">
                        Staf program studi &amp; fakultas silakan
                        <a href="{{ route('login') }}" class="font-medium text-slate-500 underline underline-offset-2 hover:text-slate-700">masuk di sini</a>
                        untuk memverifikasi pengajuan.
                    </p>
                </div>
            </div>
        </section>

        <!-- What goes into an SKPI -->
        <section class="mx-auto max-w-6xl px-6 py-16 sm:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Satu SKPI, semua capaian Anda</h2>
                <p class="mt-3 text-slate-500">
                    Lengkapi data akademik dan lampirkan bukti pendukung dari enam kategori berikut —
                    semuanya dirangkum otomatis menjadi satu dokumen resmi.
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['icon' => 'trophy', 'title' => 'Prestasi', 'desc' => 'Juara lomba tingkat lokal hingga internasional, akademik maupun non-akademik.'],
                    ['icon' => 'user-group', 'title' => 'Organisasi', 'desc' => 'Pengalaman berorganisasi dan jabatan yang pernah diemban selama kuliah.'],
                    ['icon' => 'shield-check', 'title' => 'Sertifikasi', 'desc' => 'Sertifikat kompetensi dan pelatihan bersertifikat dari lembaga resmi.'],
                    ['icon' => 'academic-cap', 'title' => 'Pelatihan / Seminar', 'desc' => 'Keikutsertaan sebagai peserta, panitia, maupun pemateri kegiatan ilmiah.'],
                    ['icon' => 'briefcase', 'title' => 'Magang / PKL', 'desc' => 'Pengalaman kerja praktik dan magang di instansi atau perusahaan mitra.'],
                    ['icon' => 'document-text', 'title' => 'Kompetensi Lain', 'desc' => 'Aktivitas dan kompetensi pendukung lain yang relevan dengan bidang studi.'],
                ] as $item)
                    <div class="rounded-2xl border border-slate-100 p-6 transition hover:border-indigo-100 hover:shadow-sm">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="h-5.5 w-5.5" />
                        </span>
                        <h3 class="mt-4 font-semibold text-slate-900">{{ $item['title'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Process -->
        <section id="alur" class="border-t border-slate-100 bg-slate-50/60">
            <div class="mx-auto max-w-6xl px-6 py-16 sm:py-20">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Alur pengajuan, jelas dari awal</h2>
                    <p class="mt-3 text-slate-500">Pantau statusnya kapan saja, dari draf sampai terbit.</p>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['icon' => 'pencil-square', 'color' => 'slate', 'step' => '1', 'title' => 'Ajukan', 'desc' => 'Lengkapi data akademik dan lampirkan bukti pendukung.'],
                        ['icon' => 'clock', 'color' => 'amber', 'step' => '2', 'title' => 'Verifikasi Prodi', 'desc' => 'Admin program studi memeriksa kelengkapan data.'],
                        ['icon' => 'check-badge', 'color' => 'sky', 'step' => '3', 'title' => 'Validasi Kaprodi', 'desc' => 'Kaprodi memberikan persetujuan akhir.'],
                        ['icon' => 'sparkles', 'color' => 'emerald', 'step' => '4', 'title' => 'SKPI Terbit', 'desc' => 'Dokumen resmi siap diunduh dan diverifikasi publik.'],
                    ] as $step)
                        <div class="relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-{{ $step['color'] }}-50 text-{{ $step['color'] }}-600">
                                    <x-dynamic-component :component="'heroicon-o-'.$step['icon']" class="h-5.5 w-5.5" />
                                </span>
                                <span class="text-2xl font-bold text-slate-100">{{ $step['step'] }}</span>
                            </div>
                            <h3 class="mt-4 font-semibold text-slate-900">{{ $step['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="mx-auto max-w-6xl px-6 py-16 sm:py-20">
            <div class="flex flex-col items-center justify-between gap-6 rounded-3xl bg-indigo-600 px-8 py-10 text-center sm:flex-row sm:px-12 sm:text-left">
                <div>
                    <h2 class="text-xl font-bold text-white sm:text-2xl">Siap mengajukan SKPI Anda?</h2>
                    <p class="mt-1.5 text-sm text-indigo-100">Masuk dengan akun mahasiswa untuk memulai pengajuan.</p>
                </div>
                <a href="{{ $dashboardUrl ?? route('login') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-indigo-600 shadow-sm transition hover:bg-indigo-50">
                    Mulai Sekarang
                    <x-heroicon-o-arrow-right class="h-4 w-4" />
                </a>
            </div>
        </section>

        @include('partials.site-footer')
    </body>
</html>
