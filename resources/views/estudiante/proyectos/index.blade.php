@php
    $paletas = [
        ['#006B47', '#A3D65C'], ['#2563EB', '#60A5FA'], ['#7C3AED', '#C4B5FD'],
        ['#D97706', '#FCD34D'], ['#DB2777', '#F9A8D4'], ['#0891B2', '#67E8F9'],
    ];
@endphp

<x-app-layout>
    <style>
        .pd-aviso {
            margin-top: 20px; display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 18px;
            background: #EEF9F3; border: 1px solid #C9EBD8; color: #065F46;
        }
        .pd-aviso svg { width: 26px; height: 26px; flex-shrink: 0; }
        .pd-aviso p { margin: 0; font-size: 14px; }
        .pd-aviso a { margin-left: auto; }

        .pd-grid { margin-top: 22px; display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 20px; }
        .pd-card {
            display: flex; flex-direction: column; background: #fff; border-radius: 22px; overflow: hidden;
            border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra); text-decoration: none !important; color: inherit !important;
            transition: transform .22s, box-shadow .22s;
        }
        .pd-card:hover { transform: translateY(-5px); box-shadow: var(--ui-sombra-hover); }
        .pd-cab {
            position: relative; padding: 20px; min-height: 120px; color: #fff; overflow: hidden;
            background: linear-gradient(135deg, var(--a), var(--b));
        }
        .pd-cab::after { content: ""; position: absolute; right: -40px; top: -50px; width: 150px; height: 150px; border-radius: 50%; border: 22px solid rgba(255,255,255,.12); }
        .pd-cab-ini { position: relative; z-index: 1; width: 48px; height: 48px; border-radius: 15px; background: rgba(255,255,255,.22); display: grid; place-items: center; font-size: 22px; font-weight: 800; backdrop-filter: blur(4px); }
        .pd-cab .ui-tag { position: absolute; top: 16px; right: 16px; z-index: 1; background: #fff; }
        .pd-cuerpo { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
        .pd-cuerpo h3 { margin: 0; font-size: 17px; font-weight: 800; color: var(--ui-texto); }
        .pd-cuerpo p { margin: 0; font-size: 13.5px; color: var(--ui-texto-2); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .pd-datos { display: flex; flex-wrap: wrap; gap: 6px; }
        .pd-datos span { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--ui-texto-2); background: #F4F8F6; padding: 5px 10px; border-radius: 99px; }
        .pd-datos svg { width: 14px; height: 14px; color: var(--ui-verde); }
        .pd-pie { margin-top: auto; padding-top: 14px; border-top: 1px dashed var(--ui-borde); display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .pd-num strong { font-size: 22px; font-weight: 800; color: var(--ui-verde); }
        .pd-num span { font-size: 12.5px; color: var(--ui-texto-3); font-weight: 600; }
    </style>

    <div class="ui-wrap">
        <section class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">Vinculación con la sociedad</span>
                <h1>Proyectos disponibles</h1>
                <p>Entra a un proyecto para ver sus actividades y escoge <strong>una</strong> para participar.</p>
            </div>
        </section>

        @if ($puedeOtra && $miActividad)
            <div class="pd-aviso" role="status" style="background:#EEF9F3;border-color:#C9EBD8;color:#065F46;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p><strong>El administrador te dio permiso</strong> para inscribirte en una actividad más. El permiso se usa una sola vez.</p>
            </div>
        @elseif ($miActividad)
            <div class="pd-aviso" role="status">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p>Ya estás inscrito en la actividad de <strong>{{ $miActividad->proyecto->nombre ?? 'un proyecto' }}</strong>. Para inscribirte en otra necesitas el permiso del administrador.</p>
                <a href="{{ route('dashboard') }}" class="ui-btn ui-btn-suave ui-btn-sm">Ver mi actividad</a>
            </div>
        @endif

        @if ($proyectos->isEmpty())
            <div class="ui-panel" style="margin-top: 22px;">
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg></div>
                    <strong>Aún no hay proyectos abiertos</strong>
                    Cuando el administrador publique proyectos aparecerán aquí.
                </div>
            </div>
        @else
            <div class="pd-grid">
                @foreach ($proyectos as $proyecto)
                    @php [$a, $b] = $paletas[$proyecto->id % count($paletas)]; @endphp
                    <a href="{{ route('estudiante.proyectos.show', $proyecto) }}" class="pd-card">
                        <div class="pd-cab" style="--a: {{ $a }}; --b: {{ $b }};">
                            <span class="pd-cab-ini">{{ mb_strtoupper(mb_substr($proyecto->nombre, 0, 1)) }}</span>
                            @if (in_array($proyecto->id, $inscritoEn))
                                <span class="ui-tag verde">Inscrito</span>
                            @endif
                        </div>
                        <div class="pd-cuerpo">
                            <h3>{{ $proyecto->nombre }}</h3>
                            <p>{{ $proyecto->descripcion ?: 'Sin descripción disponible.' }}</p>
                            <div class="pd-datos">
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>{{ $proyecto->docente->name ?? 'Docente por asignar' }}</span>
                                @if ($proyecto->periodoAcademico)
                                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>{{ $proyecto->periodoAcademico->nombre }}</span>
                                @endif
                            </div>
                            <div class="pd-pie">
                                <div class="pd-num">
                                    <strong>{{ $proyecto->actividades_disponibles_count }}</strong>
                                    <span>{{ $proyecto->actividades_disponibles_count === 1 ? 'actividad disponible' : 'actividades disponibles' }}</span>
                                </div>
                                <span class="ui-btn ui-btn-primario ui-btn-sm">
                                    Ver actividades
                                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
