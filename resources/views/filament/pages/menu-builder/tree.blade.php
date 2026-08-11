@props(['nodes', 'depth'])

<ul class="menu-tree-list" data-depth="{{ $depth }}">
    @if (empty($nodes))
        <li data-placeholder="true" class="menu-tree-placeholder">Seret item ke sini untuk menjadikannya sub-menu</li>
    @endif

    @foreach ($nodes as $node)
        <li data-id="{{ $node['id'] }}" class="menu-tree-item">
            <div class="menu-tree-item-row">
                <span class="drag-handle">
                    <x-heroicon-o-bars-3 />
                </span>

                <span class="menu-tree-type-badge">
                    {{ match ($node['type']) { 'page' => 'Halaman', 'url' => 'URL', 'route' => 'Aplikasi', default => $node['type'] } }}
                </span>

                <input
                    type="text"
                    value="{{ $node['label'] }}"
                    wire:blur="updateLabel({{ $node['id'] }}, $event.target.value)"
                    class="menu-tree-label-input"
                >

                <button
                    type="button"
                    wire:click="toggleTargetBlank({{ $node['id'] }})"
                    title="{{ $node['target_blank'] ? 'Terbuka di tab baru — klik untuk buka di tab yang sama' : 'Terbuka di tab yang sama — klik untuk buka di tab baru' }}"
                    class="menu-tree-target-blank {{ $node['target_blank'] ? 'menu-tree-target-blank-active' : '' }}"
                >
                    <x-heroicon-o-arrow-top-right-on-square />
                </button>

                <button
                    type="button"
                    wire:click="deleteItem({{ $node['id'] }})"
                    wire:confirm="Hapus item ini beserta sub-menunya?"
                    class="menu-tree-delete"
                >
                    <x-heroicon-o-trash />
                </button>
            </div>

            @if ($depth < 3)
                <div class="menu-tree-children">
                    @include('filament.pages.menu-builder.tree', ['nodes' => $node['children'], 'depth' => $depth + 1])
                </div>
            @endif
        </li>
    @endforeach
</ul>
