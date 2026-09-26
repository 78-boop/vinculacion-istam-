<x-app-layout>
    <style>
        .dc-lista { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px; }
        .dc-card { background: #fff; border-radius: 22px; border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra); padding: 20px; display: flex; flex-direction: column; gap: 16px; transition: transform .2s, box-shadow .2s; }
        .dc-card:hover { transform: translateY(-3px); box-shadow: var(--ui-sombra-hover); }
        .dc-card.tiene-pendientes { border: 2px solid #FCD34D; }
        .dc-cab { display: flex; align-items: center; gap: 12px; }
        .dc-cab .ui-avatar { width: 46px; height: 46px; font-size: 15px; border-radius: 15px; }
        .dc-cab-info { flex: 1; min-width: 0; }
        .dc-cab-info strong { display: block; font-size: 15.5px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dc-cab-info small { display: block; font-size: 12.5px; color: var(--ui-texto-3); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dc-datos { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .dc-dato { background: #F6F9F7; border-radius: 14px; padding: 12px; }
        .dc-dato small { display: block; font-size: 11.5px; font-weight: 700; color: var(--ui-texto-3); text-transform: uppercase; letter-spacing: .04em; }
        .dc-dato strong { font-size: 20px; font-weight: 800; color: var(--ui-texto); }
        .dc-dato span { font-size: 12.5px; color: var(--ui-texto-3); font-weight: 600; }
        .dc-horas-form { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
        .dc-horas-form input { width: 80px; padding: 6px 8px !important; font-size: 14px; }
        .dc-editar { border: 0; background: none; padding: 0; margin-top: 4px; font-family: inherit; font-size: 12px; font-weight: 700; color: var(--ui-verde); cursor: pointer; }
        .dc-editar:hover { text-decoration: underline; }
        .dc-pie { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: auto; }
    </style>

    <div class="ui-wrap">
        <x-ui.hero etiqueta="Revisión de documentos" titulo="Certificados de mis estudiantes"
                   subtitulo="Revisa los documentos que suben tus estudiantes y registra sus horas cumplidas." />

        @if ($inscripciones->isEmpty())
            <div class="ui-panel">
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m10-12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/></svg></div>
                    <strong>Aún no tienes estudiantes inscritos</strong>
                    Cuando se inscriban en tus proyectos aparecerán aquí.
                </div>
            </div>
        @else
            <div class="dc-lista">
                @foreach ($inscripciones as $inscripcion)
                    @php
                        $aprobados = $inscripcion->certificadosVigentes->where('estado', 'aprobado')->count();
                        $pendientes = $inscripcion->certificadosVigentes->where('estado', 'pendiente')->count();
                        $carrera = $inscripcion->estudiante->carrera;
                        $nombreEst = $inscripcion->estudiante->name;
                        $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                        $pct = $totalTipos > 0 ? round($aprobados / $totalTipos * 100) : 0;
                    @endphp
                    <article class="dc-card {{ $pendientes ? 'tiene-pendientes' : '' }}">
                        <div class="dc-cab">
                            <span class="ui-avatar">{{ $ini }}</span>
                            <div class="dc-cab-info">
                                <strong>{{ $nombreEst }}</strong>
                                <small>{{ $inscripcion->proyecto->nombre }} · {{ $carrera->nombre ?? 'Sin carrera' }}</small>
                            </div>
                            @if ($pendientes)
                                <span class="ui-tag ambar">{{ $pendientes }} por revisar</span>
                            @endif
                        </div>

                        <div class="dc-datos">
                            <div class="dc-dato">
                                <small>Documentos</small>
                                <strong>{{ $aprobados }}</strong><span> / {{ $totalTipos }}</span>
                                <div class="ui-progreso" style="margin-top:8px"><span style="width: {{ $pct }}%"></span></div>
                            </div>
                            <div class="dc-dato" x-data="{ editando: false }">
                                <small>Horas cumplidas</small>
                                <div x-show="!editando">
                                    <strong>{{ $inscripcion->horas_cumplidas }}</strong><span> / {{ $carrera->horas_requeridas ?? '—' }} h</span>
                                    <button type="button" class="dc-editar" @click="editando = true">Editar horas</button>
                                </div>
                                <form x-show="editando" x-cloak action="{{ route('docente.certificados.actualizar-horas', $inscripcion->id) }}" method="POST"
                                      data-confirm-title="¿Guardar las horas de {{ $nombreEst }}?" data-confirm-button="Sí, guardar">
                                    @csrf
                                    <div class="dc-horas-form">
                                        <input type="number" name="horas_cumplidas" min="0" max="1000" value="{{ $inscripcion->horas_cumplidas }}" class="ui-input">
                                        <span style="font-size:12.5px;color:var(--ui-texto-3)">/ {{ $carrera->horas_requeridas ?? '—' }} h</span>
                                    </div>
                                    <div style="display:flex;gap:10px;margin-top:6px">
                                        <button type="submit" class="dc-editar">Guardar</button>
                                        <button type="button" class="dc-editar" style="color:var(--ui-texto-3)" @click="editando = false">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="dc-pie">
                            <span class="ui-tag {{ $aprobados >= $totalTipos && $totalTipos > 0 ? 'verde' : 'gris' }}">{{ $aprobados >= $totalTipos && $totalTipos > 0 ? 'Completo' : $pct . '% aprobado' }}</span>
                            <a href="{{ route('docente.certificados.show', $inscripcion->id) }}" class="ui-btn ui-btn-primario ui-btn-sm">
                                Revisar documentos
                                <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
