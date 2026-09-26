@php
    use Carbon\Carbon;

    $hoy = Carbon::today();
    $totalPeriodos = method_exists($periodos, 'total') ? $periodos->total() : $periodos->count();
    $periodoActivo = collect(method_exists($periodos, 'items') ? $periodos->items() : $periodos)->firstWhere('activo', true);

    // Convierte una fecha a texto legible (ej: 15 mar 2026)
    $formatear = function ($fecha) {
        if (! $fecha) return '—';
        try {
            return Carbon::parse($fecha)->translatedFormat('d M Y');
        } catch (\Throwable $e) {
            return $fecha;
        }
    };

    // Calcula el avance (%) del período según la fecha de hoy
    $avance = function ($periodo) use ($hoy) {
        try {
            $inicio = Carbon::parse($periodo->fecha_inicio);
            $fin = Carbon::parse($periodo->fecha_fin);
        } catch (\Throwable $e) {
            return null;
        }
        $totalDias = max($inicio->diffInDays($fin), 1);
        if ($hoy->lt($inicio)) return 0;
        if ($hoy->gt($fin)) return 100;
        return (int) round($inicio->diffInDays($hoy) / $totalDias * 100);
    };

    // Estado del período según fechas
    $estado = function ($periodo) use ($hoy) {
        try {
            $inicio = Carbon::parse($periodo->fecha_inicio);
            $fin = Carbon::parse($periodo->fecha_fin);
        } catch (\Throwable $e) {
            return ['texto' => 'Sin fechas', 'clase' => 'neutro'];
        }
        if ($hoy->lt($inicio)) return ['texto' => 'Próximo', 'clase' => 'proximo'];
        if ($hoy->gt($fin)) return ['texto' => 'Finalizado', 'clase' => 'neutro'];
        return ['texto' => 'En curso', 'clase' => 'curso'];
    };
@endphp

<x-app-layout>
    <style>
        .per-wrap { max-width: 1100px; margin: 0 auto; padding: 32px 20px 48px; }

        /* Encabezado */
        .per-hero {
            position: relative; overflow: hidden;
            background: #006B47; color: #fff;
            border-radius: 18px; padding: 28px 28px 24px;
            display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 20px;
        }
        .per-hero::after {
            content: ""; position: absolute; right: -60px; top: -60px;
            width: 220px; height: 220px; border-radius: 9999px;
            border: 28px solid rgba(255,255,255,.07);
        }
        .per-hero h1 { margin: 0; font-size: 28px; font-weight: 800; line-height: 1.15; }
        .per-hero p { margin: 6px 0 0; color: #CDE8DB; font-size: 15px; max-width: 46ch; }

        .per-btn {
            position: relative; z-index: 1;
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; color: #006B47; font-weight: 700; font-size: 14px;
            padding: 11px 18px; border-radius: 10px; text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,.15); transition: transform .15s, box-shadow .15s;
        }
        .per-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(0,0,0,.18); }
        .per-btn:focus-visible { outline: 3px solid #C9E4D6; outline-offset: 2px; }
        .per-btn svg { width: 18px; height: 18px; }

        /* Resumen */
        .per-resumen { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-top: -18px; padding: 0 16px; position: relative; z-index: 2; }
        .per-dato {
            background: #fff; border-radius: 14px; padding: 16px 18px;
            box-shadow: 0 4px 14px rgba(0,0,0,.07); border: 1px solid #EEF2EF;
        }
        .per-dato-label { font-size: 13px; color: #6B7280; }
        .per-dato-valor { font-size: 24px; font-weight: 800; color: #0F2F22; margin-top: 2px; }
        .per-dato-valor.texto { font-size: 17px; }

        /* Alerta */
        .per-alerta {
            margin-top: 20px; display: flex; align-items: center; gap: 10px;
            background: #E8F5EE; color: #0B5B3C; border: 1px solid #BFE3CF;
            padding: 12px 16px; border-radius: 12px; font-size: 14px;
        }
        .per-alerta svg { width: 20px; height: 20px; flex-shrink: 0; }

        /* Lista */
        .per-lista { margin-top: 24px; display: flex; flex-direction: column; gap: 12px; }
        .per-item {
            background: #fff; border: 1px solid #E5E7EB; border-radius: 14px;
            padding: 18px 20px; display: grid;
            grid-template-columns: minmax(180px, 1.3fr) minmax(220px, 1.6fr) auto;
            align-items: center; gap: 20px;
            transition: border-color .15s, box-shadow .15s;
        }
        .per-item:hover { border-color: #BFE3CF; box-shadow: 0 4px 14px rgba(0,107,71,.08); }
        .per-item.activo { border: 2px solid #006B47; background: #FBFEFC; }

        .per-nombre { font-size: 17px; font-weight: 700; color: #111827; margin: 0; }
        .per-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .per-chip { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 9999px; }
        .per-chip.curso { background: #006B47; color: #fff; }
        .per-chip.proximo { background: #FEF3C7; color: #92400E; }
        .per-chip.neutro { background: #F3F4F6; color: #4B5563; }
        .per-chip.activo { background: #E8F5EE; color: #006B47; border: 1px solid #BFE3CF; }

        .per-fechas { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #374151; }
        .per-fecha small { display: block; font-size: 12px; color: #6B7280; }
        .per-fecha strong { font-weight: 600; }
        .per-flecha { color: #9CA3AF; width: 18px; height: 18px; flex-shrink: 0; }
        .per-barra { margin-top: 10px; height: 6px; background: #E5E7EB; border-radius: 9999px; overflow: hidden; }
        .per-barra span { display: block; height: 100%; background: #006B47; border-radius: 9999px; }
        .per-barra-txt { font-size: 12px; color: #6B7280; margin-top: 4px; }

        .per-acciones { display: flex; gap: 8px; justify-content: flex-end; }
        .per-accion {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600; padding: 8px 12px; border-radius: 9px;
            border: 1px solid #E5E7EB; background: #fff; color: #374151;
            cursor: pointer; text-decoration: none; font-family: inherit;
            transition: background .15s, color .15s, border-color .15s;
        }
        .per-accion svg { width: 16px; height: 16px; }
        .per-accion.editar:hover { background: #E8F5EE; color: #006B47; border-color: #BFE3CF; }
        .per-accion.eliminar:hover { background: #FEF2F2; color: #B91C1C; border-color: #FECACA; }
        .per-accion:focus-visible { outline: 2px solid #006B47; outline-offset: 2px; }

        /* Vacío */
        .per-vacio {
            margin-top: 24px; background: #fff; border: 2px dashed #CFE3D8; border-radius: 16px;
            padding: 48px 20px; text-align: center;
        }
        .per-vacio-icono {
            width: 64px; height: 64px; margin: 0 auto 14px; border-radius: 16px;
            background: #E8F5EE; color: #006B47; display: flex; align-items: center; justify-content: center;
        }
        .per-vacio-icono svg { width: 32px; height: 32px; }
        .per-vacio h3 { margin: 0; font-size: 18px; color: #111827; }
        .per-vacio p { margin: 6px auto 20px; color: #6B7280; font-size: 14px; max-width: 42ch; }
        .per-vacio .per-btn { background: #006B47; color: #fff; }

        .per-paginacion { margin-top: 20px; }

        @media (max-width: 760px) {
            .per-hero { padding: 22px 20px 36px; }
            .per-hero h1 { font-size: 24px; }
            .per-item { grid-template-columns: 1fr; gap: 14px; }
            .per-acciones { justify-content: flex-start; }
        }
    </style>

    <div class="per-wrap">

        {{-- Encabezado --}}
        <div class="per-hero">
            <div>
                <h1>Períodos Académicos</h1>
                <p>Define las fechas de cada período para organizar proyectos, inscripciones y horas de vinculación.</p>
            </div>
            <a href="{{ route('admin.periodos.create') }}" class="per-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Nuevo período
            </a>
        </div>

        {{-- Resumen --}}
        <div class="per-resumen">
            <div class="per-dato">
                <div class="per-dato-label">Períodos registrados</div>
                <div class="per-dato-valor">{{ $totalPeriodos }}</div>
            </div>
            <div class="per-dato">
                <div class="per-dato-label">Período activo</div>
                <div class="per-dato-valor texto">{{ $periodoActivo->nombre ?? 'Ninguno' }}</div>
            </div>
            <div class="per-dato">
                <div class="per-dato-label">Hoy</div>
                <div class="per-dato-valor texto">{{ $hoy->translatedFormat('d M Y') }}</div>
            </div>
        </div>

        {{-- Lista de períodos --}}
        @if ($periodos->count())
            <div class="per-lista">
                @foreach ($periodos as $periodo)
                    @php
                        $est = $estado($periodo);
                        $pct = $avance($periodo);
                    @endphp
                    <div class="per-item {{ $periodo->activo ? 'activo' : '' }}">

                        <div>
                            <h3 class="per-nombre">{{ $periodo->nombre }}</h3>
                            <div class="per-chips">
                                <span class="per-chip {{ $est['clase'] }}">{{ $est['texto'] }}</span>
                                @if ($periodo->activo)
                                    <span class="per-chip activo">Activo en el sistema</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="per-fechas">
                                <div class="per-fecha">
                                    <small>Inicio</small>
                                    <strong>{{ $formatear($periodo->fecha_inicio) }}</strong>
                                </div>
                                <svg class="per-flecha" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                                <div class="per-fecha">
                                    <small>Fin</small>
                                    <strong>{{ $formatear($periodo->fecha_fin) }}</strong>
                                </div>
                            </div>
                            @if (! is_null($pct))
                                <div class="per-barra" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Avance del período">
                                    <span style="width: {{ $pct }}%"></span>
                                </div>
                                <div class="per-barra-txt">{{ $pct }}% transcurrido</div>
                            @endif
                        </div>

                        <div class="per-acciones">
                            <a href="{{ route('admin.periodos.edit', $periodo) }}" class="per-accion editar">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L19 9l-4-4L4 16v4Zm9-13 4 4"/></svg>
                                Editar
                            </a>
                            <form action="{{ route('admin.periodos.destroy', $periodo) }}" method="POST"
                                  data-confirm-title="¿Eliminar el período {{ $periodo->nombre }}?"
                                  data-confirm-text="Esta acción no se puede deshacer.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="per-accion eliminar">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (method_exists($periodos, 'links'))
                <div class="per-paginacion">{{ $periodos->links() }}</div>
            @endif
        @else
            <div class="per-vacio">
                <div class="per-vacio-icono">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>
                </div>
                <h3>Aún no hay períodos</h3>
                <p>Crea el primer período académico para poder registrar proyectos e inscripciones de estudiantes.</p>
                <a href="{{ route('admin.periodos.create') }}" class="per-btn">
                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Crear primer período
                </a>
            </div>
        @endif
    </div>
</x-app-layout>