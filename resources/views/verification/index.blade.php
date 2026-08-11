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
            <p class="mt-2 text-slate-500">Masukkan nomor SKPI atau kode verifikasi yang tertera pada dokumen.</p>

            <form method="GET" action="{{ route('verification.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Contoh: 0001/SKPI/TI/VIII/2026"
                    class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                    Verifikasi
                </button>
            </form>

            <div class="mt-8">
                <x-verification-result :result="$result" :searched="$searched" />
            </div>
        </div>

        @include('partials.site-footer')
    </body>
</html>
