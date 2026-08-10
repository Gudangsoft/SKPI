<?php

namespace App\Filament\Resources\Mahasiswas;

use App\Filament\Resources\Mahasiswas\Pages\CreateMahasiswa;
use App\Filament\Resources\Mahasiswas\Pages\EditMahasiswa;
use App\Filament\Resources\Mahasiswas\Pages\ListMahasiswas;
use App\Filament\Resources\Mahasiswas\Schemas\MahasiswaForm;
use App\Filament\Resources\Mahasiswas\Tables\MahasiswasTable;
use App\Models\Mahasiswa;
use App\Support\Roles;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MahasiswaResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user && ! $user->hasRole(Roles::SUPER_ADMIN)) {
            $query->where('program_studi_id', $user->program_studi_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return MahasiswaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MahasiswasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMahasiswas::route('/'),
            'create' => CreateMahasiswa::route('/create'),
            'edit' => EditMahasiswa::route('/{record}/edit'),
        ];
    }
}
