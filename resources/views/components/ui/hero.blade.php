{{-- Cabecera verde de página. Uso: <x-ui.hero etiqueta="..." titulo="..." subtitulo="..."> ... <x-slot:acciones>...</x-slot:acciones> </x-ui.hero> --}}
@props(['titulo', 'subtitulo' => null, 'etiqueta' => null, 'volver' => null, 'volverTexto' => 'Volver'])

<section {{ $attributes->merge(['class' => 'ui-hero', 'style' => 'margin-bottom: 22px;']) }}>
    <div style="flex: 1 1 380px; min-width: 0;">
        @if ($volver)
            <a href="{{ $volver }}" class="ui-hero-eyebrow" style="text-decoration: none; color: var(--ui-lima);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
                {{ $volverTexto }}
            </a>
        @elseif ($etiqueta)
            <span class="ui-hero-eyebrow">{{ $etiqueta }}</span>
        @endif
        <h1>{{ $titulo }}</h1>
        @if ($subtitulo)
            <p>{{ $subtitulo }}</p>
        @endif
        {{ $slot }}
    </div>
    @isset($acciones)
        <div class="ui-hero-acciones">{{ $acciones }}</div>
    @endisset
</section>
