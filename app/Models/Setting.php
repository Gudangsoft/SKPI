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
        'contact_address',
        'contact_phone',
        'contact_email',
        'social_facebook_url',
        'social_instagram_url',
        'social_twitter_url',
        'social_youtube_url',
        'footer_bg_type',
        'footer_bg_color',
        'footer_bg_image_path',
        'footer_text_color',
        'footer_accent_color',
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

    public function footerBgImageUrl(): ?string
    {
        return $this->footer_bg_image_path ? Storage::disk('public')->url($this->footer_bg_image_path) : null;
    }

    public function hasContactInfo(): bool
    {
        return filled($this->contact_address) || filled($this->contact_phone) || filled($this->contact_email);
    }

    public function hasSocialLinks(): bool
    {
        return filled($this->social_facebook_url)
            || filled($this->social_instagram_url)
            || filled($this->social_twitter_url)
            || filled($this->social_youtube_url);
    }
}
