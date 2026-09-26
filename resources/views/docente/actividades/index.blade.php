@php
    $hoy = \Carbon\Carbon::today();
    $formatoHoras = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.');
    $filtros = ['todas' => 'Todas', 'en_curso' => 'En curso', 'proximas' => 'Próximas', 'finalizadas' => 'Finalizadas'];
@endphp

<x-app-layout>
    <style>
        .da-lista { display: flex; flex-direction: column; gap: 18px; }
        .da-card { background: #fff; border-radius: 22px; border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra); overflow: hidden; }
        .da-cab { display: grid; grid-template-columns: 76px minmax(0, 1fr) auto; gap: 18px; align-items: center; padding: 20px 22px; }
        .da-fecha { width: 76px; border-radius: 18px; overflow: hidden; text-align: center; background: #fff; border: 1px solid var(--ui-borde); box-shadow: 0 6px 14px -8px rgba(0,0,0,.25); }
        .da-fecha span { display: block; background: var(--ui-verde); color: #fff; font-size: 12px; font-weight: 800; text-transform: uppercase; padding: 5px 0; }
        .da-fecha strong { display: block; font-size: 28px; font-weight: 800; color: var(--ui-texto); padding: 6px 0 2px; line-height: 1; }
        .da-fecha small { display: block; font-size: 11px; color: var(--ui-texto-3); padding-bottom: 6px; }
        .da-info h3 { margin: 0 0 4px; font-size: 17px; font-weight: 800; color: var(--ui-texto); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .da-info p { margin: 0 0 10px; font-size: 13.5px; color: var(--ui-texto-2); }
        .da-meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .da-meta span { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: var(--ui-texto-2); background: #F4F8F6; padding: 5px 10px; border-radius: 99px; }
        .da-meta svg { width: 14px; height: 14px; color: var(--ui-verde); }
        .da-horas { text-align: center; padding: 14px 18px; border-radius: 18px; background: linear-gradient(135deg, var(--ui-menta), #F5FBF8); }
        .da-horas strong { display: block; font-size: 28px; font-weight: 800; color: var(--ui-verde); line-height: 1; }
        .da-horas small { font-size: 12px; color: var(--ui-texto-2); font-weight: 700; }
        .da-est-cab { display: flex; align-items: center; justify-content: space-between; padding: 12px 22px; background: #F8FBF9; border-top: 1px solid var(--ui-borde); font-size: 13px; font-weight: 800; color: var(--ui-texto-2); }
        .da-ests { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); }
        .da-est { display: flex; align-items: center; gap: 10px; padding: 12px 22px; border-top: 1px solid #EEF2F0; text-decoration: none !important; transition: background .15s; }
        .da-est:hover { background: #F4FAF7; }
        .da-est .ui-avatar { width: 36px; height: 36px; font-size: 12.5px; }
        .da-est strong { display: block; font-size: 13.5px; color: var(--ui-texto); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .da-est small { display: block; font-size: 12px; color: var(--ui-texto-3); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        @media (max-width: 760px) {
            .da-cab { grid-template-columns: 62px minmax(0, 1fr); }
            .da-fecha { width: 62px; }
            .da-horas { grid-column: 1 / -1; display: flex; align-items: baseline; justify-content: center; gap: 8px; padding: 10px; }
        }
    </style>

    <div class="ui-wrap">
        <x-ui.hero etiqueta="Mi trabajo" titulo="Mis actividades"
                   subtitulo="Actividades creadas por el administrador en tus proyectos o de las que eres responsable, con los estudiantes inscritos." />

        <div class="ui-barra">
            <nav class="ui-filtros" aria-label="Filtrar actividades">
                @foreach ($filtros as $clave => $texto)
                    <a href="{{ route('docente.actividades.index', $clave === 'todas' ? [] : ['ver' => $clave]) }}"
                       class="ui-filtro {{ $filtro === $clave ? 'is-activo' : '' }}">{{ $texto }} <span>{{ $conteos[$clave] }}</span></a>
                @endforeach
            </nav>
        </div>

        @forelse ($actividades as $act)
            @php
                $inicio = $act->fecha_inicio ?? $act->fecha;
                $fin = $act->fecha_finalizacion ?? $inicio;
                $tiempo = ($inicio && $hoy->lt($inicio)) ? ['Próxima', 'ambar'] : (($fin && $hoy->gt($fin)) ? ['Finalizada', 'gris'] : ['En curso', 'verde']);
                $soyResponsable = (int) $act->docente_id === (int) $docenteId;
            @endphp
            <article class="da-card" style="margin-bottom: 18px;">
                <div class="da-cab">
                    <div class="da-fecha" aria-hidden="true">
                        <span>{{ $inicio?->translatedFormat('M') ?? '—' }}</span>
                        <strong>{{ $inicio?->format('d') ?? '—' }}</strong>
                        <small>{{ $inicio?->format('Y') }}</small>
                    </div>
                    <div class="da-info" style="min-width:0">
                        <h3>
                            {{ $act->proyecto->nombre ?? 'Actividad' }}
                            <span class="ui-tag {{ $tiempo[1] }}">{{ $tiempo[0] }}</span>
                            <span class="ui-tag {{ $act->estado === 'aprobada' ? 'verde' : 'ambar' }}">{{ ucfirst($act->estado) }}</span>
                        </h3>
                        @if ($act->descripcion) <p>{{ $act->descripcion }}</p> @endif
                        <div class="da-meta">
                            <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>{{ $inicio?->format('d/m/Y') ?? '—' }} → {{ $fin?->format('d/m/Y') ?? '—' }}</span>
                            @if ($act->lugar)
                                <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>{{ $act->lugar }}</span>
                            @endif
                            <span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>{{ $soyResponsable ? 'Tú eres el responsable' : 'Responsable: ' . ($act->docente->name ?? '—') }}</span>
                            @if ($act->proyecto?->periodoAcademico)
                                <span>Período {{ $act->proyecto->periodoAcademico->nombre }}</span>
                            @endif
                        </div>
                        @if ($act->comentario_docente)
                            <div style="margin-top:10px;padding:10px 12px;border-radius:12px;background:#FFFBEB;border-left:4px solid #F59E0B;font-size:13px;color:#78350F"><strong>Comentario:</strong> {{ $act->comentario_docente }}</div>
                        @endif
                    </div>
                    <div class="da-horas">
                        <strong>{{ $formatoHoras($act->horas) }}</strong>
                        <small>horas</small>
                    </div>
                </div>

                <div class="da-est-cab">
                    <span>Estudiantes inscritos</span>
                    <span class="ui-tag gris">{{ $act->inscripciones->count() }}</span>
                </div>
                @if ($act->inscripciones->isEmpty())
                    <div class="ui-vacio" style="padding:18px">Aún no se inscribe ningún estudiante en esta actividad.</div>
                @else
                    <div class="da-ests">
                        @foreach ($act->inscripciones as $inscripcion)
                            @php
                                $nombreEst = $inscripcion->estudiante->name ?? 'Estudiante';
                                $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                            @endphp
                            <a href="{{ route('docente.certificados.show', $inscripcion->id) }}" class="da-est" title="Ver documentos de {{ $nombreEst }}">
                                <span class="ui-avatar">{{ $ini }}</span>
                                <div style="min-width:0">
                                    <strong>{{ $nombreEst }}</strong>
                                    <small>{{ $inscripcion->estudiante->carrera->nombre ?? 'Sin carrera' }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <section class="ui-panel">
                <div class="ui-vacio" style="padding: 44px 20px;">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg></div>
                    <strong>{{ $filtro === 'todas' ? 'Aún no tienes actividades' : 'No hay actividades en este filtro' }}</strong>
                    Las actividades las crea el administrador en tus proyectos o te asigna como responsable.
                </div>
            </section>
        @endforelse
    </div>
</x-app-layout>
