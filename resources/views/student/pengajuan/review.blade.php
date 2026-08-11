<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan SKPI') }} — Review &amp; Submit
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('student.pengajuan._steps', ['current' => 'review'])

            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-medium text-gray-700">Status</h3>
                    <x-status-badge :status="$pengajuan->status" />
                </div>

                @if ($pengajuan->status === \App\Enums\PengajuanStatus::Revisi && $pengajuan->catatan_revisi)
                    <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                        <strong>Catatan revisi:</strong> {{ $pengajuan->catatan_revisi }}
                    </div>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Data Pribadi &amp; Akademik</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div><dt class="text-gray-500">Nama</dt><dd class="font-medium">{{ $pengajuan->mahasiswa->nama_lengkap }}</dd></div>
                    <div><dt class="text-gray-500">NIM</dt><dd class="font-medium">{{ $pengajuan->mahasiswa->nim }}</dd></div>
                    <div><dt class="text-gray-500">Program Studi</dt><dd class="font-medium">{{ $pengajuan->mahasiswa->programStudi->nama_prodi }}</dd></div>
                    <div><dt class="text-gray-500">IPK</dt><dd class="font-medium">{{ $pengajuan->ipk ?? '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-gray-500">Judul Skripsi</dt><dd class="font-medium">{{ $pengajuan->judul_skripsi ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Tanggal Lulus</dt><dd class="font-medium">{{ optional($pengajuan->tanggal_lulus)->format('d M Y') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Predikat</dt><dd class="font-medium">{{ $pengajuan->predikat_kelulusan?->getLabel() ?? '—' }}</dd></div>
                </dl>
                <a href="{{ route('pengajuan.akademik', $pengajuan) }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-3 inline-block">Ubah data akademik</a>
            </div>

            @php
                $categorySummaries = [
                    ['label' => 'Prestasi', 'items' => $pengajuan->prestasis, 'nameField' => 'nama_prestasi', 'slug' => 'prestasi'],
                    ['label' => 'Organisasi', 'items' => $pengajuan->organisasis, 'nameField' => 'nama_organisasi', 'slug' => 'organisasi'],
                    ['label' => 'Sertifikasi', 'items' => $pengajuan->sertifikasis, 'nameField' => 'nama_sertifikat', 'slug' => 'sertifikasi'],
                    ['label' => 'Pelatihan / Seminar', 'items' => $pengajuan->pelatihanSeminars, 'nameField' => 'nama_kegiatan', 'slug' => 'pelatihan-seminar'],
                    ['label' => 'Magang / PKL', 'items' => $pengajuan->magangPkls, 'nameField' => 'nama_instansi', 'slug' => 'magang-pkl'],
                    ['label' => 'Kompetensi / Aktivitas', 'items' => $pengajuan->kompetensiAktivitas, 'nameField' => 'nama_kegiatan', 'slug' => 'kompetensi-aktivitas'],
                ];
            @endphp

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Ringkasan Data Pendukung</h3>
                <div class="divide-y divide-gray-100">
                    @foreach ($categorySummaries as $summary)
                        <div class="py-3 flex justify-between items-start">
                            <div>
                                <p class="font-medium text-sm">{{ $summary['label'] }} ({{ $summary['items']->count() }})</p>
                                @if ($summary['items']->isNotEmpty())
                                    <ul class="text-xs text-gray-500 mt-1 list-disc list-inside">
                                        @foreach ($summary['items'] as $item)
                                            <li>{{ $item->{$summary['nameField']} }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <a href="{{ route('pengajuan.kategori', [$pengajuan, $summary['slug']]) }}" class="text-xs text-indigo-600 hover:text-indigo-800 shrink-0 ml-4">Ubah</a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Riwayat Status</h3>
                @if ($pengajuan->statusHistories->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada riwayat.</p>
                @else
                    <ul class="text-sm space-y-2">
                        @foreach ($pengajuan->statusHistories as $history)
                            <li class="flex justify-between text-gray-600">
                                <span>{{ $history->status_to->getLabel() }} @if ($history->changedBy) oleh {{ $history->changedBy->name }} @endif</span>
                                <span class="text-gray-400">{{ $history->created_at->format('d M Y H:i') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($editable)
                <form method="post" action="{{ route('pengajuan.submit', $pengajuan) }}" onsubmit="return confirm('Kirim pengajuan SKPI ini untuk diverifikasi?');">
                    @csrf
                    <x-primary-button>Submit Pengajuan</x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
