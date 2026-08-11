<?php

namespace App\Models;

use App\Enums\PengajuanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanStatusHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'pengajuan_skpi_id',
        'status_from',
        'status_to',
        'changed_by',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'status_from' => PengajuanStatus::class,
            'status_to' => PengajuanStatus::class,
        ];
    }

    public function pengajuanSkpi(): BelongsTo
    {
        return $this->belongsTo(PengajuanSkpi::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
