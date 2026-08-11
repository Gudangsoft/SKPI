<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan SKPI') }} — {{ \App\Support\PengajuanKategori::forSlug($kategori)['label'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('student.pengajuan._steps', ['current' => $kategori])

            <div class="bg-white shadow sm:rounded-lg p-6">
                @livewire('pengajuan.repeatable-category-manager', ['pengajuan' => $pengajuan, 'kategori' => $kategori], key($kategori))
            </div>
        </div>
    </div>
</x-app-layout>
