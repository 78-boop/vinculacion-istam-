@props(['status' => null, 'color' => null])

@php
    $map = [
        'activo' => 'green', 'aprobada' => 'green', 'aprobado' => 'green', 'sí' => 'green', 'si' => 'green', 'completado' => 'green',
        'pendiente' => 'yellow', 'en revisión' => 'yellow', 'en proceso' => 'yellow',
        'rechazada' => 'red', 'rechazado' => 'red', 'no' => 'red', 'inactivo' => 'red', 'cancelado' => 'red',
    ];

    $key = strtolower(trim((string) $status));
    $resolvedColor = $color ?? ($map[$key] ?? 'gray');

    $styles = [
        'green'  => 'bg-green-100 text-green-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'red'    => 'bg-red-100 text-red-800',
        'blue'   => 'bg-blue-100 text-blue-800',
        'gray'   => 'bg-gray-100 text-gray-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap ' . ($styles[$resolvedColor] ?? $styles['gray'])]) }}>
    {{ $slot->isEmpty() ? $status : $slot }}
</span>