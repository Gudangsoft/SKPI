<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function rootItems(): HasMany
    {
        return $this->items()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with([
                'children' => fn ($query) => $query->orderBy('sort_order'),
                'children.children' => fn ($query) => $query->orderBy('sort_order'),
                'page',
                'children.page',
                'children.children.page',
            ]);
    }

    public static function forSlug(string $slug): ?self
    {
        return static::query()
            ->where('slug', $slug)
            ->first();
    }
}
