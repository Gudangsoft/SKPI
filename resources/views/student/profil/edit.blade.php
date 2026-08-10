<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'profil-updated')
                <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                    Data mahasiswa berhasil diperbarui.
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h3 class="text-lg font-medium text-gray-900">Data Akademik</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Data akademik dikelola oleh admin program studi. Hubungi admin prodi jika ada perubahan.
                        </p>
                    </header>

                    <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">NIM</dt>
                            <dd class="font-medium">{{ $mahasiswa->nim }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Nama Lengkap</dt>
                            <dd class="font-medium">{{ $mahasiswa->nama_lengkap }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Program Studi</dt>
                            <dd class="font-medium">{{ $mahasiswa->programStudi->nama_prodi }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Angkatan</dt>
                            <dd class="font-medium">{{ $mahasiswa->angkatan }}</dd>
                        </div>
                    </dl>
                </section>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h3 class="text-lg font-medium text-gray-900">Biodata</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Lengkapi data pribadi Anda. Data ini akan digunakan pada dokumen SKPI.
                        </p>
                    </header>

                    <form method="post" action="{{ route('mahasiswa.profil.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <div>
                            <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" class="mt-1 block w-full" :value="old('tempat_lahir', $mahasiswa->tempat_lahir)" />
                            <x-input-error class="mt-2" :messages="$errors->get('tempat_lahir')" />
                        </div>

                        <div>
                            <x-input-label for="tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="tanggal_lahir" name="tanggal_lahir" type="date" class="mt-1 block w-full" :value="old('tanggal_lahir', optional($mahasiswa->tanggal_lahir)->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('tanggal_lahir')" />
                        </div>

                        <div>
                            <x-input-label for="jenis_kelamin" value="Jenis Kelamin" />
                            <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih --</option>
                                <option value="L" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin?->value) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin', $mahasiswa->jenis_kelamin?->value) === 'P')>Perempuan</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
                        </div>

                        <div>
                            <x-input-label for="no_hp" value="No. HP" />
                            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp', $mahasiswa->no_hp)" />
                            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
                        </div>

                        <div>
                            <x-input-label for="alamat" value="Alamat" />
                            <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                        </div>

                        <div>
                            <x-input-label for="foto" value="Foto" />
                            @if ($mahasiswa->foto_path)
                                <p class="text-xs text-gray-500 mt-1">Foto saat ini sudah tersimpan. Unggah file baru untuk menggantinya.</p>
                            @endif
                            <input id="foto" name="foto" type="file" accept="image/*" class="mt-1 block w-full text-sm" />
                            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
