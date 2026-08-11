@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\PengajuanStatus::Draft => 'bg-gray-100 text-gray-800',
        \App\Enums\PengajuanStatus::Diajukan, \App\Enums\PengajuanStatus::DisetujuiProdi => 'bg-yellow-100 text-yellow-800',
        \App\Enums\PengajuanStatus::Revisi => 'bg-red-100 text-red-800',
        \App\Enums\PengajuanStatus::DisetujuiFakultas, \App\Enums\PengajuanStatus::NomorTerbit => 'bg-blue-100 text-blue-800',
        \App\Enums\PengajuanStatus::Published => 'bg-green-100 text-green-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex px-2 py-1 rounded-full text-xs font-medium {$classes}"]) }}>
    {{ $status->getLabel() }}
</span>
