@props(['result', 'searched' => true])

@if ($result)
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
        <div class="flex items-center gap-2 text-emerald-700">
            <x-heroicon-o-check-badge class="h-6 w-6" />
            <span class="font-semibold">SKPI ini sah dan terverifikasi</span>
        </div>

        <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <dt class="text-xs text-slate-500">Nomor SKPI</dt>
                <dd class="font-medium text-slate-900">{{ $result->nomor_skpi }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500">Tanggal Terbit</dt>
                <dd class="font-medium text-slate-900">{{ optional($result->published_at)->translatedFormat('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500">Nama Mahasiswa</dt>
                <dd class="font-medium text-slate-900">{{ $result->mahasiswa->nama_lengkap }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500">NIM</dt>
                <dd class="font-medium text-slate-900">{{ $result->mahasiswa->nim }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-slate-500">Program Studi</dt>
                <dd class="font-medium text-slate-900">{{ $result->mahasiswa->programStudi->nama_prodi }}</dd>
            </div>
        </dl>

        @if ($result->pdf_path)
            <a
                href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($result->pdf_path) }}"
                target="_blank"
                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500"
            >
                <x-heroicon-o-document-arrow-down class="h-4 w-4" />
                Lihat Dokumen SKPI
            </a>
        @endif
    </div>
@elseif ($searched)
    <div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center">
        <x-heroicon-o-x-circle class="mx-auto h-8 w-8 text-red-500" />
        <p class="mt-2 font-semibold text-red-700">Dokumen tidak ditemukan atau belum terbit</p>
        <p class="mt-1 text-sm text-red-600">Periksa kembali nomor SKPI atau kode verifikasi yang Anda masukkan.</p>
    </div>
@endif
