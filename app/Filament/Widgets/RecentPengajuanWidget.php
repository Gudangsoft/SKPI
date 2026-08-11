<?php

namespace App\Filament\Widgets;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanSkpi;
use App\Support\Roles;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentPengajuanWidget extends TableWidget
{
    protected static ?string $heading = 'Pengajuan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                TextColumn::make('mahasiswa.nama_lengkap')->label('Nama'),
                TextColumn::make('mahasiswa.nim')->label('NIM'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated(false);
    }

    protected function getQuery(): Builder
    {
        $user = Auth::user();

        $query = PengajuanSkpi::query()
            ->where('status', '!=', PengajuanStatus::Draft)
            ->latest('updated_at')
            ->limit(8);

        if (! ($user?->hasRole(Roles::SUPER_ADMIN) ?? false)) {
            $query->whereRelation('mahasiswa', 'program_studi_id', $user->program_studi_id);
        }

        return $query;
    }
}
