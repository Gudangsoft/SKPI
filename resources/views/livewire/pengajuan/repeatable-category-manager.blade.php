<div>
    @if (session('status') === 'kategori-saved')
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
            Data {{ $label }} berhasil disimpan.
        </div>
    @endif

    @if (! $editable)
        <div class="mb-4 p-3 bg-yellow-50 text-yellow-800 rounded-lg text-sm">
            Pengajuan ini sudah tidak dapat diubah pada status saat ini.
        </div>
    @endif

    @if (empty($items))
        <p class="text-sm text-gray-500 mb-4">Belum ada data {{ $label }}. Klik "Tambah Data" untuk menambahkan.</p>
    @endif

    <div class="space-y-4">
        @foreach ($items as $index => $item)
            <div wire:key="item-{{ $index }}" class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <h4 class="text-sm font-medium text-gray-700">{{ $label }} #{{ $index + 1 }}</h4>
                    @if ($editable)
                        <button type="button" wire:click="removeItem({{ $index }})" wire:confirm="Hapus data ini?" class="text-xs text-red-600 hover:text-red-800">
                            Hapus
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($fields as $field)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                {{ $field['label'] }}@if ($field['required']) <span class="text-red-500">*</span>@endif
                            </label>

                            @if ($field['type'] === 'select')
                                <select wire:model="items.{{ $index }}.{{ $field['name'] }}" @disabled(! $editable) class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($field['options']::cases() as $case)
                                        <option value="{{ $case->value }}">{{ $case->getLabel() }}</option>
                                    @endforeach
                                </select>
                            @elseif ($field['type'] === 'textarea')
                                <textarea wire:model="items.{{ $index }}.{{ $field['name'] }}" @disabled(! $editable) rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                            @else
                                <input
                                    type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}"
                                    wire:model="items.{{ $index }}.{{ $field['name'] }}"
                                    @disabled(! $editable)
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                />
                            @endif

                            @error("items.{$index}.{$field['name']}")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Dokumen Bukti</label>

                        @if ($item['dokumen_bukti_existing'])
                            <p class="text-xs text-gray-500 mt-1">File sudah tersimpan. Unggah file baru untuk menggantinya.</p>
                        @endif

                        @if ($editable)
                            <input type="file" wire:model="items.{{ $index }}.dokumen_bukti" class="mt-1 block w-full text-sm" />
                            <div wire:loading wire:target="items.{{ $index }}.dokumen_bukti" class="text-xs text-gray-500 mt-1">Mengunggah...</div>
                        @endif

                        @error("items.{$index}.dokumen_bukti")
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($editable)
        <div class="mt-4 flex items-center gap-3">
            <button type="button" wire:click="addItem" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                + Tambah Data
            </button>

            <button type="button" wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Simpan {{ $label }}
            </button>
        </div>
    @endif
</div>
