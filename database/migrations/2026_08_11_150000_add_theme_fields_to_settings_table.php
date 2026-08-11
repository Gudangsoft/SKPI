<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('social_facebook_url')->nullable();
            $table->string('social_instagram_url')->nullable();
            $table->string('social_twitter_url')->nullable();
            $table->string('social_youtube_url')->nullable();

            $table->string('footer_bg_type')->default('color');
            $table->string('footer_bg_color')->default('#1f2937');
            $table->string('footer_bg_image_path')->nullable();
            $table->string('footer_text_color')->default('#cbd5e1');
            $table->string('footer_accent_color')->nullable()->default('#2563eb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
