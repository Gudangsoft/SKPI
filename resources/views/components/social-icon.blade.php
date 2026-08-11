@props(['platform'])

@switch($platform)
    @case('facebook')
        <svg {{ $attributes->merge(['class' => 'h-4 w-4', 'viewBox' => '0 0 24 24', 'fill' => 'currentColor']) }}>
            <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.9h-2.34V22c4.78-.8 8.44-4.95 8.44-9.94Z" />
        </svg>
        @break

    @case('instagram')
        <svg {{ $attributes->merge(['class' => 'h-4 w-4', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8']) }}>
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none" />
        </svg>
        @break

    @case('twitter')
        <svg {{ $attributes->merge(['class' => 'h-4 w-4', 'viewBox' => '0 0 24 24', 'fill' => 'currentColor']) }}>
            <path d="M18.9 3h3.68l-8.04 9.19L24 21h-7.41l-5.8-7.59L4.16 21H.47l8.6-9.83L0 3h7.59l5.24 6.93L18.9 3Zm-1.29 16.17h2.04L6.49 5.06H4.3l13.3 14.11Z" />
        </svg>
        @break

    @case('youtube')
        <svg {{ $attributes->merge(['class' => 'h-4 w-4', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linejoin' => 'round', 'stroke-linecap' => 'round']) }}>
            <rect x="2" y="6" width="20" height="12" rx="4" />
            <path d="M10.5 9.5v5l4.5-2.5-4.5-2.5Z" fill="currentColor" stroke="none" />
        </svg>
        @break
@endswitch
