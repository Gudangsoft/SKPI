<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php $setting = \App\Models\Setting::current(); @endphp

        <title>{{ $page->title }} — {{ $setting->app_name }}</title>

        @if ($page->meta_description)
            <meta name="description" content="{{ $page->meta_description }}">
        @endif

        @if ($setting->faviconUrl())
            <link rel="icon" href="{{ $setting->faviconUrl() }}">
        @endif

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-white text-slate-800">
        @include('partials.site-header')

        <article class="mx-auto max-w-3xl px-6 py-16">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $page->title }}</h1>

            <div class="prose prose-slate mt-8 max-w-none">
                {!! $page->content !!}
            </div>
        </article>

        @include('partials.site-footer')
    </body>
</html>
