<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Roles;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
            'contact_address',
            'contact_phone',
            'contact_email',
            'social_facebook_url',
            'social_instagram_url',
            'social_twitter_url',
            'social_youtube_url',
            'footer_bg_type',
            'footer_bg_color',
            'footer_bg_image_path',
            'footer_text_color',
            'footer_accent_color',
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
                            ->imageEditor()
                            ->maxSize(1024)
                            ->helperText('Format PNG/JPG, disarankan persegi minimal 512×512px. Maksimal 1 MB.'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(512)
                            ->helperText('Format PNG/ICO, disarankan persegi 512×512px. Maksimal 512 KB.'),
                    ]),

                Section::make('Kontak & Media Sosial')
                    ->description('Tampil pada bilah atas dan kolom "Informasi" di footer halaman utama.')
                    ->collapsible()
                    ->columns(2)
                    ->components([
                        Textarea::make('contact_address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('contact_phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('social_facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/…'),
                        TextInput::make('social_instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/…'),
                        TextInput::make('social_twitter_url')
                            ->label('Twitter / X')
                            ->url()
                            ->placeholder('https://x.com/…'),
                        TextInput::make('social_youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/…'),
                    ]),

                Section::make('Footer')
                    ->description('Atur tampilan latar footer halaman utama.')
                    ->collapsible()
                    ->components([
                        Radio::make('footer_bg_type')
                            ->label('Tipe Latar')
                            ->options([
                                'color' => 'Warna Polos',
                                'image' => 'Gambar',
                            ])
                            ->inline()
                            ->live()
                            ->default('color')
                            ->required(),
                        FileUpload::make('footer_bg_image_path')
                            ->label('Gambar Latar')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Format JPG/PNG, disarankan lanskap lebar minimal 1920×600px. Maksimal 2 MB.')
                            ->visible(fn (Get $get) => $get('footer_bg_type') === 'image'),
                        ColorPicker::make('footer_bg_color')
                            ->label('Warna Latar')
                            ->hex()
                            ->visible(fn (Get $get) => $get('footer_bg_type') === 'color'),
                        ColorPicker::make('footer_text_color')
                            ->label('Warna Teks')
                            ->hex(),
                        ColorPicker::make('footer_accent_color')
                            ->label('Warna Aksen (garis atas)')
                            ->hex(),
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
