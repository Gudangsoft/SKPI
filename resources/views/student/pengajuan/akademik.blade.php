<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan SKPI') }} — Data Pribadi &amp; Akademik
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('student.pengajuan._steps', ['current' => 'akademik'])

            @if (session('status') === 'akademik-updated')
                <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">Data akademik tersimpan.</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Data Pribadi (dari Data Mahasiswa)</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div><dt class="text-gray-500">Nama</dt><dd class="font-medium">{{ $mahasiswa->nama_lengkap }}</dd></div>
                    <div><dt class="text-gray-500">NIM</dt><dd class="font-medium">{{ $mahasiswa->nim }}</dd></div>
                    <div><dt class="text-gray-500">Program Studi</dt><dd class="font-medium">{{ $mahasiswa->programStudi->nama_prodi }}</dd></div>
                    <div><dt class="text-gray-500">Fakultas</dt><dd class="font-medium">{{ $mahasiswa->programStudi->fakultas->nama_fakultas }}</dd></div>
                </dl>
                <p class="text-xs text-gray-400 mt-3">
                    Untuk melengkapi/memperbarui data pribadi, kunjungi halaman <a href="{{ route('mahasiswa.profil.edit') }}" class="underline">Data Mahasiswa</a>.
                </p>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Data Akademik</h3>

                <form method="post" action="{{ route('pengajuan.akademik.update', $pengajuan) }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="ipk" value="IPK" />
                        <x-text-input id="ipk" name="ipk" type="number" step="0.01" min="0" max="4" class="mt-1 block w-full" :value="old('ipk', $pengajuan->ipk)" :disabled="! $editable" />
                        <x-input-error class="mt-2" :messages="$errors->get('ipk')" />
                    </div>

                    <div>
                        <x-input-label for="judul_skripsi" value="Judul Skripsi/Tugas Akhir" />
                        <x-text-input id="judul_skripsi" name="judul_skripsi" type="text" class="mt-1 block w-full" :value="old('judul_skripsi', $pengajuan->judul_skripsi)" :disabled="! $editable" />
                        <x-input-error class="mt-2" :messages="$errors->get('judul_skripsi')" />
                    </div>

                    <div>
                        <x-input-label for="tanggal_lulus" value="Tanggal Lulus" />
                        <x-text-input id="tanggal_lulus" name="tanggal_lulus" type="date" class="mt-1 block w-full" :value="old('tanggal_lulus', optional($pengajuan->tanggal_lulus)->format('Y-m-d'))" :disabled="! $editable" />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal_lulus')" />
                    </div>

                    <div>
                        <x-input-label for="no_ijazah" value="No. Ijazah (opsional)" />
                        <x-text-input id="no_ijazah" name="no_ijazah" type="text" class="mt-1 block w-full" :value="old('no_ijazah', $pengajuan->no_ijazah)" :disabled="! $editable" />
                        <x-input-error class="mt-2" :messages="$errors->get('no_ijazah')" />
                    </div>

                    <div>
                        <x-input-label for="predikat_kelulusan" value="Predikat Kelulusan" />
                        <select id="predikat_kelulusan" name="predikat_kelulusan" @disabled(! $editable) class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Pilih --</option>
                            @foreach ($predikatOptions as $option)
                                <option value="{{ $option->value }}" @selected(old('predikat_kelulusan', $pengajuan->predikat_kelulusan?->value) === $option->value)>{{ $option->getLabel() }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('predikat_kelulusan')" />
                    </div>

                    @if ($editable)
                        <div class="flex items-center gap-4">
                            <x-primary-button>Simpan &amp; Lanjut</x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
