@php
    $usuario = Auth::user();
    $hora = (int) now()->format('H');
    $saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $primerNombre = \Illuminate\Support\Str::before(trim($usuario->name) . ' ', ' ');

    $totalDocs = (int) ($totalTipos ?? 0);
    $aprobados = (int) ($certificadosAprobados ?? 0);
    $pendientes = (int) ($certificadosPendientes ?? 0);
    $rechazados = (int) ($certificadosRechazados ?? 0);
    $porcentajeDocs = $totalDocs > 0 ? min(100, (int) round($aprobados / $totalDocs * 100)) : 0;

    // Anillo de progreso (SVG)
    $radio = 52;
    $circ = 2 * M_PI * $radio;
    $trazo = $circ * (1 - $porcentajeDocs / 100);

    $hoy = \Carbon\Carbon::today();
    $formatoHoras = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.');

    // Pasos del proceso de vinculación
    $tieneActividad = (bool) $miActividad;
    $docsCompletos = $totalDocs > 0 && $aprobados >= $totalDocs;
    $pasos = [
        ['Escoge un proyecto', $tieneActividad ? 'hecho' : 'actual', $tieneActividad ? ($miActividad->proyecto->nombre ?? 'Elegido') : 'Mira los disponibles'],
        ['Inscríbete en una actividad', $tieneActividad ? 'hecho' : 'bloqueado', $tieneActividad ? 'Inscrito' : 'Solo una'],
        ['Sube tus documentos', $docsCompletos ? 'hecho' : ($tieneActividad ? 'actual' : 'bloqueado'), $totalDocs ? "$aprobados de $totalDocs aprobados" : 'Aún no hay documentos'],
        ['Certificado final', $docsCompletos ? 'hecho' : 'bloqueado', $docsCompletos ? '¡Listo para emitir!' : 'Al aprobar todo'],
    ];
    $pasoActual = collect($pasos)->search(fn ($p) => $p[1] !== 'hecho');

    $ico = [
        'proyecto'  => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z',
        'check'     => 'm5 13 4 4L19 7',
        'reloj'     => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'x'         => 'M6 6l12 12M18 6 6 18',
        'doc'       => 'M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13h6m-6 4h4',
        'actividad' => 'M4 12h4l3-8 4 16 3-8h2',
        'enlace'    => 'M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1',
        'calendario'=> 'M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'lugar'     => 'M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        'persona'   => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0',
        'birrete'   => 'M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5',
        'mas'       => 'M12 5v14M5 12h14',
        'flecha'    => 'M5 12h14m-5-5 5 5-5 5',
        'medalla'   => 'M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3 2.5L8 22l4-2 4 2-1-4.5',
    ];
@endphp

<x-app-layout>
    <style>
        /* ---------- Hero ---------- */
        .ed-hero { align-items: center; }
        .ed-hero-info { flex: 1 1 380px; }
        .ed-carrera {
            display: inline-flex; align-items: center; gap: 8px; margin-top: 14px;
            padding: 8px 14px; border-radius: 12px; background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2); font-size: 14px; font-weight: 700;
        }
        .ed-carrera svg { width: 18px; height: 18px; color: var(--ui-lima); }
        .ed-hero .ui-hero-acciones { margin-top: 18px; }

        .ed-anillo { position: relative; width: 150px; height: 150px; flex-shrink: 0; margin: 0 auto; }
        .ed-anillo svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .ed-anillo-fondo { stroke: rgba(255,255,255,.15); }
        .ed-anillo-valor { stroke: var(--ui-lima); stroke-linecap: round; transition: stroke-dashoffset 1s ease; filter: drop-shadow(0 0 6px rgba(163,214,92,.6)); }
        .ed-anillo-txt { position: absolute; inset: 0; display: grid; place-items: center; text-align: center; }
        .ed-anillo-txt strong { display: block; font-size: 32px; font-weight: 800; line-height: 1; }
        .ed-anillo-txt small { display: block; font-size: 11.5px; color: #BFE3CF; font-weight: 600; margin-top: 4px; max-width: 90px; }

        /* ---------- Pasos ---------- */
        .ed-pasos { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0; padding: 22px; position: relative; }
        .ed-paso { position: relative; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; padding: 0 8px; }
        .ed-paso::before {
            content: ""; position: absolute; top: 21px; left: -50%; width: 100%; height: 3px;
            background: #E3EAE6; z-index: 0;
        }
        .ed-paso:first-child::before { display: none; }
        .ed-paso.hecho::before, .ed-paso.actual::before { background: linear-gradient(90deg, var(--ui-verde), var(--ui-lima)); }
        .ed-paso-num {
            position: relative; z-index: 1; width: 44px; height: 44px; border-radius: 50%;
            display: grid; place-items: center; font-weight: 800; font-size: 16px;
            background: #F1F5F3; color: #8A9A92; border: 3px solid #fff; box-shadow: 0 0 0 2px #E3EAE6;
        }
        .ed-paso-num svg { width: 20px; height: 20px; }
        .ed-paso.hecho .ed-paso-num { background: var(--ui-verde); color: #fff; box-shadow: 0 0 0 2px var(--ui-verde); }
        .ed-paso.actual .ed-paso-num { background: var(--ui-lima); color: var(--ui-verde-noche); box-shadow: 0 0 0 2px var(--ui-lima), 0 0 0 8px rgba(163,214,92,.25); animation: ed-latido 2s infinite; }
        .ed-paso > div { display: flex; flex-direction: column; gap: 2px; }
        .ed-paso strong { font-size: 13.5px; color: var(--ui-texto); }
        .ed-paso span { font-size: 12px; color: var(--ui-texto-3); font-weight: 600; }
        .ed-paso.actual span { color: var(--ui-verde); }
        @keyframes ed-latido { 50% { box-shadow: 0 0 0 2px var(--ui-lima), 0 0 0 12px rgba(163,214,92,.1); } }

        /* ---------- Rejillas ---------- */
        .ed-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 20px; margin-top: 20px; align-items: start; }
        .ed-col { display: flex; flex-direction: column; gap: 20px; }

        /* ---------- Actividades asignadas ---------- */
        .ma-item { display: grid; grid-template-columns: 60px minmax(0, 1fr) auto; gap: 16px; align-items: start; padding: 18px 22px; transition: background .15s; }
        .ma-item:hover { background: #F8FBF9; }
        .ma-item + .ma-item { border-top: 1px solid #EEF2F0; }
        .ma-fecha { width: 60px; border-radius: 14px; overflow: hidden; text-align: center; background: #fff; border: 1px solid var(--ui-borde); box-shadow: 0 4px 10px -6px rgba(0,0,0,.2); }
        .ma-fecha span { display: block; background: var(--ui-verde); color: #fff; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 0; }
        .ma-fecha strong { display: block; font-size: 22px; font-weight: 800; color: var(--ui-texto); padding: 4px 0 6px; line-height: 1.1; }
        .ma-titulo { margin: 0; font-size: 15.5px; font-weight: 800; color: var(--ui-texto); }
        .ma-desc { margin: 4px 0 10px; font-size: 13.5px; color: var(--ui-texto-2); }
        .ma-meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .ma-meta span { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--ui-texto-2); background: #F4F8F6; padding: 5px 10px; border-radius: 99px; }
        .ma-meta svg { width: 14px; height: 14px; color: var(--ui-verde); flex-shrink: 0; }
        .ma-lado { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
        .ma-horas { font-size: 22px; font-weight: 800; color: var(--ui-verde); line-height: 1; white-space: nowrap; }
        .ma-horas small { font-size: 12px; color: var(--ui-texto-3); font-weight: 600; }
        .ma-comentario { margin-top: 10px; padding: 10px 12px; border-radius: 12px; background: #FFFBEB; border-left: 4px solid #F59E0B; font-size: 13px; color: #78350F; }

        /* ---------- Actividad del catálogo ---------- */
        .ed-vinc { display: flex; gap: 14px; align-items: flex-start; }
        .ed-vinc-ico { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; flex-shrink: 0; }
        .ed-vinc-ico svg { width: 22px; height: 22px; }
        .ed-vinc-ico.ok { background: #DCFCE7; color: #15803D; }
        .ed-vinc-ico.espera { background: #FEF3C7; color: #B45309; }
        .ed-vinc p { margin: 0; }
        .ed-vinc-titulo { font-size: 15px; font-weight: 800; color: var(--ui-texto); }
        .ed-vinc-sub { font-size: 13px; color: var(--ui-texto-3); margin-top: 2px !important; }
        .ed-vinc-desc { font-size: 13px; color: var(--ui-texto-2); margin-top: 6px !important; }
        .ed-vinc + .ed-vinc { margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--ui-borde); }

        /* ---------- Mis proyectos ---------- */
        .ed-proy { display: flex; align-items: center; gap: 12px; padding: 14px 22px; }
        .ed-proy + .ed-proy { border-top: 1px solid #EEF2F0; }
        .ed-proy-ini { width: 42px; height: 42px; border-radius: 13px; display: grid; place-items: center; flex-shrink: 0; font-weight: 800; color: #fff; background: linear-gradient(135deg, var(--ui-verde-2), var(--ui-verde)); }
        .ed-proy-info { flex: 1; min-width: 0; }
        .ed-proy-info strong { display: block; font-size: 14px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ed-proy-info small { font-size: 12px; color: var(--ui-texto-3); }

        /* ---------- Documentos ---------- */
        .ed-doc { display: flex; align-items: center; gap: 12px; padding: 12px 22px; }
        .ed-doc + .ed-doc { border-top: 1px solid #EEF2F0; }
        .ed-doc-cod { font-size: 11px; font-weight: 800; color: var(--ui-verde); background: var(--ui-menta); padding: 5px 8px; border-radius: 8px; flex-shrink: 0; }
        .ed-doc-info { flex: 1; min-width: 0; }
        .ed-doc-info strong { display: block; font-size: 13.5px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ed-doc-info small { font-size: 12px; color: var(--ui-texto-3); }

        /* ---------- Proyectos disponibles ---------- */
        .ed-disp { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; padding: 20px 22px 22px; }
        .ed-disp-card {
            display: flex; flex-direction: column; gap: 12px; padding: 18px; border-radius: 18px;
            border: 1px solid var(--ui-borde); background: linear-gradient(180deg, #FFFFFF, #F8FBF9);
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .ed-disp-card:hover { transform: translateY(-4px); box-shadow: var(--ui-sombra-hover); border-color: var(--ui-menta-2); }
        .ed-disp-card h3 { margin: 0; font-size: 16px; font-weight: 800; color: var(--ui-texto); }
        .ed-disp-card p { margin: 0; font-size: 13.5px; color: var(--ui-texto-2); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .ed-disp-doc { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--ui-texto-2); font-weight: 600; margin-top: auto; padding-top: 12px; border-top: 1px dashed var(--ui-borde); }
        .ed-disp-doc svg { width: 16px; height: 16px; color: var(--ui-verde); }
        .ed-disp-card form, .ed-disp-card .ui-btn { width: 100%; }

        @media (max-width: 1100px) { .ed-grid { grid-template-columns: 1fr; } }
        @media (max-width: 760px) {
            .ed-pasos { grid-template-columns: 1fr; gap: 14px; padding: 18px; }
            .ed-paso { flex-direction: row; text-align: left; align-items: center; gap: 12px; padding: 0; }
            .ed-paso::before { display: none; }
            .ma-item { grid-template-columns: 52px minmax(0, 1fr); padding: 16px; }
            .ma-fecha { width: 52px; }
            .ma-lado { grid-column: 1 / -1; flex-direction: row; justify-content: space-between; align-items: center; flex-wrap: wrap; }
            .ed-anillo { width: 130px; height: 130px; }
        }

        /* ---------- Mi actividad (destacada) ---------- */
        .ed-mia { display: grid; grid-template-columns: 88px minmax(0, 1fr) 150px; gap: 22px; align-items: center; padding: 22px; }
        .ed-mia-fecha { width: 88px; border-radius: 20px; overflow: hidden; text-align: center; background: #fff; border: 1px solid var(--ui-borde); box-shadow: 0 10px 20px -12px rgba(0,0,0,.35); }
        .ed-mia-fecha span { display: block; background: linear-gradient(135deg, var(--ui-verde-2), var(--ui-verde)); color: #fff; font-size: 12.5px; font-weight: 800; text-transform: uppercase; padding: 6px 0; }
        .ed-mia-fecha strong { display: block; font-size: 34px; font-weight: 800; color: var(--ui-texto); padding: 8px 0 0; line-height: 1; }
        .ed-mia-fecha small { display: block; font-size: 12px; color: var(--ui-texto-3); padding: 2px 0 8px; }
        .ed-mia-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
        .ed-mia-info h4 { margin: 0; font-size: 19px; font-weight: 800; color: var(--ui-texto); }
        .ed-mia-info p { margin: 6px 0 12px; font-size: 14px; color: var(--ui-texto-2); }
        .ed-mia-avance { margin-top: 14px; max-width: 520px; }
        .ed-mia-avance small { display: block; margin-top: 6px; font-size: 12px; color: var(--ui-texto-3); font-weight: 600; }
        .ed-mia-horas { text-align: center; padding: 18px 10px; border-radius: 20px; background: linear-gradient(135deg, var(--ui-menta), #F5FBF8); }
        .ed-mia-horas strong { display: block; font-size: 38px; font-weight: 800; color: var(--ui-verde); line-height: 1; letter-spacing: -.03em; }
        .ed-mia-horas span { font-size: 12.5px; color: var(--ui-texto-2); font-weight: 700; }
        .ed-mia-vacio { display: flex; align-items: center; gap: 18px; padding: 24px 22px; flex-wrap: wrap; }
        .ed-mia-vacio .ui-vacio-ico { margin: 0; flex-shrink: 0; }
        .ed-mia-vacio > div:nth-child(2) { flex: 1 1 260px; }
        .ed-mia-vacio strong { display: block; font-size: 16px; color: var(--ui-texto); }
        .ed-mia-vacio p { margin: 4px 0 0; font-size: 14px; color: var(--ui-texto-2); }

        .ed-dos { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-top: 20px; align-items: stretch; }
        a.ed-disp-card { text-decoration: none !important; color: inherit !important; }

        @media (max-width: 900px) {
            .ed-dos { grid-template-columns: 1fr; }
            .ed-mia { grid-template-columns: 64px minmax(0, 1fr); padding: 18px; gap: 14px; }
            .ed-mia-fecha { width: 64px; }
            .ed-mia-fecha strong { font-size: 26px; }
            .ed-mia-horas { grid-column: 1 / -1; display: flex; align-items: baseline; justify-content: center; gap: 8px; padding: 12px; }
            .ed-mia-horas strong { font-size: 28px; }
        }
    </style>

    <div class="ui-wrap">

        {{-- ===== Hero ===== --}}
        <section class="ui-hero ed-hero">
            <div class="ed-hero-info">
                <span class="ui-hero-eyebrow">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['birrete'] }}"/></svg>
                    Mi vinculación
                </span>
                <h1>{{ $saludo }}, {{ $primerNombre }} 👋</h1>
                <p>Aquí ves tu avance en la vinculación con la sociedad: tus proyectos, actividades y documentos.</p>
                <div class="ed-carrera">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['birrete'] }}"/></svg>
                    {{ $usuario->carrera->nombre ?? 'Sin carrera asignada' }}
                </div>
                <div class="ui-hero-acciones">
                    <a href="{{ route('certificados-estudiante.index') }}" class="ui-btn ui-btn-blanco">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['doc'] }}"/></svg>
                        Mis documentos
                    </a>
                    <a href="{{ route('estudiante.proyectos.index') }}" class="ui-btn ui-btn-vidrio">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['proyecto'] }}"/></svg>
                        Proyectos disponibles
                    </a>
                </div>
            </div>

            <div class="ed-anillo" role="img" aria-label="Documentos aprobados: {{ $aprobados }} de {{ $totalDocs }}">
                <svg viewBox="0 0 120 120">
                    <circle class="ed-anillo-fondo" cx="60" cy="60" r="{{ $radio }}" fill="none" stroke-width="10"/>
                    <circle class="ed-anillo-valor" cx="60" cy="60" r="{{ $radio }}" fill="none" stroke-width="10"
                            stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $trazo }}"/>
                </svg>
                <div class="ed-anillo-txt">
                    <div>
                        <strong>{{ $porcentajeDocs }}%</strong>
                        <small>{{ $aprobados }} de {{ $totalDocs }} documentos</small>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Indicadores ===== --}}
        <section class="ui-kpis" style="margin-top: 22px;">
            @foreach ([
                ['Proyectos inscritos', $inscripciones->count(), '', 'proyecto', route('estudiante.proyectos.index')],
                ['Documentos aprobados', $aprobados . ' / ' . $totalDocs, 'lima', 'check', route('certificados-estudiante.index')],
                ['Pendientes de revisión', $pendientes, 'ambar', 'reloj', route('certificados-estudiante.index')],
                ['Rechazados', $rechazados, 'rosa', 'x', route('certificados-estudiante.index')],
            ] as [$label, $valor, $color, $icono, $url])
                <a href="{{ $url }}" class="ui-kpi {{ $color }}">
                    <div class="ui-kpi-top">
                        <span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico[$icono] }}"/></svg></span>
                        <span class="ui-kpi-flecha"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/></svg></span>
                    </div>
                    <div class="ui-kpi-valor">{{ $valor }}</div>
                    <div class="ui-kpi-label">{{ $label }}</div>
                </a>
            @endforeach
        </section>

        {{-- ===== Pasos ===== --}}
        <section class="ui-panel" style="margin-top: 20px;">
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg></span> Tu camino de vinculación</h3>
            </div>
            <div class="ed-pasos">
                @foreach ($pasos as $i => [$titulo, $estado, $detalle])
                    <div class="ed-paso {{ $estado }}">
                        <span class="ed-paso-num">
                            @if ($estado === 'hecho')
                                <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['check'] }}"/></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </span>
                        <div>
                            <strong>{{ $titulo }}</strong>
                            <span>{{ $detalle }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ===== Mi actividad ===== --}}
        <section class="ui-panel" style="margin-top: 20px;">
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividad'] }}"/></svg></span> Mi actividad</h3>
                @if ($miActividad)
                    <a href="{{ route('estudiante.proyectos.show', $miActividad->proyecto_vinculacion_id) }}">Ver proyecto →</a>
                @endif
            </div>

            @if ($miActividad)
                @php
                    $inicioAct = $miActividad->fecha_inicio ?? $miActividad->fecha;
                    $finAct = $miActividad->fecha_finalizacion ?? $inicioAct;
                    if ($inicioAct && $hoy->lt($inicioAct)) { $tiempo = ['Próxima', 'ambar']; }
                    elseif ($finAct && $hoy->gt($finAct)) { $tiempo = ['Finalizada', 'gris']; }
                    else { $tiempo = ['En curso', 'verde']; }
                    $diasTotal = ($inicioAct && $finAct) ? max(1, $inicioAct->diffInDays($finAct)) : 1;
                    $avanceAct = (!$inicioAct || $hoy->lt($inicioAct)) ? 0 : ($hoy->gt($finAct) ? 100 : (int) round($inicioAct->diffInDays($hoy) / $diasTotal * 100));
                @endphp
                <div class="ed-mia">
                    <div class="ed-mia-fecha" aria-hidden="true">
                        <span>{{ $inicioAct?->translatedFormat('M') ?? '—' }}</span>
                        <strong>{{ $inicioAct?->format('d') ?? '—' }}</strong>
                        <small>{{ $inicioAct?->format('Y') }}</small>
                    </div>
                    <div class="ed-mia-info">
                        <div class="ed-mia-tags">
                            <span class="ui-tag {{ $tiempo[1] }}">{{ $tiempo[0] }}</span>
                            @if ($miActividad->estado === 'aprobada')
                                <span class="ui-tag verde">Aprobada</span>
                            @else
                                <span class="ui-tag ambar">{{ ucfirst($miActividad->estado) }}</span>
                            @endif
                        </div>
                        <h4>{{ $miActividad->proyecto->nombre ?? 'Actividad de vinculación' }}</h4>
                        @if ($miActividad->descripcion)
                            <p>{{ $miActividad->descripcion }}</p>
                        @endif
                        <div class="ma-meta">
                            <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['calendario'] }}"/></svg>{{ $inicioAct?->format('d/m/Y') ?? '—' }} → {{ $finAct?->format('d/m/Y') ?? '—' }}</span>
                            @if ($miActividad->lugar)
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['lugar'] }}"/></svg>{{ $miActividad->lugar }}</span>
                            @endif
                            @if ($miActividad->docente)
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['persona'] }}"/></svg>{{ $miActividad->docente->name }}</span>
                            @endif
                        </div>
                        <div class="ed-mia-avance">
                            <div class="ui-progreso"><span style="width: {{ $avanceAct }}%"></span></div>
                            <small>{{ $avanceAct }}% del tiempo de la actividad transcurrido</small>
                        </div>
                        @if ($miActividad->comentario_docente)
                            <div class="ma-comentario"><strong>Comentario del docente:</strong> {{ $miActividad->comentario_docente }}</div>
                        @endif
                    </div>
                    <div class="ed-mia-horas">
                        <strong>{{ $formatoHoras($miActividad->horas) }}</strong>
                        <span>horas de vinculación</span>
                    </div>
                </div>
            @else
                <div class="ed-mia-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['actividad'] }}"/></svg></div>
                    <div>
                        <strong>Todavía no te inscribes en una actividad</strong>
                        <p>Entra a un proyecto disponible y escoge la actividad en la que quieres participar. Solo puedes elegir una.</p>
                    </div>
                    <a href="{{ route('estudiante.proyectos.index') }}" class="ui-btn ui-btn-primario">
                        Ver proyectos disponibles
                        <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg>
                    </a>
                </div>
            @endif
        </section>

        {{-- ===== Proyectos + documentos (mismo ancho) ===== --}}
        <section class="ed-dos">
            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['proyecto'] }}"/></svg></span> Mis proyectos</h3>
                </div>
                @forelse ($inscripciones as $inscripcion)
                    @php
                        $estadoInsc = $inscripcion->estado ?? 'activo';
                        $colorInsc = ['activo' => 'verde', 'completado' => 'verde', 'retirado' => 'gris'][$estadoInsc] ?? 'gris';
                    @endphp
                    <div class="ed-proy">
                        <span class="ed-proy-ini">{{ mb_strtoupper(mb_substr($inscripcion->proyecto->nombre ?? 'P', 0, 1)) }}</span>
                        <div class="ed-proy-info">
                            <strong>{{ $inscripcion->proyecto->nombre ?? 'Proyecto' }}</strong>
                            <small>{{ $inscripcion->proyecto->docente->name ?? 'Sin docente' }} · desde {{ $inscripcion->created_at?->format('d/m/Y') }}</small>
                        </div>
                        <span class="ui-tag {{ $colorInsc }}">{{ ucfirst($estadoInsc) }}</span>
                    </div>
                @empty
                    <div class="ui-vacio">
                        <strong>Aún no participas en ningún proyecto</strong>
                        Al inscribirte en una actividad quedarás inscrito en su proyecto.
                    </div>
                @endforelse
            </div>

            <div class="ui-panel">
                <div class="ui-panel-head">
                    <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['doc'] }}"/></svg></span> Últimos documentos</h3>
                    <a href="{{ route('certificados-estudiante.index') }}">Ver todos →</a>
                </div>
                @forelse ($ultimosCertificados as $certificado)
                    <div class="ed-doc">
                        <span class="ed-doc-cod">{{ $certificado->tipoCertificado->codigo }}</span>
                        <div class="ed-doc-info">
                            <strong>{{ $certificado->tipoCertificado->nombre }}</strong>
                            <small>{{ $certificado->updated_at->format('d/m/Y') }}</small>
                        </div>
                        @if ($certificado->estado === 'aprobado')
                            <span class="ui-tag verde">Aprobado</span>
                        @elseif ($certificado->estado === 'pendiente')
                            <span class="ui-tag ambar">Pendiente</span>
                        @else
                            <span class="ui-tag rojo">Rechazado</span>
                        @endif
                    </div>
                @empty
                    <div class="ui-vacio">
                        <strong>Aún no subes documentos</strong>
                        <a href="{{ route('certificados-estudiante.index') }}" class="ui-btn ui-btn-primario ui-btn-sm" style="margin-top: 10px;">Subir documentos</a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ===== Proyectos disponibles ===== --}}
        <section class="ui-panel" style="margin-top: 20px;">
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['mas'] }}"/></svg></span> Proyectos disponibles</h3>
                <a href="{{ route('estudiante.proyectos.index') }}">Ver todos →</a>
            </div>
            @if ($proyectosDisponibles->isEmpty())
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['proyecto'] }}"/></svg></div>
                    <strong>No hay proyectos disponibles por ahora</strong>
                    Cuando el administrador publique proyectos aparecerán aquí.
                </div>
            @else
                <div class="ed-disp">
                    @foreach ($proyectosDisponibles->take(6) as $proyecto)
                        <a href="{{ route('estudiante.proyectos.show', $proyecto) }}" class="ed-disp-card">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                                <h3>{{ $proyecto->nombre }}</h3>
                                @if (in_array($proyecto->id, $proyectosInscritos))
                                    <span class="ui-tag verde">Inscrito</span>
                                @endif
                            </div>
                            <p>{{ $proyecto->descripcion ?: 'Sin descripción disponible.' }}</p>
                            <div class="ed-disp-doc">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['persona'] }}"/></svg>
                                {{ $proyecto->docente->name ?? 'Docente por asignar' }}
                            </div>
                            <span class="ui-btn ui-btn-primario" style="width:100%">
                                Ver {{ $proyecto->actividades_disponibles_count }} {{ $proyecto->actividades_disponibles_count === 1 ? 'actividad' : 'actividades' }}
                                <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico['flecha'] }}"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
