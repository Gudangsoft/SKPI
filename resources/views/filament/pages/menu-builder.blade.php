<x-filament-panels::page>
    <div class="skpi-menu-tabs">
        @foreach ($this->menus() as $menu)
            <a
                href="{{ static::getUrl(['menu' => $menu->id]) }}"
                class="skpi-menu-tab {{ $menu->id === $this->menuId ? 'skpi-menu-tab-active' : '' }}"
            >
                {{ $menu->name }}
            </a>
        @endforeach
    </div>

    <div class="skpi-menu-builder-grid">
        <div class="skpi-panel">
            <h3 class="skpi-panel-title">Tambah Item Menu</h3>

            <form wire:submit="addItem" class="skpi-add-form">
                {{ $this->form }}

                <x-filament::button type="submit" class="skpi-add-submit">
                    Tambah ke Menu
                </x-filament::button>
            </form>
        </div>

        <div class="skpi-panel">
            <h3 class="skpi-panel-title">Struktur Menu</h3>
            <p class="skpi-panel-hint">Seret untuk mengurutkan atau memindahkan ke dalam item lain (maks. 3 tingkat).</p>

            <div class="skpi-tree-wrapper" wire:ignore x-data x-init="initMenuTree($el, $wire)">
                @include('filament.pages.menu-builder.tree', ['nodes' => $this->getTree(), 'depth' => 1])
            </div>
        </div>
    </div>

    <style>
        .skpi-menu-tabs { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .skpi-menu-tab {
            border-radius: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500;
            background: #fff; color: #475569; box-shadow: inset 0 0 0 1px #e2e8f0; text-decoration: none;
        }
        .skpi-menu-tab:hover { background: #f8fafc; }
        .skpi-menu-tab-active { background: #0d9488; color: #fff; box-shadow: none; }

        .skpi-menu-builder-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-top: 1.5rem; }
        @media (min-width: 1024px) {
            .skpi-menu-builder-grid { grid-template-columns: 1fr 2fr; }
        }

        .skpi-panel { border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #fff; padding: 1rem; }
        .skpi-panel-title { font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0; }
        .skpi-panel-hint { font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0; }
        .skpi-add-form { margin-top: 1rem; }
        .skpi-add-submit { margin-top: 1rem; }
        .skpi-tree-wrapper { margin-top: 1rem; }

        .menu-tree-list {
            list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.375rem;
            min-height: 2.25rem; border-radius: 0.5rem;
        }
        .menu-tree-list[data-depth="1"] { min-height: 0; }
        .menu-tree-list:has(> li[data-placeholder]) {
            border: 1px dashed #cbd5e1;
        }
        .menu-tree-item {
            border-radius: 0.5rem; border: 1px solid #e2e8f0; background: #f8fafc;
        }
        .menu-tree-item-row { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; }
        .menu-tree-placeholder { padding: 0.5rem 0.75rem; font-size: 0.75rem; color: #cbd5e1; pointer-events: none; }
        .drag-handle { cursor: move; color: #cbd5e1; flex-shrink: 0; }
        .drag-handle:hover { color: #64748b; }
        .drag-handle svg, .menu-tree-delete svg { width: 1rem; height: 1rem; }
        .menu-tree-type-badge {
            border-radius: 0.25rem; background: #e2e8f0; padding: 0.125rem 0.375rem;
            font-size: 0.625rem; font-weight: 600; text-transform: uppercase; color: #64748b; flex-shrink: 0;
        }
        .menu-tree-label-input {
            flex: 1; border: 0; background: transparent; padding: 0; font-size: 0.875rem; color: #334155;
        }
        .menu-tree-label-input:focus { outline: none; box-shadow: none; }
        .menu-tree-delete { color: #fca5a5; flex-shrink: 0; background: none; border: 0; cursor: pointer; padding: 0; }
        .menu-tree-delete:hover { color: #dc2626; }
        .menu-tree-children { padding: 0 0.5rem 0.5rem 2rem; }
        .fi-sortable-ghost { opacity: 0.4; }
    </style>

    <script>
        function initMenuTree(root, wire) {
            const lists = root.querySelectorAll('.menu-tree-list');

            const serialize = (ul) => {
                if (! ul) {
                    return [];
                }

                return Array.from(ul.children)
                    .filter((li) => li.matches('li') && ! li.hasAttribute('data-placeholder'))
                    .map((li) => {
                        const childList = li.querySelector(':scope > .menu-tree-children > .menu-tree-list');

                        return {
                            id: parseInt(li.dataset.id, 10),
                            children: childList ? serialize(childList) : [],
                        };
                    });
            };

            const onEnd = () => {
                const rootList = root.querySelector(':scope > .menu-tree-list[data-depth="1"]');
                const tree = serialize(rootList);

                // Always reload after a drop: the tree region is wire:ignore'd (so
                // SortableJS's own DOM moves never fight with Livewire's morph), which
                // means nothing here re-renders reactively — a fresh load is what
                // clears empty-list placeholders and re-syncs depth-3 cutoffs.
                wire.updateTree(tree).then(() => window.location.reload());
            };

            lists.forEach((list) => {
                new Sortable(list, {
                    group: 'menu-tree',
                    animation: 150,
                    handle: '.drag-handle',
                    forceFallback: true,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd,
                });
            });
        }
    </script>
</x-filament-panels::page>
