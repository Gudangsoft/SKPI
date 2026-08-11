<?php

namespace App\Filament\Resources\PengajuanSkpis;

use App\Enums\PengajuanStatus;
use App\Filament\Resources\PengajuanSkpis\Pages\ListPengajuanSkpis;
use App\Filament\Resources\PengajuanSkpis\Pages\ViewPengajuanSkpi;
use App\Filament\Resources\PengajuanSkpis\Schemas\PengajuanSkpiInfolist;
use App\Filament\Resources\PengajuanSkpis\Tables\PengajuanSkpisTable;
use App\Models\PengajuanSkpi;
use App\Support\Roles;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajuanSkpiResource extends Resource
{
    protected static ?string $model = PengajuanSkpi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Verifikasi Pengajuan';

    protected static ?string $navigationLabel = 'Pengajuan SKPI';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('status', '!=', PengajuanStatus::Draft);

        $user = Auth::user();

        if ($user && ! $user->hasRole(Roles::SUPER_ADMIN)) {
            $query->whereRelation('mahasiswa', 'program_studi_id', $user->program_studi_id);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return PengajuanSkpisTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PengajuanSkpiInfolist::configure($schema);
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
            'index' => ListPengajuanSkpis::route('/'),
            'view' => ViewPengajuanSkpi::route('/{record}'),
        ];
    }

    public static function verifikasiAction(): Action
    {
        return Action::make('verifikasi')
            ->label('Verifikasi')
            ->color('success')
            ->icon(Heroicon::OutlinedCheck)
            ->requiresConfirmation()
            ->visible(fn (PengajuanSkpi $record): bool => static::canVerifikasiProdi($record))
            ->action(fn (PengajuanSkpi $record) => $record->update([
                'status' => PengajuanStatus::DisetujuiProdi,
            ]));
    }

    public static function setujuiAction(): Action
    {
        return Action::make('setujui')
            ->label('Setujui')
            ->color('success')
            ->icon(Heroicon::OutlinedCheckBadge)
            ->requiresConfirmation()
            ->visible(fn (PengajuanSkpi $record): bool => static::canSetujuiFakultas($record))
            ->action(fn (PengajuanSkpi $record) => $record->update([
                'status' => PengajuanStatus::DisetujuiFakultas,
            ]));
    }

    public static function mintaRevisiAction(): Action
    {
        return Action::make('mintaRevisi')
            ->label('Minta Revisi')
            ->color('warning')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->schema([
                Textarea::make('catatan_revisi')
                    ->label('Catatan Revisi')
                    ->helperText('Catatan ini akan ditampilkan ke mahasiswa.')
                    ->required()
                    ->rows(4),
            ])
            ->visible(fn (PengajuanSkpi $record): bool => static::canMintaRevisi($record))
            ->action(fn (PengajuanSkpi $record, array $data) => $record->update([
                'status' => PengajuanStatus::Revisi,
                'catatan_revisi' => $data['catatan_revisi'],
            ]));
    }

    protected static function canVerifikasiProdi(PengajuanSkpi $record): bool
    {
        return $record->status === PengajuanStatus::Diajukan
            && (Auth::user()?->hasAnyRole([Roles::ADMIN_PRODI, Roles::SUPER_ADMIN]) ?? false);
    }

    protected static function canSetujuiFakultas(PengajuanSkpi $record): bool
    {
        return $record->status === PengajuanStatus::DisetujuiProdi
            && (Auth::user()?->hasAnyRole([Roles::KAPRODI, Roles::SUPER_ADMIN]) ?? false);
    }

    protected static function canMintaRevisi(PengajuanSkpi $record): bool
    {
        return static::canVerifikasiProdi($record) || static::canSetujuiFakultas($record);
    }
}
