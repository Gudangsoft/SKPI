@php
    $setting = \App\Models\Setting::current();
    $footerMenu = \App\Models\Menu::forSlug('landing-footer');

    $footerStyle = $setting->footer_bg_type === 'image' && $setting->footerBgImageUrl()
        ? "background-image:url('{$setting->footerBgImageUrl()}');background-size:cover;background-position:center;"
        : 'background-color:'.$setting->footer_bg_color.';';
    $footerStyle .= 'color:'.$setting->footer_text_color.';';
@endphp

<footer style="{{ $footerStyle }}">
    @if ($setting->footer_accent_color)
        <div style="height:4px;background-color:{{ $setting->footer_accent_color }};"></div>
    @endif

    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid grid-cols-1 gap-8 pb-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @if ($setting->hasContactInfo() || $setting->hasSocialLinks())
                <div>
                    <p class="text-sm font-semibold" style="color:{{ $setting->footer_text_color }};opacity:1;">Informasi</p>

                    <div class="mt-3 space-y-2 text-sm opacity-80">
                        @if ($setting->contact_address)
                            <p class="leading-relaxed">{{ $setting->contact_address }}</p>
                        @endif
                        @if ($setting->contact_phone)
                            <p>{{ $setting->contact_phone }}</p>
                        @endif
                        @if ($setting->contact_email)
                            <p>{{ $setting->contact_email }}</p>
                        @endif
                    </div>

                    @if ($setting->hasSocialLinks())
                        <div class="mt-4 flex items-center gap-2">
                            @foreach ([
                                'facebook' => $setting->social_facebook_url,
                                'instagram' => $setting->social_instagram_url,
                                'twitter' => $setting->social_twitter_url,
                                'youtube' => $setting->social_youtube_url,
                            ] as $platform => $url)
                                @if ($url)
                                    <a
                                        href="{{ $url }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="flex h-8 w-8 items-center justify-center rounded-full border transition hover:opacity-75"
                                        style="border-color:{{ $setting->footer_text_color }};color:{{ $setting->footer_text_color }};"
                                    >
                                        <x-social-icon :platform="$platform" />
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($footerMenu && $footerMenu->rootItems->isNotEmpty())
                @foreach ($footerMenu->rootItems as $item)
                    <div>
                        @if ($item->children->isNotEmpty())
                            <p class="text-sm font-semibold">{{ $item->label }}</p>
                            <ul class="mt-3 space-y-2">
                                @foreach ($item->children as $child)
                                    <li>
                                        <a href="{{ $child->resolvedUrl() }}"
                                            @if ($child->target_blank) target="_blank" rel="noopener" @endif
                                            class="text-sm opacity-70 transition hover:opacity-100">
                                            {{ $child->label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $item->resolvedUrl() }}"
                                @if ($item->target_blank) target="_blank" rel="noopener" @endif
                                class="text-sm font-semibold transition hover:opacity-80">
                                {{ $item->label }}
                            </a>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <div class="flex flex-col items-center justify-between gap-3 border-t pt-6 text-sm opacity-60 sm:flex-row" style="border-color:{{ $setting->footer_text_color }};">
            <span>&copy; {{ now()->year }} {{ $setting->tagline }}. Semua hak dilindungi.</span>
            <span>Sistem Informasi {{ $setting->app_name }}</span>
        </div>
    </div>
</footer>
