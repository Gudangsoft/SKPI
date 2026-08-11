@php
    $steps = [
        ['key' => 'akademik', 'label' => 'Data Akademik', 'route' => route('pengajuan.akademik', $pengajuan)],
        ['key' => 'prestasi', 'label' => 'Prestasi', 'route' => route('pengajuan.kategori', [$pengajuan, 'prestasi'])],
        ['key' => 'organisasi', 'label' => 'Organisasi', 'route' => route('pengajuan.kategori', [$pengajuan, 'organisasi'])],
        ['key' => 'sertifikasi', 'label' => 'Sertifikasi', 'route' => route('pengajuan.kategori', [$pengajuan, 'sertifikasi'])],
        ['key' => 'pelatihan-seminar', 'label' => 'Pelatihan / Seminar', 'route' => route('pengajuan.kategori', [$pengajuan, 'pelatihan-seminar'])],
        ['key' => 'magang-pkl', 'label' => 'Magang / PKL', 'route' => route('pengajuan.kategori', [$pengajuan, 'magang-pkl'])],
        ['key' => 'kompetensi-aktivitas', 'label' => 'Kompetensi / Aktivitas', 'route' => route('pengajuan.kategori', [$pengajuan, 'kompetensi-aktivitas'])],
        ['key' => 'review', 'label' => 'Review & Submit', 'route' => route('pengajuan.review', $pengajuan)],
    ];
    $current = $current ?? null;
@endphp

<nav class="flex flex-wrap gap-2 mb-6">
    @foreach ($steps as $step)
        <a
            href="{{ $step['route'] }}"
            class="px-3 py-1.5 rounded-md text-xs font-medium {{ $current === $step['key'] ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}"
        >
            {{ $step['label'] }}
        </a>
    @endforeach
</nav>
