<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $setting = \App\Models\Setting::current(); @endphp

        <title>Verifikasi SKPI — {{ $setting->app_name }}</title>

        @if ($setting->faviconUrl())
            <link rel="icon" href="{{ $setting->faviconUrl() }}">
        @endif

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-white text-slate-800">
        @include('partials.site-header')

        <div class="mx-auto max-w-2xl px-6 py-16">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Verifikasi Keaslian SKPI</h1>
            <p class="mt-2 text-slate-500">Hasil pemindaian kode verifikasi dokumen.</p>

            <div class="mt-8">
                <x-verification-result :result="$result" />
            </div>

            <p class="mt-6 text-sm text-slate-400">
                Ingin memeriksa dokumen lain?
                <a href="{{ route('verification.index') }}" class="font-medium text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Cari di sini</a>.
            </p>
        </div>

        @include('partials.site-footer')
    </body>
</html>
