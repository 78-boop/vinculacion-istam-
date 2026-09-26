@php
    $hora = (int) now()->format('H');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $primerNombre = \Illuminate\Support\Str::before(trim(Auth::user()->name) . ' ', ' ');
    $maxEstudiantesCarrera = max(1, (int) $carreras->max('estudiantes_count'));
    $maxActEstudiante = max(1, (int) $estudiantesActivos->max('actividades_count'));
    $maxActProyecto = max(1, (int) $proyectosActivos->max('actividades_aprobadas'));
    $coloresCarrera = ['#006B47', '#2563EB', '#7C3AED', '#D97706', '#DB2777', '#0891B2'];

    $ico = [
        'periodos'    => 'M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'proyectos'   => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z',
        'inscripcion' => 'M9 5h6m-6 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2m-6 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 13l2 2 4-4',
        'actividades' => 'M4 12h4l3-8 4 16 3-8h2',
        'carreras'    => 'M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5M22 9v6',
        'usuarios'    => 'M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17 0v-1a4 4 0 0 0-3-3.87M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm3-3.87a3.5 3.5 0 0 1 0 6.74',
        'reloj'       => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'alerta'      => 'M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z',
        'mas'         => 'M12 5v14M5 12h14',
        'flecha'      => 'M7 17 17 7M9 7h8v8',
        'trofeo'      => 'M8 21h8m-4-4v4M7 4h10v5a5 5 0 0 1-10 0V4Zm10 2h3a2 2 0 0 1-2 4h-1M7 6H4a2 2 0 0 0 2 4h1',
    ];
@endphp

<x-app-layout>
    <style>
        .db-grid-2 { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr); gap: 20px; margin-top: 20px; }
        .db-grid-3 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-top: 20px; }

        /* Hero */
        .db-hero-datos { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 18px; }
        .db-hero-dato {
            display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 14px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); backdrop-filter: blur(6px);
        }
        .db-hero-dato svg { width: 18px; height: 18px; color: var(--ui-lima); }
        .db-hero-dato small { display: block; font-size: 11px; color: #BFE3CF; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
        .db-hero-dato strong { font-size: 14.5px; }

        /* Atención */
        .db-atencion { display: grid; gap: 12px; }
        .db-alerta {
            display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 16px;
            text-decoration: none !important; transition: transform .18s, box-shadow .18s;
        }
        .db-alerta:hover { transform: translateX(4px); box-shadow: var(--ui-sombra); }
        .db-alerta-ico { width: 44px; height: 44px; border-radius: 13px; display: grid; place-items: center; flex-shrink: 0; }
        .db-alerta-ico svg { width: 22px; height: 22px; }
        .db-alerta strong { display: block; font-size: 24px; font-weight: 800; line-height: 1; }
        .db-alerta span { font-size: 13px; font-weight: 600; }
        .db-alerta .db-ir { margin-left: auto; width: 20px; height: 20px; opacity: .6; }
        .db-alerta.ambar { background: #FFF8EB; color: #92400E; border: 1px solid #FDE7B0; }
        .db-alerta.ambar .db-alerta-ico { background: #F59E0B; color: #fff; }
        .db-alerta.verde { background: #EEF9F3; color: #065F46; border: 1px solid #C9EBD8; }
        .db-alerta.verde .db-alerta-ico { background: var(--ui-verde); color: #fff; }
        .db-alerta.azul { background: #EFF5FF; color: #1E40AF; border: 1px solid #CFDFFE; }
        .db-alerta.azul .db-alerta-ico { background: #2563EB; color: #fff; }

        /* Acciones rápidas */
        .db-rapidas { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .db-rapida {
            display: flex; flex-direction: column; gap: 10px; padding: 14px; border-radius: 15px;
            background: #F6F9F7; border: 1px solid transparent; text-decoration: none !important;
            color: var(--ui-texto) !important; font-size: 13.5px; font-weight: 700; transition: all .18s;
        }
        .db-rapida:hover { background: #fff; border-color: var(--ui-menta-2); box-shadow: var(--ui-sombra); transform: translateY(-2px); }
        .db-rapida .ui-chip-ico { transition: background .18s, color .18s; }
        .db-rapida:hover .ui-chip-ico { background: var(--ui-verde); color: #fff; }

        /* Carreras */
        .db-carrera { display: grid; grid-template-columns: 12px minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 11px 0; }
        .db-carrera + .db-carrera { border-top: 1px dashed var(--ui-borde); }
        .db-punto { width: 12px; height: 12px; border-radius: 4px; }
        .db-carrera-nombre { font-size: 14px; font-weight: 700; color: var(--ui-texto); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .db-carrera-barra { margin-top: 7px; height: 7px; border-radius: 99px; background: #EEF2F0; overflow: hidden; }
        .db-carrera-barra span { display: block; height: 100%; border-radius: 99px; }
        .db-carrera-num { text-align: right; }
        .db-carrera-num strong { display: block; font-size: 17px; font-weight: 800; color: var(--ui-texto); line-height: 1; }
        .db-carrera-num small { font-size: 11.5px; color: var(--ui-texto-3); font-weight: 600; }

        /* Ranking */
        .db-rank { display: flex; align-items: center; gap: 14px; padding: 13px 22px; transition: background .15s; }
        .db-rank + .db-rank { border-top: 1px solid #F0F3F1; }
        .db-rank:hover { background: #F8FBF9; }
        .db-pos {
            width: 34px; height: 34px; border-radius: 11px; display: grid; place-items: center; flex-shrink: 0;
            font-size: 14px; font-weight: 800; background: #F1F5F3; color: var(--ui-texto-2);
        }
        .db-pos.p1 { background: linear-gradient(135deg, #FCD34D, #F59E0B); color: #fff; box-shadow: 0 6px 14px -6px #F59E0B; }
        .db-pos.p2 { background: linear-gradient(135deg, #E5E7EB, #9CA3AF); color: #fff; }
        .db-pos.p3 { background: linear-gradient(135deg, #FDBA74, #C2410C); color: #fff; }
        .db-rank-info { flex: 1; min-width: 0; }
        .db-rank-info p { margin: 0 0 6px; font-size: 14px; font-weight: 700; color: var(--ui-texto); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .db-rank-num { text-align: right; min-width: 58px; }
        .db-rank-num strong { display: block; font-size: 18px; font-weight: 800; color: var(--ui-verde); line-height: 1; }
        .db-rank-num small { font-size: 11px; color: var(--ui-texto-3); font-weight: 600; }

        /* Tabla actividades */
        .db-tabla td, .db-tabla th { padding: 13px 22px !important; font-size: 14px; }
        .db-proy { display: flex; align-items: center; gap: 12px; }
        .db-proy-ini {
            width: 36px; height: 36px; border-radius: 11px; flex-shrink: 0; display: grid; place-items: center;
            background: var(--ui-menta); color: var(--ui-verde); font-weight: 800; font-size: 13px;
        }
        .db-horas { font-weight: 800; color: var(--ui-texto); }

        @media (max-width: 1100px) { .db-grid-2 { grid-template-columns: 1fr; } }
        @media (max-width: 800px) { .db-grid-3 { grid-template-columns: 1fr; } }
    </style>

    <div class="ui-wrap">

        {{-- ===== Hero ===== --}}
        <section class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.4-6.4-1.4 1.4M7 17l-1.4 1.4m12.8 0L17 17M7 7 5.6 5.6M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
                    Panel de administración
                </span>
                <h1>{{ $saludo }}, {{ $primerNombre }} 👋</h1>
                <p>Este es el resumen del sistema de vinculación. Revisa lo pendiente y gestiona todo desde aquí.</p>
                <div class="db-hero-datos">
                    <div class="db-hero-dato">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['periodos'] }}"/></svg>
                        <div><small>Período activo</small><strong>{{ $periodoActivo->nombre ?? 'Sin período activo' }}</strong></div>
                    </div>
                    <div class="db-hero-dato">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['usuarios'] }}"/></svg>
                        <div><small>Usuarios</small><strong>{{ $totalUsuarios }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="ui-hero-acciones">
                <a href="{{ route('admin.proyectos.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="{{ $ico['mas'] }}"/></svg>
                    Nuevo proyecto
                </a>
                <a href="{{ route('admin.carreras.create') }}" class="ui-btn ui-btn-vidrio">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="{{ $ico['mas'] }}"/></svg>
                    Nueva carrera
                </a>
            </div>
        </section>

        {{-- ===== KPIs ===== --}}
        <section class="ui-kpis" style="margin-top: 22px; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
            @foreach ([
                ['Períodos académicos', $totalPeriodos, 'admin.periodos.index', 'periodos', ''],
                ['Carreras activas', $carreras->count(), 'admin.carreras.index', 'carreras', 'lima'],
                ['Proyectos de vinculación', $totalProyectos, 'admin.proyectos.index', 'proyectos', 'azul'],
                ['Inscripciones', $totalInscripciones, 'admin.inscripciones.index', 'inscripcion', 'violeta'],
                ['Actividades', $totalActividades, 'admin.actividades.index', 'actividades', 'ambar'],
            ] as [$label, $valor, $ruta, $icono, $color])
                <a href="{{ route($ruta) }}" class="ui-kpi {{ $color }}">
                    <div class="ui-kpi-top">
                        <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico[$icono] }}"/></svg></span>
                        <span class="ui-kpi-flecha"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg></span>
                    </div>
                    <div class="ui-kpi-valor">{{ $valor }}</div>
                    <div class="ui-kpi-label">{{ $label }}</div>
                </a>
            @endforeach
        </section>

        {{-- ===== Actividades recientes + Atención ===== --}}
        <section class="db-grid-2">
            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividades'] }}"/></svg></span> Últimas actividades registradas</h3>
                    <a href="{{ route('admin.actividades.index') }}">Ver todas →</a>
                </div>
                <div style="overflow-x: auto;">
                    <table class="db-tabla">
                        <thead>
                            <tr><th>Proyecto</th><th>Fecha</th><th>Horas</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasActividades as $actividad)
                                @php $nombreProyecto = $actividad->proyecto?->nombre ?? $actividad->inscripcion?->proyecto?->nombre ?? 'Sin proyecto'; @endphp
                                <tr>
                                    <td>
                                        <div class="db-proy">
                                            <span class="db-proy-ini">{{ mb_strtoupper(mb_substr($nombreProyecto, 0, 1)) }}</span>
                                            <span style="font-weight: 600;">{{ \Illuminate\Support\Str::limit($nombreProyecto, 38) }}</span>
                                        </div>
                                    </td>
                                    <td style="white-space: nowrap;">{{ ($actividad->fecha_inicio ?? $actividad->fecha)?->translatedFormat('d M Y') ?? '—' }}</td>
                                    <td class="db-horas" style="white-space:nowrap">{{ rtrim(rtrim(number_format((float) $actividad->horas, 2), '0'), '.') }} h</td>
                                    <td>
                                        @if($actividad->estado === 'aprobada')
                                            <span class="ui-tag verde">Aprobada</span>
                                        @elseif($actividad->estado === 'pendiente')
                                            <span class="ui-tag ambar">Pendiente</span>
                                        @else
                                            <span class="ui-tag rojo">Rechazada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="ui-vacio">
                                            <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividades'] }}"/></svg></div>
                                            <strong>Sin actividades todavía</strong>
                                            Cuando se registren actividades aparecerán aquí.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="ui-panel">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico" style="background:#FFF4DB;color:#B45309"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['alerta'] }}"/></svg></span> Requiere atención</h3>
                    </div>
                    <div class="ui-panel-body db-atencion">
                        <a href="{{ route('admin.actividades.index') }}" class="db-alerta ambar">
                            <span class="db-alerta-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['reloj'] }}"/></svg></span>
                            <div><strong>{{ $actividadesPendientes }}</strong><span>Actividades pendientes de aprobación</span></div>
                            <svg class="db-ir" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                        </a>
                    </div>
                </div>

                <div class="ui-panel">
                    <div class="ui-panel-head"><h3>Acciones rápidas</h3></div>
                    <div class="ui-panel-body db-rapidas">
                        @foreach ([
                            ['Nuevo período', 'admin.periodos.create', 'periodos'],
                            ['Nueva carrera', 'admin.carreras.create', 'carreras'],
                            ['Nuevo proyecto', 'admin.proyectos.create', 'proyectos'],
                            ['Nueva actividad', 'admin.actividades.create', 'actividades'],
                            ['Nuevo usuario', 'admin.usuarios.create', 'usuarios'],
                            ['Nueva inscripción', 'admin.inscripciones.create', 'inscripcion'],
                        ] as [$texto, $ruta, $icono])
                            <a href="{{ route($ruta) }}" class="db-rapida">
                                <span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico[$icono] }}"/></svg></span>
                                {{ $texto }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Carreras + rankings ===== --}}
        <section class="db-grid-3">
            <div class="ui-panel" style="grid-column: 1 / -1;">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['carreras'] }}"/></svg></span> Estudiantes por carrera</h3>
                    <a href="{{ route('admin.carreras.index') }}">Gestionar carreras →</a>
                </div>
                <div class="ui-panel-body">
                    @forelse ($carreras as $i => $carrera)
                        @php $color = $coloresCarrera[$i % count($coloresCarrera)]; @endphp
                        <div class="db-carrera">
                            <span class="db-punto" style="background: {{ $color }}"></span>
                            <div style="min-width: 0;">
                                <div class="db-carrera-nombre">{{ $carrera->nombre }}</div>
                                <div class="db-carrera-barra"><span style="width: {{ round($carrera->estudiantes_count / $maxEstudiantesCarrera * 100) }}%; background: {{ $color }};"></span></div>
                            </div>
                            <div class="db-carrera-num">
                                <strong>{{ $carrera->estudiantes_count }}</strong>
                                <small>estudiantes · {{ $carrera->actividades_count }} {{ $carrera->actividades_count === 1 ? 'actividad' : 'actividades' }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="ui-vacio">
                            <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['carreras'] }}"/></svg></div>
                            <strong>No hay carreras activas</strong>
                            <a href="{{ route('admin.carreras.create') }}" class="ui-btn ui-btn-primario ui-btn-sm" style="margin-top: 10px;">Crear carrera</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico" style="background:#FEF3C7;color:#B45309"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['trofeo'] }}"/></svg></span> Estudiantes más activos</h3>
                </div>
                @forelse($estudiantesActivos as $i => $inscripcion)
                    <div class="db-rank">
                        <span class="db-pos {{ $i < 3 ? 'p' . ($i + 1) : '' }}">{{ $i + 1 }}</span>
                        <div class="db-rank-info">
                            <p>{{ $inscripcion->estudiante->name ?? 'Estudiante' }}</p>
                            <div class="ui-progreso"><span style="width: {{ round($inscripcion->actividades_count / $maxActEstudiante * 100) }}%"></span></div>
                        </div>
                        <div class="db-rank-num"><strong>{{ $inscripcion->actividades_count }}</strong><small>actividades</small></div>
                    </div>
                @empty
                    <div class="ui-vacio">Sin datos disponibles</div>
                @endforelse
            </div>

            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico" style="background:#DBEAFE;color:#1D4ED8"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['proyectos'] }}"/></svg></span> Proyectos más activos</h3>
                    <a href="{{ route('admin.proyectos.index') }}">Ver todos →</a>
                </div>
                @forelse($proyectosActivos as $i => $proyecto)
                    <div class="db-rank">
                        <span class="db-pos {{ $i < 3 ? 'p' . ($i + 1) : '' }}">{{ $i + 1 }}</span>
                        <div class="db-rank-info">
                            <p>{{ $proyecto->nombre }}</p>
                            <div class="ui-progreso"><span style="width: {{ round($proyecto->actividades_aprobadas / $maxActProyecto * 100) }}%; background: linear-gradient(90deg, #93C5FD, #2563EB);"></span></div>
                        </div>
                        <div class="db-rank-num"><strong style="color:#2563EB">{{ $proyecto->actividades_aprobadas }}</strong><small>aprobadas</small></div>
                    </div>
                @empty
                    <div class="ui-vacio">Sin datos disponibles</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
