@php
    $activas = $carreras->where('activo', true)->count();
    $totalEstudiantes = $carreras->sum('estudiantes_count');
    $paletas = [
        ['#006B47', '#A3D65C'], ['#2563EB', '#60A5FA'], ['#7C3AED', '#C4B5FD'],
        ['#D97706', '#FCD34D'], ['#DB2777', '#F9A8D4'], ['#0891B2', '#67E8F9'],
    ];
@endphp

<x-app-layout>
    <style>
        .car-grid { margin-top: 26px; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .car-card {
            position: relative; background: #fff; border-radius: 22px; overflow: hidden;
            border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra);
            display: flex; flex-direction: column; transition: transform .22s, box-shadow .22s;
        }
        .car-card:hover { transform: translateY(-5px); box-shadow: var(--ui-sombra-hover); }
        .car-card.inactiva { opacity: .72; }
        .car-card.inactiva .car-foto { filter: grayscale(1); }

        .car-portada { position: relative; overflow: hidden; aspect-ratio: 16 / 10; background: #EEF3F0; }
        .car-foto { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform .5s; }
        .car-card:hover .car-foto { transform: scale(1.05); }
        .car-lupa {
            position: absolute; top: 12px; right: 12px; z-index: 1; width: 34px; height: 34px; border-radius: 10px;
            display: grid; place-items: center; background: rgba(255,255,255,.92); color: var(--ui-verde);
            opacity: 0; transform: scale(.85); transition: opacity .2s, transform .2s; pointer-events: none;
        }
        .car-lupa svg { width: 18px; height: 18px; }
        .car-card:hover .car-lupa { opacity: 1; transform: none; }
        @media (hover: none) { .car-lupa { opacity: 1; transform: none; } }
        .car-portada-sin {
            width: 100%; height: 100%; display: grid; place-items: center;
            background: linear-gradient(135deg, var(--a), var(--b));
        }
        .car-portada-sin span { font-size: 54px; font-weight: 800; color: rgba(255,255,255,.92); letter-spacing: -.04em; text-shadow: 0 6px 20px rgba(0,0,0,.2); }
        .car-portada::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, transparent 60%, rgba(3,36,26,.5)); pointer-events: none; }
        .car-estado { position: absolute; top: 12px; left: 12px; z-index: 1; }
        .car-horas {
            position: absolute; right: 12px; bottom: 12px; z-index: 1;
            background: rgba(255,255,255,.95); color: var(--ui-verde); font-weight: 800; font-size: 13px;
            padding: 6px 11px; border-radius: 11px; display: flex; align-items: center; gap: 6px;
            box-shadow: 0 6px 16px -6px rgba(0,0,0,.35);
        }
        .car-horas svg { width: 15px; height: 15px; }

        .car-body { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 14px; flex: 1; }
        .car-body h3 { margin: 0; font-size: 17.5px; font-weight: 800; color: var(--ui-texto); line-height: 1.25; }
        .car-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .car-stat { background: #F6F9F7; border-radius: 13px; padding: 10px 12px; }
        .car-stat strong { display: block; font-size: 20px; font-weight: 800; color: var(--ui-texto); line-height: 1.1; }
        .car-stat span { font-size: 12px; color: var(--ui-texto-3); font-weight: 600; }
        .car-acciones { display: flex; gap: 8px; margin-top: auto; }
        .car-acciones > * { flex: 1; }
        .car-acciones form { display: flex; }
        .car-acciones form .ui-btn { flex: 1; }

        .car-nueva {
            border: 2px dashed #BFDCCB; background: linear-gradient(135deg, #F7FCF9, #EEF8F2);
            border-radius: 22px; min-height: 300px; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 12px; text-align: center; padding: 24px;
            text-decoration: none !important; color: var(--ui-verde) !important; transition: border-color .2s, background .2s, transform .2s;
        }
        .car-nueva:hover { border-color: var(--ui-verde); background: #E7F5EE; transform: translateY(-5px); }
        .car-nueva-ico {
            width: 64px; height: 64px; border-radius: 20px; display: grid; place-items: center;
            background: linear-gradient(135deg, var(--ui-verde-2), var(--ui-verde)); color: #fff;
            box-shadow: 0 14px 28px -12px rgba(0,107,71,.9); transition: transform .25s;
        }
        .car-nueva:hover .car-nueva-ico { transform: rotate(90deg); }
        .car-nueva-ico svg { width: 30px; height: 30px; }
        .car-nueva strong { font-size: 16px; font-weight: 800; }
        .car-nueva span { font-size: 13px; color: var(--ui-texto-2); max-width: 26ch; }
    </style>

    <div class="ui-wrap">
        <div class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5"/></svg>
                    Oferta académica
                </span>
                <h1>Carreras</h1>
                <p>Las carreras que registres aquí aparecen automáticamente en usuarios, actividades de vinculación, certificados y reportes.</p>
            </div>
            <div class="ui-hero-acciones">
                <a href="{{ route('admin.carreras.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nueva carrera
                </a>
            </div>
        </div>

        <div class="ui-kpis" style="margin-top: 20px;">
            <div class="ui-kpi">
                <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5"/></svg></span></div>
                <div class="ui-kpi-valor">{{ $carreras->count() }}</div>
                <div class="ui-kpi-label">Carreras registradas · {{ $activas }} activas</div>
            </div>
            <div class="ui-kpi azul">
                <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m10-12a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/></svg></span></div>
                <div class="ui-kpi-valor">{{ $totalEstudiantes }}</div>
                <div class="ui-kpi-label">Estudiantes asignados</div>
            </div>
            <div class="ui-kpi violeta">
                <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h4l3-8 4 16 3-8h2"/></svg></span></div>
                <div class="ui-kpi-valor">{{ $totalActividades }}</div>
                <div class="ui-kpi-label">Actividades de estudiantes de estas carreras</div>
            </div>
        </div>

        <div class="car-grid">
            @foreach ($carreras as $carrera)
                @php
                    [$colorA, $colorB] = $paletas[$carrera->id % count($paletas)];
                    $siglas = mb_strtoupper(collect(preg_split('/\s+/', $carrera->nombre))
                        ->filter(fn ($p) => mb_strlen($p) > 2)->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                @endphp
                <article class="car-card {{ $carrera->activo ? '' : 'inactiva' }}">
                    <div class="car-portada">
                        @if ($carrera->imagen)
                            <img class="car-foto" src="{{ asset($carrera->imagen) }}" alt="{{ $carrera->nombre }}" loading="lazy">
                            <span class="car-lupa" aria-hidden="true"><svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5M11 8v6M8 11h6"/></svg></span>
                        @else
                            <div class="car-portada-sin" style="--a: {{ $colorA }}; --b: {{ $colorB }};">
                                <span>{{ $siglas ?: 'C' }}</span>
                            </div>
                        @endif
                        <span class="car-estado ui-tag {{ $carrera->activo ? 'verde' : 'gris' }}">{{ $carrera->activo ? 'Activa' : 'Inactiva' }}</span>
                        <span class="car-horas">
                            <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
                            {{ $carrera->horas_requeridas }} h requeridas
                        </span>
                    </div>
                    <div class="car-body">
                        <h3>{{ $carrera->nombre }}</h3>
                        <div class="car-stats">
                            <div class="car-stat"><strong>{{ $carrera->estudiantes_count }}</strong><span>Estudiantes</span></div>
                            <div class="car-stat"><strong>{{ $carrera->actividades_count }}</strong><span>Actividades</span></div>
                        </div>
                        <div class="car-acciones">
                            <a href="{{ route('admin.carreras.edit', $carrera) }}" class="ui-btn ui-btn-suave ui-btn-sm">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L19 9l-4-4L4 16v4Zm9-13 4 4"/></svg>
                                Editar
                            </a>
                            <form action="{{ route('admin.carreras.destroy', $carrera) }}" method="POST"
                                  data-confirm-title="¿Eliminar la carrera {{ $carrera->nombre }}?"
                                  data-confirm-text="Solo se puede eliminar si no tiene estudiantes ni actividades asociadas.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach

            <a href="{{ route('admin.carreras.create') }}" class="car-nueva">
                <span class="car-nueva-ico"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg></span>
                <strong>Agregar carrera</strong>
                <span>{{ $carreras->isEmpty() ? 'Aún no hay carreras. Crea la primera para empezar.' : 'Se mostrará en todas las secciones del sistema.' }}</span>
            </a>
        </div>
    </div>
</x-app-layout>
