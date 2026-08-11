<?php

namespace App\Livewire\Pengajuan;

use App\Models\PengajuanSkpi;
use App\Support\PengajuanKategori;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class RepeatableCategoryManager extends Component
{
    use WithFileUploads;

    public PengajuanSkpi $pengajuan;

    public string $kategori;

    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    /** @var array<int, int> */
    public array $deletedIds = [];

    public function mount(PengajuanSkpi $pengajuan, string $kategori): void
    {
        $this->authorize('view', $pengajuan);

        $this->pengajuan = $pengajuan;
        $this->kategori = $kategori;

        $this->loadItems();
    }

    protected function config(): array
    {
        return PengajuanKategori::forSlug($this->kategori);
    }

    protected function modelClass(): string
    {
        return $this->config()['model'];
    }

    protected function fieldNames(): array
    {
        return array_column($this->config()['fields'], 'name');
    }

    protected function loadItems(): void
    {
        $modelClass = $this->modelClass();

        $rows = $modelClass::where('pengajuan_skpi_id', $this->pengajuan->id)->orderBy('id')->get();

        $this->items = $rows->map(function ($row) {
            $data = ['id' => $row->id];

            foreach ($this->config()['fields'] as $field) {
                $value = $row->{$field['name']};

                if ($value instanceof \BackedEnum) {
                    $value = $value->value;
                } elseif ($value instanceof Carbon) {
                    $value = $value->format('Y-m-d');
                }

                $data[$field['name']] = $value;
            }

            $data['dokumen_bukti_existing'] = $row->dokumen_bukti_path;
            $data['dokumen_bukti'] = null;

            return $data;
        })->values()->toArray();
    }

    public function addItem(): void
    {
        $item = ['id' => null];

        foreach ($this->config()['fields'] as $field) {
            $item[$field['name']] = null;
        }

        $item['dokumen_bukti_existing'] = null;
        $item['dokumen_bukti'] = null;

        $this->items[] = $item;
    }

    public function removeItem(int $index): void
    {
        if (! empty($this->items[$index]['id'])) {
            $this->deletedIds[] = $this->items[$index]['id'];
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    protected function rules(): array
    {
        $rules = [];

        foreach ($this->items as $i => $item) {
            foreach ($this->config()['fields'] as $field) {
                $rule = [$field['required'] ? 'required' : 'nullable'];

                match ($field['type']) {
                    'number' => $rule[] = 'numeric',
                    'date' => $rule[] = 'date',
                    'select' => $rule[] = Rule::in(array_column($field['options']::cases(), 'value')),
                    default => null,
                };

                $rules["items.{$i}.{$field['name']}"] = $rule;
            }

            $rules["items.{$i}.dokumen_bukti"] = ['nullable', 'file', 'max:2048'];
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->items as $i => $item) {
            foreach ($this->config()['fields'] as $field) {
                $attributes["items.{$i}.{$field['name']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function save(): void
    {
        $this->authorize('update', $this->pengajuan);

        // An empty $this->items is a valid state (the student removed every row in
        // this category). Livewire's validate() treats a fully empty rules array as
        // a developer mistake and throws, so only validate when there's something to.
        if (! empty($this->items)) {
            $this->validate();
        }

        $modelClass = $this->modelClass();
        $fieldNames = $this->fieldNames();

        foreach ($this->items as $item) {
            $payload = array_intersect_key($item, array_flip($fieldNames));
            $payload['pengajuan_skpi_id'] = $this->pengajuan->id;

            if (! empty($item['dokumen_bukti'])) {
                $payload['dokumen_bukti_path'] = $item['dokumen_bukti']->store('pengajuan/'.$this->kategori, 'local');
            }

            if (! empty($item['id'])) {
                $modelClass::where('id', $item['id'])
                    ->where('pengajuan_skpi_id', $this->pengajuan->id)
                    ->update($payload);
            } else {
                $modelClass::create($payload);
            }
        }

        if ($this->deletedIds) {
            $modelClass::where('pengajuan_skpi_id', $this->pengajuan->id)
                ->whereIn('id', $this->deletedIds)
                ->delete();

            $this->deletedIds = [];
        }

        $this->loadItems();

        session()->flash('status', 'kategori-saved');
        $this->dispatch('kategori-saved');
    }

    public function render()
    {
        return view('livewire.pengajuan.repeatable-category-manager', [
            'label' => $this->config()['label'],
            'fields' => $this->config()['fields'],
            'editable' => $this->pengajuan->isEditableByMahasiswa(),
        ]);
    }
}
