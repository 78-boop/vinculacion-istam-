<x-app-layout>
    <style>
        .av-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
        .av-card { display: flex; flex-direction: column; background: #fff; border-radius: 22px; overflow: hidden; border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra); transition: transform .22s, box-shadow .22s; }
        .av-card:hover { transform: translateY(-5px); box-shadow: var(--ui-sombra-hover); }
        .av-portada { position: relative; display: block; aspect-ratio: 16 / 10; overflow: hidden; background: linear-gradient(135deg, var(--ui-verde), var(--ui-lima)); }
        .av-portada img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
        .av-card:hover .av-portada img { transform: scale(1.05); }
        .av-portada .ui-tag { position: absolute; left: 12px; bottom: 12px; background: #fff; }
        .av-cuerpo { padding: 16px 18px 18px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
        .av-cuerpo h3 { margin: 0; font-size: 16px; font-weight: 800; color: var(--ui-texto); }
        .av-pie { display: flex; gap: 8px; margin-top: auto; }
        .av-pie > * { flex: 1; }
    </style>

    <div class="ui-wrap">
        <x-ui.hero etiqueta="Catálogo por carrera" titulo="Actividades de vinculación"
                   subtitulo="Elige una carrera para ver o agregar las actividades disponibles para sus estudiantes.">
            <x-slot:acciones>
                <a href="{{ route('docente.postulaciones-actividad.index') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m10-12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/></svg>
                    Postulaciones
                </a>
                <a href="{{ route('docente.actividades-vinculacion.exportar') }}" class="ui-btn ui-btn-vidrio">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14"/></svg>
                    Descargar Excel
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        @if ($carreras->isEmpty())
            <div class="ui-panel"><div class="ui-vacio"><strong>No hay carreras activas</strong>El administrador debe registrar carreras primero.</div></div>
        @else
            <div class="av-grid">
                @foreach ($carreras as $carrera)
                    <article class="av-card">
                        <a href="{{ route('docente.actividades-vinculacion.show', $carrera->id) }}" class="av-portada" aria-label="Ver {{ $carrera->nombre }}">
                            @if ($carrera->imagen)
                                <img src="{{ asset($carrera->imagen) }}" alt="" data-sin-zoom loading="lazy">
                            @endif
                            <span class="ui-tag verde">{{ $carrera->actividades_vinculacion_count }} {{ $carrera->actividades_vinculacion_count === 1 ? 'actividad' : 'actividades' }}</span>
                        </a>
                        <div class="av-cuerpo">
                            <h3>{{ $carrera->nombre }}</h3>
                            <div class="av-pie">
                                <a href="{{ route('docente.actividades-vinculacion.show', $carrera->id) }}" class="ui-btn ui-btn-primario ui-btn-sm">Ver / agregar</a>
                                <a href="{{ route('docente.actividades-vinculacion.exportar-carrera', $carrera->id) }}" class="ui-btn ui-btn-suave ui-btn-sm">Excel</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
