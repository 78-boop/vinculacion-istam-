@props(['active'])
@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-bold text-black border-b-2 border-black transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold text-[#1b5e20] hover:bg-white hover:text-[#006B47] transition duration-150 ease-in-out';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>