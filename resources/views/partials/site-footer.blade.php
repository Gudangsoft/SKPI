@php
    $setting = \App\Models\Setting::current();
    $footerMenu = \App\Models\Menu::forSlug('landing-footer');
@endphp

<footer class="border-t border-slate-100">
    <div class="mx-auto max-w-6xl px-6 py-10">
        @if ($footerMenu && $footerMenu->rootItems->isNotEmpty())
            <div class="grid grid-cols-2 gap-6 pb-8 sm:grid-cols-3 md:grid-cols-4">
                @foreach ($footerMenu->rootItems as $item)
                    <div>
                        @if ($item->children->isNotEmpty())
                            <p class="text-sm font-semibold text-slate-900">{{ $item->label }}</p>
                            <ul class="mt-3 space-y-2">
                                @foreach ($item->children as $child)
                                    <li>
                                        <a href="{{ $child->resolvedUrl() }}"
                                            @if ($child->target_blank) target="_blank" rel="noopener" @endif
                                            class="text-sm text-slate-500 transition hover:text-slate-800">
                                            {{ $child->label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $item->resolvedUrl() }}"
                                @if ($item->target_blank) target="_blank" rel="noopener" @endif
                                class="text-sm font-semibold text-slate-900 transition hover:text-slate-600">
                                {{ $item->label }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 pt-6 text-sm text-slate-400 sm:flex-row">
            <span>&copy; {{ now()->year }} {{ $setting->tagline }}. Semua hak dilindungi.</span>
            <span>Sistem Informasi {{ $setting->app_name }}</span>
        </div>
    </div>
</footer>
