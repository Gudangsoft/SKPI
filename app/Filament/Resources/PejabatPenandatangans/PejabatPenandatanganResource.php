<?php

namespace App\Filament\Resources\PejabatPenandatangans;

use App\Filament\Resources\PejabatPenandatangans\Pages\ManagePejabatPenandatangans;
use App\Models\Fakultas;
use App\Models\PejabatPenandatangan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PejabatPenandatanganResource extends Resource
{
    protected static ?string $model = PejabatPenandatangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Pejabat Penandatangan';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(255),
                Select::make('fakultas_id')
                    ->label('Fakultas')
                    ->options(fn () => Fakultas::query()->orderBy('nama_fakultas')->pluck('nama_fakultas', 'id'))
                    ->searchable()
                    ->helperText('Kosongkan jika berlaku untuk semua fakultas (mis. Rektor).'),
                FileUpload::make('tanda_tangan_path')
                    ->label('Tanda Tangan')
                    ->image()
                    ->disk('public')
                    ->directory('signatures')
                    ->visibility('public')
                    ->maxSize(512)
                    ->helperText('Format PNG latar transparan disarankan, ukuran sekitar 400×200px. Maksimal 512 KB.'),
                Toggle::make('aktif')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                TextColumn::make('jabatan')->label('Jabatan')->searchable()->sortable(),
                TextColumn::make('fakultas.nama_fakultas')->label('Fakultas')->placeholder('Semua Fakultas')->sortable(),
                ToggleColumn::make('aktif')->label('Aktif'),
            ])
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
            'index' => ManagePejabatPenandatangans::route('/'),
        ];
    }
}
