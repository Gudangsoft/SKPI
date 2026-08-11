<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'tagline',
        'logo_path',
        'favicon_path',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'app_name' => 'SKPI',
            'tagline' => 'Kampus Kasih Bangsa',
        ]);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function faviconUrl(): ?string
    {
        return $this->favicon_path ? Storage::disk('public')->url($this->favicon_path) : null;
    }
}
