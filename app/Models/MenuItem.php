<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'type',
        'page_id',
        'url',
        'route_name',
        'target_blank',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_blank' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function resolvedUrl(): string
    {
        return match ($this->type) {
            'page' => $this->page ? route('pages.show', $this->page->slug) : '#',
            'url' => $this->url ?? '#',
            'route' => ($this->route_name && Route::has($this->route_name)) ? route($this->route_name) : '#',
            default => '#',
        };
    }
}
