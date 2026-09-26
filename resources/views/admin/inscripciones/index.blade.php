@php
    $estados = ['activo' => 'verde', 'completado' => 'verde', 'retirado' => 'gris'];
@endphp

<x-app-layout>
    <div class="ui-wrap">
        <x-ui.hero etiqueta="Personas" titulo="Inscripciones"
                   :subtitulo="$inscripciones->count() . ' inscripciones de estudiantes en proyectos'">
            <x-slot:acciones>
                <a href="{{ route('admin.inscripciones.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nueva inscripción
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        <section class="ui-panel">
            <div class="ui-tabla-wrap">
                <table class="ui-tabla">
                    <thead>
                        <tr><th>Estudiante</th><th>Proyecto</th><th>Inscrito el</th><th>Horas cumplidas</th><th>Estado</th><th style="text-align:right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($inscripciones as $inscripcion)
                            @php
                                $nombreEst = $inscripcion->estudiante->name ?? 'Sin asignar';
                                $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                            @endphp
                            <tr>
                                <td>
                                    <div class="ui-celda">
                                        <span class="ui-avatar">{{ $ini }}</span>
                                        <div style="min-width:0">
                                            <strong>{{ $nombreEst }}</strong>
                                            <small>{{ $inscripcion->estudiante->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $inscripcion->proyecto->nombre ?? '—' }}</td>
                                <td style="white-space:nowrap">{{ $inscripcion->fecha_inscripcion ? \Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y') : '—' }}</td>
                                <td class="ui-num">{{ $inscripcion->horas_cumplidas }} h</td>
                                <td><span class="ui-tag {{ $estados[$inscripcion->estado] ?? 'gris' }}">{{ ucfirst($inscripcion->estado) }}</span></td>
                                <td>
                                    <div class="ui-acciones">
                                        <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}" class="ui-btn ui-btn-suave ui-btn-sm">Editar</a>
                                        <form action="{{ route('admin.inscripciones.destroy', $inscripcion) }}" method="POST"
                                              data-confirm-title="¿Eliminar la inscripción de {{ $nombreEst }}?" data-confirm-text="Esta acción no se puede deshacer.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">
                                <div class="ui-vacio">
                                    <strong>Aún no hay inscripciones</strong>
                                    Los estudiantes quedan inscritos al elegir una actividad, o puedes inscribirlos tú.
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
