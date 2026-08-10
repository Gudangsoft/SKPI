<?php

namespace App\Filament\Resources\Fakultas;

use App\Filament\Resources\Fakultas\Pages\ManageFakultas;
use App\Models\Fakultas;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FakultasResource extends Resource
{
    protected static ?string $model = Fakultas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'nama_fakultas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_fakultas')
                    ->label('Kode Fakultas')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_fakultas')
                    ->label('Nama Fakultas')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_fakultas')->label('Kode')->searchable()->sortable(),
                TextColumn::make('nama_fakultas')->label('Nama Fakultas')->searchable()->sortable(),
                TextColumn::make('program_studis_count')->label('Jumlah Prodi')->counts('programStudis'),
            ])
            ->filters([
                //
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
            'index' => ManageFakultas::route('/'),
        ];
    }
}
