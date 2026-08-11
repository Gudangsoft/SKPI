<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan SKPI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'pengajuan-submitted')
                <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                    Pengajuan berhasil dikirim dan menunggu verifikasi admin program studi.
                </div>
            @endif

            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Riwayat pengajuan Surat Keterangan Pendamping Ijazah Anda.</p>

                @if ($canCreate)
                    <form method="post" action="{{ route('pengajuan.store') }}">
                        @csrf
                        <x-primary-button>Ajukan SKPI Baru</x-primary-button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                @if ($pengajuans->isEmpty())
                    <div class="p-6 text-sm text-gray-500">Belum ada pengajuan SKPI.</div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Tanggal Dibuat</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Nomor SKPI</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="px-4 py-3">{{ $pengajuan->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $pengajuan->nomor_skpi ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$pengajuan->status" />
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-800">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
