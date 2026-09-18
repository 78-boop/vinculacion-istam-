@props(['href' => null, 'variant' => 'primary', 'type' => 'button', 'size' => 'md'])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];

    $variants = [
        'primary'   => 'bg-[--eva-green] hover:bg-[--eva-green-dark] text-white shadow-sm focus:ring-[--eva-green]',
        'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 focus:ring-gray-300',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white shadow-sm focus:ring-red-500',
        'outline'   => 'bg-transparent hover:bg-green-50 text-[--eva-green-dark] border border-[--eva-green] focus:ring-[--eva-green]',
        'ghost'     => 'bg-transparent hover:bg-gray-100 text-gray-600 focus:ring-gray-300',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif