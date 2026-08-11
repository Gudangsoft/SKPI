<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Roles;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Brand & Logo';

    protected static ?string $title = 'Brand & Logo';

    protected string $view = 'filament.pages.settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole(Roles::SUPER_ADMIN) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(Setting::current()->only([
            'app_name',
            'tagline',
            'logo_path',
            'favicon_path',
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Aplikasi')
                    ->description('Nama dan tagline ini tampil di panel admin, halaman utama, dan halaman masuk.')
                    ->components([
                        TextInput::make('app_name')
                            ->label('Nama Aplikasi')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tagline')
                            ->label('Tagline / Nama Kampus')
                            ->maxLength(255),
                    ]),

                Section::make('Logo & Favicon')
                    ->description('Kosongkan untuk memakai ikon bawaan.')
                    ->components([
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->imageEditor(),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Setting::current()->update($this->form->getState());

        Notification::make()
            ->title('Pengaturan brand berhasil disimpan')
            ->success()
            ->send();
    }
}
