<?php

namespace App\Filament\Resources\PengajuanSkpis;

use App\Enums\PengajuanStatus;
use App\Filament\Resources\PengajuanSkpis\Pages\ListPengajuanSkpis;
use App\Filament\Resources\PengajuanSkpis\Pages\ViewPengajuanSkpi;
use App\Filament\Resources\PengajuanSkpis\Schemas\PengajuanSkpiInfolist;
use App\Filament\Resources\PengajuanSkpis\Tables\PengajuanSkpisTable;
use App\Models\PejabatPenandatangan;
use App\Models\PengajuanSkpi;
use App\Services\NomorSkpiGenerator;
use App\Support\Roles;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder as QrCodeBuilder;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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

    public static function terbitkanNomorAction(): Action
    {
        return Action::make('terbitkanNomor')
            ->label('Terbitkan Nomor')
            ->color('success')
            ->icon(Heroicon::OutlinedHashtag)
            ->schema(fn (PengajuanSkpi $record) => [
                Select::make('pejabat_penandatangan_id')
                    ->label('Pejabat Penandatangan')
                    ->options(fn () => PejabatPenandatangan::query()
                        ->where('aktif', true)
                        ->where(fn (Builder $q) => $q
                            ->whereNull('fakultas_id')
                            ->orWhere('fakultas_id', $record->mahasiswa->programStudi->fakultas_id))
                        ->get()
                        ->mapWithKeys(fn (PejabatPenandatangan $p) => [$p->id => "{$p->nama} — {$p->jabatan}"]))
                    ->required()
                    ->native(false),
            ])
            ->visible(fn (PengajuanSkpi $record): bool => static::canTerbitkanNomor($record))
            ->action(fn (PengajuanSkpi $record, array $data) => $record->update([
                'nomor_skpi' => app(NomorSkpiGenerator::class)->generate($record),
                'pejabat_penandatangan_id' => $data['pejabat_penandatangan_id'],
                'nomor_skpi_generated_at' => now(),
                'status' => PengajuanStatus::NomorTerbit,
            ]));
    }

    public static function terbitkanPdfAction(): Action
    {
        return Action::make('terbitkanPdf')
            ->label('Terbitkan PDF')
            ->color('success')
            ->icon(Heroicon::OutlinedDocumentArrowDown)
            ->requiresConfirmation()
            ->modalDescription('Dokumen PDF resmi akan dibuat dan pengajuan berstatus Terbit.')
            ->visible(fn (PengajuanSkpi $record): bool => static::canTerbitkanPdf($record))
            ->action(function (PengajuanSkpi $record) {
                $record->load([
                    'mahasiswa.programStudi.fakultas',
                    'prestasis',
                    'organisasis',
                    'sertifikasis',
                    'pelatihanSeminars',
                    'magangPkls',
                    'kompetensiAktivitas',
                    'pejabatPenandatangan',
                ]);

                $qrCode = (new QrCodeBuilder)->build(
                    data: route('verification.show', $record->verification_token),
                    size: 180,
                    margin: 4,
                )->getDataUri();

                $filename = "skpi-pdf/{$record->id}.pdf";

                Pdf::loadView('pdf.skpi', ['pengajuan' => $record, 'qrCode' => $qrCode])
                    ->save($filename, 'public');

                $record->update([
                    'pdf_path' => $filename,
                    'status' => PengajuanStatus::Published,
                ]);
            });
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

    protected static function canTerbitkanNomor(PengajuanSkpi $record): bool
    {
        return $record->status === PengajuanStatus::DisetujuiFakultas
            && (Auth::user()?->hasAnyRole([Roles::KAPRODI, Roles::SUPER_ADMIN]) ?? false);
    }

    protected static function canTerbitkanPdf(PengajuanSkpi $record): bool
    {
        return $record->status === PengajuanStatus::NomorTerbit
            && (Auth::user()?->hasAnyRole([Roles::KAPRODI, Roles::SUPER_ADMIN]) ?? false);
    }
}
