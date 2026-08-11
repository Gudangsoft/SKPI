<?php

namespace App\Filament\Pages;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page as PageModel;
use App\Support\Roles;
use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuBuilder extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static \UnitEnum|string|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Menu Navigasi';

    protected static ?string $title = 'Menu Navigasi';

    protected string $view = 'filament.pages.menu-builder';

    public ?int $menuId = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole(Roles::SUPER_ADMIN) ?? false;
    }

    public function mount(): void
    {
        $requested = (int) request()->query('menu', 0);
        $menu = $requested ? Menu::find($requested) : null;

        $this->menuId = $menu?->id ?? Menu::query()->orderBy('id')->value('id');

        $this->form->fill(['type' => 'page', 'target_blank' => false]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('type')
                    ->label('Tipe')
                    ->options([
                        'page' => 'Halaman',
                        'url' => 'URL Kustom',
                        'route' => 'Menu Aplikasi',
                    ])
                    ->inline()
                    ->live()
                    ->default('page')
                    ->required(),
                Select::make('page_id')
                    ->label('Halaman')
                    ->options(fn () => $this->pages()->pluck('title', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'page')
                    ->required(fn (Get $get) => $get('type') === 'page'),
                TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->placeholder('https://…')
                    ->visible(fn (Get $get) => $get('type') === 'url')
                    ->required(fn (Get $get) => $get('type') === 'url'),
                Select::make('route_name')
                    ->label('Rute Aplikasi')
                    ->options($this->getRouteOptions())
                    ->visible(fn (Get $get) => $get('type') === 'route')
                    ->required(fn (Get $get) => $get('type') === 'route'),
                TextInput::make('label')
                    ->label('Label')
                    ->placeholder('Nama tampilan menu')
                    ->required()
                    ->maxLength(255),
                Toggle::make('target_blank')
                    ->label('Buka di Tab Baru')
                    ->helperText('Aktifkan agar tautan terbuka di tab baru, bukan menggantikan halaman saat ini.'),
            ])
            ->statePath('data');
    }

    public function menus(): Collection
    {
        return Menu::query()->orderBy('id')->get();
    }

    public function pages(): Collection
    {
        return PageModel::query()->orderBy('title')->get(['id', 'title']);
    }

    public function getRouteOptions(): array
    {
        return [
            'dashboard' => 'Dashboard Mahasiswa',
            'mahasiswa.profil.edit' => 'Data Mahasiswa',
            'pengajuan.index' => 'Pengajuan SKPI',
            'login' => 'Masuk (Mahasiswa)',
        ];
    }

    public function getTree(): array
    {
        if (! $this->menuId) {
            return [];
        }

        $menu = Menu::find($this->menuId);

        if (! $menu) {
            return [];
        }

        return $this->mapItems($menu->rootItems);
    }

    protected function mapItems(Collection $items): array
    {
        return $items->map(fn (MenuItem $item) => [
            'id' => $item->id,
            'label' => $item->label,
            'type' => $item->type,
            'target_blank' => $item->target_blank,
            'children' => $this->mapItems($item->children),
        ])->values()->all();
    }

    public function addItem(): void
    {
        $data = $this->form->getState();

        if (! $this->menuId) {
            return;
        }

        $maxOrder = MenuItem::where('menu_id', $this->menuId)->whereNull('parent_id')->max('sort_order') ?? 0;

        MenuItem::create([
            'menu_id' => $this->menuId,
            'parent_id' => null,
            'label' => $data['label'],
            'type' => $data['type'],
            'page_id' => $data['type'] === 'page' ? $data['page_id'] : null,
            'url' => $data['type'] === 'url' ? $data['url'] : null,
            'route_name' => $data['type'] === 'route' ? $data['route_name'] : null,
            'target_blank' => $data['target_blank'] ?? false,
            'sort_order' => $maxOrder + 1,
        ]);

        Notification::make()->title('Item menu ditambahkan')->success()->send();

        $this->redirect(static::getUrl(['menu' => $this->menuId]));
    }

    public function deleteItem(int $id): void
    {
        MenuItem::where('id', $id)->where('menu_id', $this->menuId)->delete();

        Notification::make()->title('Item menu dihapus')->success()->send();

        $this->redirect(static::getUrl(['menu' => $this->menuId]));
    }

    public function updateLabel(int $id, string $label): void
    {
        if (blank($label)) {
            return;
        }

        MenuItem::where('id', $id)->where('menu_id', $this->menuId)->update(['label' => $label]);
    }

    public function toggleTargetBlank(int $id): void
    {
        $item = MenuItem::where('id', $id)->where('menu_id', $this->menuId)->first();

        if (! $item) {
            return;
        }

        $item->update(['target_blank' => ! $item->target_blank]);

        $this->redirect(static::getUrl(['menu' => $this->menuId]));
    }

    /**
     * @param  array<int, array{id: int, children?: array}>  $tree
     */
    public function updateTree(array $tree): bool
    {
        if (! $this->menuId) {
            return false;
        }

        if (! $this->isValidDepth($tree, 1)) {
            Notification::make()->title('Menu maksimal 3 tingkat')->danger()->send();

            return false;
        }

        DB::transaction(function () use ($tree) {
            $this->persistTree($tree, null);
        });

        return true;
    }

    protected function isValidDepth(array $nodes, int $depth): bool
    {
        if ($depth > 3) {
            return false;
        }

        foreach ($nodes as $node) {
            if (! empty($node['children']) && ! $this->isValidDepth($node['children'], $depth + 1)) {
                return false;
            }
        }

        return true;
    }

    protected function persistTree(array $nodes, ?int $parentId): void
    {
        foreach ($nodes as $index => $node) {
            MenuItem::where('id', $node['id'])
                ->where('menu_id', $this->menuId)
                ->update([
                    'parent_id' => $parentId,
                    'sort_order' => $index,
                ]);

            if (! empty($node['children'])) {
                $this->persistTree($node['children'], (int) $node['id']);
            }
        }
    }
}
