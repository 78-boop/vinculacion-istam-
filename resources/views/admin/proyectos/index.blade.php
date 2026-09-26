@php
    $pendientes = $proyectos->where('estado', 'pendiente')->count();
    $estados = ['pendiente' => ['Pendiente', 'ambar'], 'aprobado' => ['Aprobado', 'verde'], 'rechazado' => ['Rechazado', 'rojo']];
@endphp

<x-app-layout>
    <div class="ui-wrap">
        <x-ui.hero etiqueta="Gestión académica" titulo="Proyectos de vinculación"
                   :subtitulo="$proyectos->count() . ' proyectos registrados' . ($pendientes ? ' · ' . $pendientes . ' propuestas esperan tu aprobación' : '')">
            <x-slot:acciones>
                <a href="{{ route('admin.proyectos.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nuevo proyecto
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        <section class="ui-panel">
            <div class="ui-tabla-wrap">
                <table class="ui-tabla">
                    <thead>
                        <tr><th>Proyecto</th><th>Docente</th><th>Período</th><th>Horas req.</th><th>Estado</th><th style="text-align:right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($proyectos as $proyecto)
                            <tr>
                                <td>
                                    <div class="ui-celda">
                                        <span class="ui-celda-ini">{{ mb_strtoupper(mb_substr($proyecto->nombre, 0, 1)) }}</span>
                                        <div style="min-width:0">
                                            <strong>{{ $proyecto->nombre }}</strong>
                                            @if ($proyecto->descripcion) <small>{{ \Illuminate\Support\Str::limit($proyecto->descripcion, 60) }}</small> @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $proyecto->docente->name ?? 'Sin asignar' }}</td>
                                <td>{{ $proyecto->periodoAcademico->nombre ?? '—' }}</td>
                                <td class="ui-num">{{ $proyecto->horas_requeridas ?? '—' }}</td>
                                <td><span class="ui-tag {{ $estados[$proyecto->estado][1] ?? 'gris' }}">{{ $estados[$proyecto->estado][0] ?? ucfirst($proyecto->estado) }}</span></td>
                                <td>
                                    <div class="ui-acciones">
                                        @if ($proyecto->estado === 'pendiente')
                                            <form action="{{ route('admin.proyectos.aprobar', $proyecto) }}" method="POST"
                                                  data-confirm-title="¿Aprobar «{{ $proyecto->nombre }}»?" data-confirm-text="Quedará visible para los estudiantes." data-confirm-button="Sí, aprobar">
                                                @csrf
                                                <button type="submit" class="ui-btn ui-btn-primario ui-btn-sm">Aprobar</button>
                                            </form>
                                            <form action="{{ route('admin.proyectos.rechazar', $proyecto) }}" method="POST"
                                                  data-confirm-title="¿Rechazar esta propuesta?" data-confirm-text="La propuesta quedará rechazada." data-confirm-button="Sí, rechazar">
                                                @csrf
                                                <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Rechazar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.proyectos.edit', $proyecto) }}" class="ui-btn ui-btn-suave ui-btn-sm">Editar</a>
                                        <form action="{{ route('admin.proyectos.destroy', $proyecto) }}" method="POST"
                                              data-confirm-title="¿Eliminar «{{ $proyecto->nombre }}»?" data-confirm-text="Esta acción no se puede deshacer.">
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
                                    <strong>Aún no hay proyectos</strong>
                                    <a href="{{ route('admin.proyectos.create') }}" class="ui-btn ui-btn-primario ui-btn-sm" style="margin-top:10px">Crear el primero</a>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
