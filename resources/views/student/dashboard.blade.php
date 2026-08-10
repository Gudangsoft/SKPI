<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Selamat datang, {{ Auth::user()->name }}</h3>

                    @if ($mahasiswa)
                        <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500">NIM</dt>
                                <dd class="font-medium">{{ $mahasiswa->nim }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Program Studi</dt>
                                <dd class="font-medium">{{ $mahasiswa->programStudi->nama_prodi }} ({{ $mahasiswa->programStudi->jenjang->value }})</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Fakultas</dt>
                                <dd class="font-medium">{{ $mahasiswa->programStudi->fakultas->nama_fakultas }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Angkatan</dt>
                                <dd class="font-medium">{{ $mahasiswa->angkatan }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-4 text-sm text-red-600">
                            Data mahasiswa Anda belum tersedia. Silakan hubungi admin program studi.
                        </p>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Pengajuan SKPI</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Fitur pengajuan Surat Keterangan Pendamping Ijazah akan segera tersedia di sini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
