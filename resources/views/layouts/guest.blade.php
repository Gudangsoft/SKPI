<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php $setting = \App\Models\Setting::current(); @endphp

        <title>{{ $setting->app_name }}</title>

        @if ($setting->faviconUrl())
            <link rel="icon" href="{{ $setting->faviconUrl() }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-6 py-12">
            <a href="{{ url('/') }}" class="mb-6 flex items-center gap-2.5">
                @if ($setting->logoUrl())
                    <img src="{{ $setting->logoUrl() }}" alt="{{ $setting->app_name }}" class="h-14 w-14 rounded-xl object-cover" style="height:56px;width:56px;object-fit:cover;border-radius:0.75rem;">
                @else
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-teal-600 text-white">
                        <x-heroicon-o-academic-cap class="h-7 w-7" />
                    </span>
                @endif
                <span class="text-lg font-semibold tracking-tight text-slate-900">{{ $setting->app_name }}</span>
            </a>

            <div class="w-full max-w-md overflow-hidden rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
                {{ $slot }}
            </div>

            <a href="{{ url('/') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-slate-400 transition hover:text-slate-600">
                <x-heroicon-o-arrow-left class="h-3.5 w-3.5" />
                Kembali ke beranda
            </a>
        </div>
    </body>
</html>
