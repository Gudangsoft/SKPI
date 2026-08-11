<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static \UnitEnum|string|null $navigationGroup = 'Konten';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Halaman';

    protected static ?string $pluralModelLabel = 'Halaman';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, string $operation) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText(fn ($state) => $state ? url('/halaman/'.$state) : null),
                FileUpload::make('featured_image_path')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk('public')
                    ->directory('pages')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->helperText('Tampil di atas judul halaman. Disarankan lanskap lebar minimal 1200×630px. Maksimal 2 MB.')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Konten')
                    ->columnSpanFull(),
                Textarea::make('meta_description')
                    ->label('Meta Deskripsi')
                    ->maxLength(255)
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')
                    ->label('Terbit Pada')
                    ->helperText('Kosongkan untuk simpan sebagai draf.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image_path')->label('Gambar')->disk('public'),
                TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable(),
                TextColumn::make('published_at')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Page $record) => $record->isPublished() ? 'Terbit' : 'Draf')
                    ->color(fn (Page $record) => $record->isPublished() ? 'success' : 'gray'),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
