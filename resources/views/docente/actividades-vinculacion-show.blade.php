<x-app-layout>
    <style>
        .avs-grid { display: grid; grid-template-columns: minmax(280px, 1fr) minmax(0, 1.5fr); gap: 20px; align-items: start; }
        .avs-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 22px; }
        .avs-item + .avs-item { border-top: 1px solid #EEF2F0; }
        .avs-item.inactiva { opacity: .55; }
        .avs-num { width: 36px; height: 36px; border-radius: 11px; flex-shrink: 0; display: grid; place-items: center; font-weight: 800; background: var(--ui-menta); color: var(--ui-verde); }
        .avs-info { flex: 1; min-width: 0; }
        .avs-info strong { display: block; font-size: 14.5px; color: var(--ui-texto); }
        .avs-info p { margin: 4px 0 0; font-size: 13.5px; color: var(--ui-texto-2); }
        .avs-info small { display: block; margin-top: 6px; font-size: 12px; color: var(--ui-texto-3); }
        @media (max-width: 900px) { .avs-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="ui-wrap" style="max-width: 1100px;">
        <x-ui.hero :volver="route('docente.actividades-vinculacion.index')" volver-texto="Carreras" :titulo="$carrera->nombre"
                   :subtitulo="$actividades->count() . ' ' . ($actividades->count() === 1 ? 'actividad' : 'actividades') . ' en el catálogo de esta carrera'" />

        <section class="avs-grid">
            <form action="{{ route('docente.actividades-vinculacion.store') }}" method="POST" class="ui-panel"
                  data-confirm-title="¿Agregar esta actividad al catálogo?" data-confirm-text="Los estudiantes de {{ $carrera->nombre }} podrán verla." data-confirm-button="Sí, agregar">
                @csrf
                <input type="hidden" name="carrera_id" value="{{ $carrera->id }}">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg></span> Nueva actividad</h3>
                </div>
                <div class="ui-panel-body" style="display:flex;flex-direction:column;gap:16px">
                    <div class="ui-campo">
                        <label for="titulo">Título de la actividad</label>
                        <input id="titulo" type="text" name="titulo" value="{{ old('titulo') }}" required class="ui-input" placeholder="Ej: Capacitación en ofimática">
                        @error('titulo') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="ui-campo">
                        <label for="descripcion">Descripción <span style="font-weight:500;color:var(--ui-texto-3)">(opcional)</span></label>
                        <textarea id="descripcion" name="descripcion" rows="4" class="ui-input">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="ui-btn ui-btn-primario">
                        <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        Agregar al catálogo
                    </button>
                </div>
            </form>

            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg></span> Actividades en el catálogo</h3>
                </div>
                @forelse ($actividades as $i => $actividad)
                    <div class="avs-item {{ $actividad->activo ? '' : 'inactiva' }}">
                        <span class="avs-num">{{ $i + 1 }}</span>
                        <div class="avs-info">
                            <strong>{{ $actividad->titulo }} @unless ($actividad->activo) <span class="ui-tag gris">Inactiva</span> @endunless</strong>
                            @if ($actividad->descripcion) <p>{{ $actividad->descripcion }}</p> @endif
                            <small>Agregada por {{ $actividad->creador->name ?? '—' }}</small>
                        </div>
                        @if ($actividad->activo)
                            <form action="{{ route('docente.actividades-vinculacion.desactivar', $actividad->id) }}" method="POST"
                                  data-confirm-title="¿Desactivar esta actividad?" data-confirm-text="Ya no aparecerá para los estudiantes." data-confirm-button="Sí, desactivar">
                                @csrf
                                <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Desactivar</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="ui-vacio">
                        <strong>Aún no hay actividades para esta carrera</strong>
                        Agrega la primera con el formulario.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
