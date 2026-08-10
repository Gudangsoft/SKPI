<?php

namespace App\Filament\Resources\ProgramStudis;

use App\Enums\Jenjang;
use App\Filament\Resources\ProgramStudis\Pages\ManageProgramStudis;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProgramStudiResource extends Resource
{
    protected static ?string $model = ProgramStudi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'nama_prodi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('fakultas_id')
                    ->label('Fakultas')
                    ->options(fn () => Fakultas::query()->orderBy('nama_fakultas')->pluck('nama_fakultas', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('kode_prodi')
                    ->label('Kode Prodi')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_prodi')
                    ->label('Nama Program Studi')
                    ->required()
                    ->maxLength(255),
                Select::make('jenjang')
                    ->options(collect(Jenjang::cases())->mapWithKeys(fn (Jenjang $j) => [$j->value => $j->label()]))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_prodi')->label('Kode')->searchable()->sortable(),
                TextColumn::make('nama_prodi')->label('Program Studi')->searchable()->sortable(),
                TextColumn::make('jenjang')->badge(),
                TextColumn::make('fakultas.nama_fakultas')->label('Fakultas')->searchable()->sortable(),
                TextColumn::make('mahasiswas_count')->label('Jumlah Mahasiswa')->counts('mahasiswas'),
            ])
            ->filters([
                SelectFilter::make('fakultas_id')
                    ->label('Fakultas')
                    ->options(fn () => Fakultas::query()->orderBy('nama_fakultas')->pluck('nama_fakultas', 'id')),
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
            'index' => ManageProgramStudis::route('/'),
        ];
    }
}
