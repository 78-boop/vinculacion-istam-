@props(['message' => 'No hay registros todavía.', 'icon' => '📭'])

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <span class="text-4xl mb-3">{{ $icon }}</span>
    <p class="text-gray-500 text-sm">{{ $message }}</p>
</div>