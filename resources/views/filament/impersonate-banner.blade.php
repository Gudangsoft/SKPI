@php
    $impersonator = \App\Models\User::find(session('impersonator_id'));
@endphp

<div class="flex w-full items-center justify-center gap-2 bg-amber-500 px-4 py-2 text-center text-sm font-medium text-white">
    <span>
        Anda login sebagai <strong>{{ auth()->user()?->name }}</strong>
        @if ($impersonator)
            (disamarkan oleh {{ $impersonator->name }})
        @endif
    </span>
    <a href="{{ route('impersonate.stop') }}" class="font-semibold underline hover:text-amber-100">
        Kembali ke akun saya
    </a>
</div>
