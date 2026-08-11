<?php

namespace App\Filament\Resources\HeroSlides;

use App\Filament\Resources\HeroSlides\Pages\ManageHeroSlides;
use App\Models\HeroSlide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static \UnitEnum|string|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Slide Beranda';

    protected static ?string $modelLabel = 'Slide Beranda';

    protected static ?string $pluralModelLabel = 'Slide Beranda';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('hero-slides')
                    ->visibility('public')
                    ->imageEditor()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->label('Judul')
                    ->maxLength(255),
                Textarea::make('subtitle')
                    ->label('Subjudul')
                    ->maxLength(500)
                    ->columnSpanFull(),
                TextInput::make('button_label')
                    ->label('Label Tombol')
                    ->maxLength(255),
                TextInput::make('button_url')
                    ->label('URL Tombol')
                    ->url()
                    ->placeholder('https://…'),
                Toggle::make('active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')->label('Gambar')->disk('public'),
                TextColumn::make('title')->label('Judul')->placeholder('—')->searchable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                ToggleColumn::make('active')->label('Aktif'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHeroSlides::route('/'),
        ];
    }
}
