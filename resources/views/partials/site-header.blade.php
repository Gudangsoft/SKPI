@php
    $setting = \App\Models\Setting::current();
    $navMenu = \App\Models\Menu::forSlug('landing-navbar');
    $user = auth()->user();
    $dashboardUrl = null;

    if ($user) {
        $dashboardUrl = $user->hasRole(\App\Support\Roles::MAHASISWA)
            ? route('dashboard')
            : '/admin';
    }
@endphp

@if ($setting->hasContactInfo() || $setting->hasSocialLinks())
    <div class="hidden border-b border-slate-100 bg-slate-50 sm:block">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-1.5 text-xs text-slate-500">
            <div class="flex items-center gap-4">
                @if ($setting->contact_phone)
                    <span class="inline-flex items-center gap-1.5">
                        <x-heroicon-o-phone class="h-3.5 w-3.5" />
                        {{ $setting->contact_phone }}
                    </span>
                @endif
                @if ($setting->contact_email)
                    <span class="inline-flex items-center gap-1.5">
                        <x-heroicon-o-envelope class="h-3.5 w-3.5" />
                        {{ $setting->contact_email }}
                    </span>
                @endif
            </div>

            @if ($setting->hasSocialLinks())
                <div class="flex items-center gap-3">
                    @foreach ([
                        'facebook' => $setting->social_facebook_url,
                        'instagram' => $setting->social_instagram_url,
                        'twitter' => $setting->social_twitter_url,
                        'youtube' => $setting->social_youtube_url,
                    ] as $platform => $url)
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="text-slate-400 transition hover:text-slate-700">
                                <x-social-icon :platform="$platform" class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

<header class="sticky top-0 z-30 border-b border-slate-100 bg-white/80 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            @if ($setting->logoUrl())
                <img src="{{ $setting->logoUrl() }}" alt="{{ $setting->app_name }}" class="h-12 w-12 rounded-xl object-cover" style="height:48px;width:48px;object-fit:cover;border-radius:0.75rem;">
            @else
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <x-heroicon-o-academic-cap class="h-6 w-6" />
                </span>
            @endif
            <span class="text-lg font-semibold tracking-tight text-slate-900">{{ $setting->app_name }}</span>
            <span class="hidden text-sm text-slate-400 sm:inline">{{ $setting->tagline }}</span>
        </a>

        <nav class="flex items-center gap-6">
            @if ($navMenu)
                <ul class="hidden items-center gap-6 md:flex">
                    @foreach ($navMenu->rootItems as $item)
                        <li class="group relative">
                            <a href="{{ $item->resolvedUrl() }}"
                                @if ($item->target_blank) target="_blank" rel="noopener" @endif
                                class="inline-flex items-center gap-1 text-sm font-medium text-slate-600 transition hover:text-slate-900">
                                {{ $item->label }}
                                @if ($item->children->isNotEmpty())
                                    <x-heroicon-o-chevron-down class="h-3.5 w-3.5" />
                                @endif
                            </a>

                            @if ($item->children->isNotEmpty())
                                <ul class="invisible absolute left-0 top-full z-40 mt-2 w-56 rounded-xl border border-slate-100 bg-white py-2 opacity-0 shadow-lg transition group-hover:visible group-hover:opacity-100">
                                    @foreach ($item->children as $child)
                                        <li class="group/child relative">
                                            <a href="{{ $child->resolvedUrl() }}"
                                                @if ($child->target_blank) target="_blank" rel="noopener" @endif
                                                class="flex items-center justify-between px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                                {{ $child->label }}
                                                @if ($child->children->isNotEmpty())
                                                    <x-heroicon-o-chevron-right class="h-3.5 w-3.5" />
                                                @endif
                                            </a>

                                            @if ($child->children->isNotEmpty())
                                                <ul class="invisible absolute left-full top-0 z-40 w-56 rounded-xl border border-slate-100 bg-white py-2 opacity-0 shadow-lg transition group-hover/child:visible group-hover/child:opacity-100">
                                                    @foreach ($child->children as $grandchild)
                                                        <li>
                                                            <a href="{{ $grandchild->resolvedUrl() }}"
                                                                @if ($grandchild->target_blank) target="_blank" rel="noopener" @endif
                                                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                                                {{ $grandchild->label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="flex items-center gap-3">
                @if ($dashboardUrl)
                    <a href="{{ $dashboardUrl }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500">
                        Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">
                        Masuk
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500">
                        Ajukan SKPI
                    </a>
                @endif
            </div>
        </nav>
    </div>
</header>
