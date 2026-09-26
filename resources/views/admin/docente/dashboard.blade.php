@php
    $usuario = Auth::user();
    $hora = (int) now()->format('H');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $primerNombre = \Illuminate\Support\Str::before(trim($usuario->name) . ' ', ' ');
    $pendientes = $certificadosPendientes->total();
    $hoy = \Carbon\Carbon::today();
    $formatoHoras = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.');
    $maxInscritos = max(1, (int) $proyectos->max('inscripciones_count'));

    $ico = [
        'reloj'     => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'check'     => 'm5 13 4 4L19 7',
        'objetivo'  => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-4a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0-4a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z',
        'usuarios'  => 'M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17 0v-1a4 4 0 0 0-3-3.87M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm3-3.87a3.5 3.5 0 0 1 0 6.74',
        'doc'       => 'M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13h6m-6 4h4',
        'proyecto'  => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z',
        'idea'      => 'M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3Zm-2 17h4',
        'actividad' => 'M4 12h4l3-8 4 16 3-8h2',
        'lugar'     => 'M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        'flecha'    => 'M7 17 17 7M9 7h8v8',
    ];
@endphp

<x-app-layout>
    <style>
        .dd-grid { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr); gap: 20px; margin-top: 20px; align-items: start; }
        .dd-col { display: flex; flex-direction: column; gap: 20px; }
        .dd-badge { display: inline-grid; place-items: center; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 99px; background: #EF4444; color: #fff; font-size: 12px; font-weight: 800; }

        /* Documentos por revisar */
        .dd-doc { display: flex; align-items: center; gap: 14px; padding: 14px 22px; transition: background .15s; }
        .dd-doc + .dd-doc { border-top: 1px solid #EEF2F0; }
        .dd-doc:hover { background: #F8FBF9; }
        .dd-av { width: 42px; height: 42px; border-radius: 13px; flex-shrink: 0; display: grid; place-items: center; font-weight: 800; font-size: 14px; color: #fff; background: linear-gradient(135deg, #60A5FA, #2563EB); }
        .dd-doc-info { flex: 1; min-width: 0; }
        .dd-doc-info strong { display: block; font-size: 14px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-doc-info small { display: block; font-size: 12.5px; color: var(--ui-texto-3); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-cod { font-size: 11px; font-weight: 800; color: var(--ui-verde); background: var(--ui-menta); padding: 3px 7px; border-radius: 7px; margin-right: 4px; }
        .dd-doc-fecha { font-size: 12px; color: var(--ui-texto-3); white-space: nowrap; }

        /* Actividades a cargo */
        .dd-act { display: grid; grid-template-columns: 54px minmax(0, 1fr) auto; gap: 14px; align-items: center; padding: 14px 22px; }
        .dd-act + .dd-act { border-top: 1px solid #EEF2F0; }
        .dd-fecha { width: 54px; border-radius: 13px; overflow: hidden; text-align: center; border: 1px solid var(--ui-borde); background: #fff; }
        .dd-fecha span { display: block; background: var(--ui-verde); color: #fff; font-size: 10.5px; font-weight: 800; text-transform: uppercase; padding: 2px 0; }
        .dd-fecha strong { display: block; font-size: 19px; font-weight: 800; color: var(--ui-texto); padding: 3px 0 4px; line-height: 1.1; }
        .dd-act-info strong { display: block; font-size: 14px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-act-info small { display: flex; align-items: center; gap: 5px; font-size: 12.5px; color: var(--ui-texto-3); overflow: hidden; white-space: nowrap; }
        .dd-act-info svg { width: 13px; height: 13px; color: var(--ui-verde); flex-shrink: 0; }
        .dd-act-lado { text-align: right; }
        .dd-act-lado strong { display: block; font-size: 17px; font-weight: 800; color: var(--ui-verde); line-height: 1; }
        .dd-act-lado small { font-size: 11.5px; color: var(--ui-texto-3); font-weight: 600; }

        /* Proyectos */
        .dd-proy { padding: 14px 22px; }
        .dd-proy + .dd-proy { border-top: 1px solid #EEF2F0; }
        .dd-proy-fila { display: flex; justify-content: space-between; align-items: baseline; gap: 10px; margin-bottom: 8px; }
        .dd-proy-fila strong { font-size: 14px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-proy-fila span { font-size: 12.5px; font-weight: 700; color: var(--ui-verde); white-space: nowrap; }

        /* Estudiantes */
        .dd-est { display: flex; align-items: center; gap: 12px; padding: 11px 22px; }
        .dd-est + .dd-est { border-top: 1px solid #EEF2F0; }
        .dd-est .ui-avatar { width: 36px; height: 36px; font-size: 12.5px; }
        .dd-est-info { min-width: 0; }
        .dd-est-info strong { display: block; font-size: 13.5px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-est-info small { display: block; font-size: 12px; color: var(--ui-texto-3); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-scroll { max-height: 340px; overflow-y: auto; }

        .dd-pag { padding: 14px 22px; border-top: 1px solid var(--ui-borde); }

        @media (max-width: 1100px) { .dd-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) {
            .dd-doc { flex-wrap: wrap; }
            .dd-doc-fecha { order: 3; width: 100%; padding-left: 56px; }
        }
    </style>

    <div class="ui-wrap">

        {{-- ===== Cabecera ===== --}}
        <x-ui.hero etiqueta="Panel del docente" :titulo="$saludo . ', ' . $primerNombre . ' 👋'"
                   subtitulo="Revisa los documentos de tus estudiantes, sigue tus actividades y gestiona tus proyectos.">
            <x-slot:acciones>
                <a href="{{ route('docente.certificados.index') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['doc'] }}"/></svg>
                    Revisar documentos
                    @if ($pendientes > 0) <span class="dd-badge">{{ $pendientes }}</span> @endif
                </a>
                <a href="{{ route('docente.proyectos.create') }}" class="ui-btn ui-btn-vidrio">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['idea'] }}"/></svg>
                    Proponer proyecto
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        {{-- ===== Indicadores ===== --}}
        <section class="ui-kpis">
            <a href="{{ route('docente.certificados.index') }}" class="ui-kpi ambar">
                <div class="ui-kpi-top">
                    <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['reloj'] }}"/></svg></span>
                    <span class="ui-kpi-flecha"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg></span>
                </div>
                <div class="ui-kpi-valor">{{ $pendientes }}</div>
                <div class="ui-kpi-label">Documentos por revisar</div>
            </a>
            <a href="{{ route('docente.certificados.index') }}" class="ui-kpi lima">
                <div class="ui-kpi-top">
                    <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['check'] }}"/></svg></span>
                    <span class="ui-kpi-flecha"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg></span>
                </div>
                <div class="ui-kpi-valor">{{ $certificadosAprobados ?? 0 }}</div>
                <div class="ui-kpi-label">Documentos aprobados</div>
            </a>
            <a href="{{ route('docente.actividades.index', ['ver' => 'en_curso']) }}" class="ui-kpi violeta">
                <div class="ui-kpi-top">
                    <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividad'] }}"/></svg></span>
                    <span class="ui-kpi-flecha"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg></span>
                </div>
                <div class="ui-kpi-valor">{{ $actividadesEnCurso }}</div>
                <div class="ui-kpi-label">Actividades en curso</div>
            </a>
            <div class="ui-kpi azul">
                <div class="ui-kpi-top">
                    <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['usuarios'] }}"/></svg></span>
                </div>
                <div class="ui-kpi-valor">{{ count($estudiantesAsignados) }}</div>
                <div class="ui-kpi-label">Estudiantes a cargo · {{ count($proyectos) }} {{ count($proyectos) === 1 ? 'proyecto' : 'proyectos' }}</div>
            </div>
        </section>

        <section class="dd-grid">
            {{-- ===== Columna principal ===== --}}
            <div class="dd-col">
                {{-- Documentos por revisar --}}
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico" style="background:#FEF3C7;color:#B45309"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['doc'] }}"/></svg></span> Documentos por revisar</h3>
                        <a href="{{ route('docente.certificados.index') }}">Ver estudiantes →</a>
                    </div>
                    @forelse ($certificadosPendientes as $certificado)
                        @php
                            $nombreEst = $certificado->inscripcion->estudiante->name ?? 'Estudiante';
                            $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                        @endphp
                        <div class="dd-doc">
                            <span class="dd-av">{{ $ini }}</span>
                            <div class="dd-doc-info">
                                <strong>{{ $nombreEst }}</strong>
                                <small><span class="dd-cod">{{ $certificado->tipoCertificado->codigo }}</span>{{ $certificado->tipoCertificado->nombre }} · {{ $certificado->inscripcion->proyecto->nombre ?? '' }}</small>
                            </div>
                            <span class="dd-doc-fecha">{{ $certificado->updated_at?->diffForHumans() }}</span>
                            <a href="{{ route('docente.certificados.show', $certificado->inscripcion_id) }}" class="ui-btn ui-btn-primario ui-btn-sm">Revisar</a>
                        </div>
                    @empty
                        <div class="ui-vacio">
                            <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['check'] }}"/></svg></div>
                            <strong>¡Todo al día!</strong>
                            No tienes documentos pendientes de revisión.
                        </div>
                    @endforelse
                    @if ($certificadosPendientes->hasPages())
                        <div class="dd-pag">{{ $certificadosPendientes->links() }}</div>
                    @endif
                </div>

                {{-- Actividades a cargo --}}
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividad'] }}"/></svg></span> Mis actividades</h3>
                        <a href="{{ route('docente.actividades.index') }}">Ver todas →</a>
                    </div>
                    @forelse ($misActividades as $act)
                        @php
                            $inicio = $act->fecha_inicio ?? $act->fecha;
                            $fin = $act->fecha_finalizacion ?? $inicio;
                            $estadoTiempo = ($inicio && $hoy->lt($inicio)) ? ['Próxima', 'ambar'] : (($fin && $hoy->gt($fin)) ? ['Finalizada', 'gris'] : ['En curso', 'verde']);
                        @endphp
                        <div class="dd-act">
                            <div class="dd-fecha" aria-hidden="true">
                                <span>{{ $inicio?->translatedFormat('M') ?? '—' }}</span>
                                <strong>{{ $inicio?->format('d') ?? '—' }}</strong>
                            </div>
                            <div class="dd-act-info" style="min-width:0">
                                <strong>{{ $act->proyecto->nombre ?? 'Actividad' }} <span class="ui-tag {{ $estadoTiempo[1] }}" style="margin-left:4px">{{ $estadoTiempo[0] }}</span></strong>
                                <small>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['lugar'] }}"/></svg>
                                    {{ $act->lugar ?: 'Sin lugar' }} · {{ $act->inscripciones_count }} {{ $act->inscripciones_count === 1 ? 'estudiante' : 'estudiantes' }}
                                </small>
                            </div>
                            <div class="dd-act-lado">
                                <strong>{{ $formatoHoras($act->horas) }} h</strong>
                                <small>hasta {{ $fin?->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="ui-vacio">
                            <strong>Aún no tienes actividades asignadas</strong>
                            Cuando el administrador cree actividades en tus proyectos o te asigne como responsable, aparecerán aquí.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ===== Columna lateral ===== --}}
            <div class="dd-col">
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['proyecto'] }}"/></svg></span> Mis proyectos</h3>
                        <a href="{{ route('docente.proyectos.create') }}">+ Proponer</a>
                    </div>
                    @forelse ($proyectos as $proyecto)
                        <div class="dd-proy">
                            <div class="dd-proy-fila">
                                <strong>{{ $proyecto->nombre }}</strong>
                                <span>{{ $proyecto->inscripciones_count ?? 0 }} inscritos</span>
                            </div>
                            <div class="ui-progreso"><span style="width: {{ round(($proyecto->inscripciones_count ?? 0) / $maxInscritos * 100) }}%"></span></div>
                        </div>
                    @empty
                        <div class="ui-vacio">
                            <strong>No tienes proyectos todavía</strong>
                            <a href="{{ route('docente.proyectos.create') }}" class="ui-btn ui-btn-primario ui-btn-sm" style="margin-top:10px">Proponer un proyecto</a>
                        </div>
                    @endforelse
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico" style="background:#DBEAFE;color:#1D4ED8"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['usuarios'] }}"/></svg></span> Mis estudiantes</h3>
                        <span class="ui-tag gris">{{ count($estudiantesAsignados) }}</span>
                    </div>
                    <div class="dd-scroll">
                        @forelse ($estudiantesAsignados as $inscripcion)
                            @php
                                $nombreEst = $inscripcion->estudiante->name ?? 'Estudiante';
                                $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                            @endphp
                            <a href="{{ route('docente.certificados.show', $inscripcion->id) }}" class="dd-est" style="text-decoration:none">
                                <span class="ui-avatar">{{ $ini }}</span>
                                <div class="dd-est-info">
                                    <strong>{{ $nombreEst }}</strong>
                                    <small>{{ $inscripcion->estudiante->email ?? '' }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="ui-vacio">Aún no tienes estudiantes inscritos.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
