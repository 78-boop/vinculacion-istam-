@php
    $hoy = \Carbon\Carbon::today();
    $formatoHoras = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.');
@endphp

<x-app-layout>
    <div class="ui-wrap">
        <x-ui.hero etiqueta="Gestión académica" titulo="Actividades"
                   :subtitulo="$actividades->count() . ' actividades registradas. Los estudiantes se inscriben desde «Proyectos disponibles».'">
            <x-slot:acciones>
                <a href="{{ route('admin.actividades.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nueva actividad
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        <section class="ui-panel">
            <div class="ui-tabla-wrap">
                <table class="ui-tabla">
                    <thead>
                        <tr><th>Actividad</th><th>Docente</th><th>Estudiantes</th><th>Fechas</th><th>Horas</th><th>Estado</th><th style="text-align:right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($actividades as $actividad)
                            @php
                                $inicio = $actividad->fecha_inicio ?? $actividad->fecha;
                                $fin = $actividad->fecha_finalizacion;
                                $estudiantes = $actividad->inscripciones->pluck('estudiante.name')->filter();
                                if ($estudiantes->isEmpty() && $actividad->inscripcion?->estudiante) $estudiantes = collect([$actividad->inscripcion->estudiante->name]);
                            @endphp
                            <tr>
                                <td>
                                    <div class="ui-celda">
                                        <span class="ui-celda-ini">{{ $inicio?->format('d') ?? '—' }}</span>
                                        <div style="min-width:0">
                                            <strong>{{ $actividad->proyecto->nombre ?? $actividad->inscripcion->proyecto->nombre ?? '—' }}</strong>
                                            <small>{{ $actividad->lugar ?: 'Sin lugar' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $actividad->docente->name ?? '—' }}</td>
                                <td>
                                    @if ($estudiantes->isNotEmpty())
                                        <span class="ui-tag gris" title="{{ $estudiantes->join(', ') }}">{{ $estudiantes->count() }} {{ $estudiantes->count() === 1 ? 'estudiante' : 'estudiantes' }}</span>
                                        <small style="display:block;margin-top:4px;color:var(--ui-texto-3);font-size:12px">{{ \Illuminate\Support\Str::limit($estudiantes->join(', '), 40) }}</small>
                                    @else
                                        <span style="color:var(--ui-texto-3)">Sin inscritos</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap">{{ $inicio?->format('d/m/Y') ?? '—' }}<br><small style="color:var(--ui-texto-3)">hasta {{ $fin?->format('d/m/Y') ?? '—' }}</small></td>
                                <td class="ui-num">{{ $formatoHoras($actividad->horas) }} h</td>
                                <td>
                                    <span class="ui-tag {{ $actividad->estado === 'aprobada' ? 'verde' : 'ambar' }}">{{ ucfirst($actividad->estado) }}</span>
                                    @if ($fin && $hoy->gt($fin)) <span class="ui-tag gris" style="margin-top:4px">Finalizada</span> @endif
                                </td>
                                <td>
                                    <div class="ui-acciones">
                                        <a href="{{ route('admin.actividades.edit', $actividad) }}" class="ui-btn ui-btn-suave ui-btn-sm">Editar</a>
                                        <form action="{{ route('admin.actividades.destroy', $actividad) }}" method="POST"
                                              data-confirm-title="¿Eliminar esta actividad?" data-confirm-text="Los estudiantes inscritos dejarán de verla. Esta acción no se puede deshacer.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">
                                <div class="ui-vacio">
                                    <strong>Aún no hay actividades</strong>
                                    <a href="{{ route('admin.actividades.create') }}" class="ui-btn ui-btn-primario ui-btn-sm" style="margin-top:10px">Crear la primera</a>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
