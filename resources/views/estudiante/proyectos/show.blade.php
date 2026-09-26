@php
    $formatoHoras = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.');
    $hoy = \Carbon\Carbon::today();
@endphp

<x-app-layout>
    <style>
        .pa-datos { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
        .pa-datos span {
            display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 12px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); font-size: 13.5px; font-weight: 600;
        }
        .pa-datos svg { width: 16px; height: 16px; color: var(--ui-lima); }

        .pa-aviso {
            margin-top: 20px; display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 18px;
            background: #FFF8EB; border: 1px solid #FDE7B0; color: #92400E;
        }
        .pa-aviso svg { width: 26px; height: 26px; flex-shrink: 0; }
        .pa-aviso p { margin: 0; font-size: 14px; }

        .pa-titulo { margin: 28px 0 14px; font-size: 18px; font-weight: 800; color: var(--ui-texto); display: flex; align-items: center; gap: 10px; }

        .pa-lista { display: flex; flex-direction: column; gap: 16px; }
        .pa-act {
            display: grid; grid-template-columns: 76px minmax(0, 1fr) 210px; gap: 20px; align-items: center;
            padding: 20px 22px; background: #fff; border-radius: 22px; border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra);
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .pa-act:hover { transform: translateY(-3px); box-shadow: var(--ui-sombra-hover); }
        .pa-act.es-mia { border: 2px solid var(--ui-verde); background: linear-gradient(180deg, #FFFFFF, #F3FBF7); }
        .pa-fecha { width: 76px; border-radius: 18px; overflow: hidden; text-align: center; background: #fff; border: 1px solid var(--ui-borde); box-shadow: 0 6px 14px -8px rgba(0,0,0,.25); }
        .pa-fecha span { display: block; background: var(--ui-verde); color: #fff; font-size: 12px; font-weight: 800; text-transform: uppercase; padding: 5px 0; }
        .pa-fecha strong { display: block; font-size: 28px; font-weight: 800; color: var(--ui-texto); padding: 6px 0 2px; line-height: 1; }
        .pa-fecha small { display: block; font-size: 11px; color: var(--ui-texto-3); padding-bottom: 6px; }
        .pa-info h3 { margin: 0 0 6px; font-size: 16px; font-weight: 800; color: var(--ui-texto); }
        .pa-info p { margin: 0 0 12px; font-size: 14px; color: var(--ui-texto-2); }
        .pa-meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .pa-meta span { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: var(--ui-texto-2); background: #F4F8F6; padding: 5px 10px; border-radius: 99px; }
        .pa-meta svg { width: 14px; height: 14px; color: var(--ui-verde); flex-shrink: 0; }
        .pa-lado { display: flex; flex-direction: column; align-items: stretch; gap: 10px; text-align: center; }
        .pa-horas strong { display: block; font-size: 30px; font-weight: 800; color: var(--ui-verde); line-height: 1; }
        .pa-horas span { font-size: 12.5px; color: var(--ui-texto-3); font-weight: 600; }
        .pa-lado form, .pa-lado .ui-btn { width: 100%; }
        .pa-mia { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px; border-radius: 13px; background: var(--ui-verde); color: #fff; font-weight: 800; font-size: 14px; }
        .pa-mia svg { width: 18px; height: 18px; }

        @media (max-width: 860px) {
            .pa-act { grid-template-columns: 64px minmax(0, 1fr); padding: 18px; gap: 14px; }
            .pa-fecha { width: 64px; }
            .pa-fecha strong { font-size: 24px; }
            .pa-lado { grid-column: 1 / -1; flex-direction: row; align-items: center; text-align: left; }
            .pa-horas { flex-shrink: 0; }
            .pa-lado form, .pa-lado .ui-btn, .pa-mia { flex: 1; }
        }
    </style>

    <div class="ui-wrap">
        <section class="ui-hero">
            <div style="flex: 1 1 420px;">
                <a href="{{ route('estudiante.proyectos.index') }}" class="ui-hero-eyebrow" style="text-decoration:none;color:var(--ui-lima)">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
                    Proyectos disponibles
                </a>
                <h1>{{ $proyecto->nombre }}</h1>
                @if ($proyecto->descripcion)
                    <p>{{ $proyecto->descripcion }}</p>
                @endif
                <div class="pa-datos">
                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>{{ $proyecto->docente->name ?? 'Docente por asignar' }}</span>
                    @if ($proyecto->periodoAcademico)
                        <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>Período {{ $proyecto->periodoAcademico->nombre }}</span>
                    @endif
                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg>{{ $actividades->count() }} {{ $actividades->count() === 1 ? 'actividad' : 'actividades' }}</span>
                </div>
            </div>
        </section>

        @if ($puedeOtra && $miActividad)
            <div class="pa-aviso" role="status" style="background:#EEF9F3;border-color:#C9EBD8;color:#065F46;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p><strong>El administrador te dio permiso</strong> para inscribirte en una actividad más. El permiso se usa una sola vez.</p>
            </div>
        @elseif ($miActividad)
            <div class="pa-aviso" role="status">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                <p>Ya estás inscrito en la actividad de <strong>{{ $miActividad->proyecto->nombre ?? 'un proyecto' }}</strong>. Para inscribirte en otra necesitas el permiso del administrador.</p>
            </div>
        @endif

        <h2 class="pa-titulo">
            <span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg></span>
            Actividades disponibles
        </h2>

        @if ($actividades->isEmpty())
            <div class="ui-panel">
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg></div>
                    <strong>Este proyecto aún no tiene actividades abiertas</strong>
                    Vuelve más tarde o revisa otros proyectos.
                    <div style="margin-top: 12px;"><a href="{{ route('estudiante.proyectos.index') }}" class="ui-btn ui-btn-primario ui-btn-sm">Ver otros proyectos</a></div>
                </div>
            </div>
        @else
            <div class="pa-lista">
                @foreach ($actividades as $act)
                    @php
                        $inicio = $act->fecha_inicio ?? $act->fecha;
                        $fin = $act->fecha_finalizacion ?? $inicio;
                        $esMia = in_array($act->id, $misActividadIds);
                        $enCurso = $inicio && $hoy->gte($inicio);
                    @endphp
                    <article class="pa-act {{ $esMia ? 'es-mia' : '' }}">
                        <div class="pa-fecha" aria-hidden="true">
                            <span>{{ $inicio?->translatedFormat('M') ?? '—' }}</span>
                            <strong>{{ $inicio?->format('d') ?? '—' }}</strong>
                            <small>{{ $inicio?->format('Y') }}</small>
                        </div>

                        <div class="pa-info">
                            <h3>
                                Actividad del {{ $inicio?->format('d/m/Y') ?? '—' }}
                                <span class="ui-tag {{ $enCurso ? 'verde' : 'ambar' }}" style="margin-left:6px;vertical-align:middle">{{ $enCurso ? 'En curso' : 'Próxima' }}</span>
                            </h3>
                            <p>{{ $act->descripcion }}</p>
                            <div class="pa-meta">
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>{{ $inicio?->format('d/m/Y') ?? '—' }} → {{ $fin?->format('d/m/Y') ?? '—' }}</span>
                                @if ($act->lugar)
                                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>{{ $act->lugar }}</span>
                                @endif
                                @if ($act->docente)
                                    <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>{{ $act->docente->name }}</span>
                                @endif
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m10-12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/></svg>{{ $act->inscripciones_count }} {{ $act->inscripciones_count === 1 ? 'inscrito' : 'inscritos' }}</span>
                            </div>
                        </div>

                        <div class="pa-lado">
                            <div class="pa-horas">
                                <strong>{{ $formatoHoras($act->horas) }}</strong>
                                <span>horas de vinculación</span>
                            </div>

                            @if ($esMia)
                                <span class="pa-mia">
                                    <svg fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    Tu actividad
                                </span>
                            @elseif ($miActividad && ! $puedeOtra)
                                {{-- Ya tiene una actividad y no tiene permiso: el botón muestra el aviso de denegado --}}
                                <button type="button" class="ui-btn ui-btn-primario"
                                        onclick="Swal.fire({ icon: 'error', title: 'Inscripción denegada', text: @js('Ya estás inscrito en la actividad de «' . ($miActividad->proyecto->nombre ?? 'otro proyecto') . '». Para inscribirte en otra, el administrador debe darte permiso.'), confirmButtonText: 'Entendido' })">
                                    Inscribirme
                                </button>
                            @else
                                <form action="{{ route('estudiante.actividades.inscribirse', $act) }}" method="POST"
                                      data-confirm-title="¿Inscribirte en esta actividad?"
                                      data-confirm-text="{{ $miActividad ? 'Usarás el permiso que te dio el administrador para inscribirte en otra actividad.' : 'Solo puedes participar en una actividad. Después de inscribirte no podrás elegir otra sin permiso del administrador.' }}"
                                      data-confirm-button="Sí, inscribirme">
                                    @csrf
                                    <button type="submit" class="ui-btn ui-btn-primario">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                                        Inscribirme
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
