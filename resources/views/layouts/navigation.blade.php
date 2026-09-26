@php
    $usuario = Auth::user();
    $iniciales = mb_strtoupper(
        collect(preg_split('/\s+/', trim($usuario->name)))
            ->filter()
            ->take(2)
            ->map(fn ($parte) => mb_substr($parte, 0, 1))
            ->implode('')
    );

    $nombresRol = [
        'admin' => 'Administrador',
        'docente' => 'Docente',
        'estudiante' => 'Estudiante',
    ];
    $rolTexto = $nombresRol[$usuario->role] ?? ucfirst($usuario->role);
    $foto = $usuario->profile_photo_path ? asset('storage/' . $usuario->profile_photo_path) : null;

    $iconos = [
        'inicio'        => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z',
        'periodos'      => 'M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'carreras'      => 'M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5M22 9v6',
        'proyectos'     => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z',
        'inscripciones' => 'M9 5h6m-6 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2m-6 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 13l2 2 4-4',
        'actividades'   => 'M4 12h4l3-8 4 16 3-8h2',
        'usuarios'      => 'M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17 0v-1a4 4 0 0 0-3-3.87M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm3-3.87a3.5 3.5 0 0 1 0 6.74',
        'certificados'  => 'M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3 2.5L8 22l4-2 4 2-1-4.5M6 3h12a1 1 0 0 1 1 1v6',
        'documentos'    => 'M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13h6m-6 4h4',
        'proponer'      => 'M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3Zm-2 17h4',
        'vinculacion'   => 'M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1',
        'perfil'        => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0',
        'salir'         => 'M15 17l5-5-5-5m5 5H9m4 9H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h8',
    ];

    // Menú agrupado por secciones según el rol
    $secciones = [
        ['titulo' => 'Principal', 'enlaces' => [
            ['ruta' => 'dashboard', 'activo' => 'dashboard', 'texto' => 'Área Personal', 'icono' => 'inicio'],
        ]],
    ];

    if ($usuario->role === 'admin') {
        $secciones[] = ['titulo' => 'Gestión académica', 'enlaces' => [
            ['ruta' => 'admin.periodos.index',    'activo' => 'admin.periodos.*',    'texto' => 'Períodos',    'icono' => 'periodos'],
            ['ruta' => 'admin.carreras.index',    'activo' => 'admin.carreras.*',    'texto' => 'Carreras',    'icono' => 'carreras'],
            ['ruta' => 'admin.proyectos.index',   'activo' => 'admin.proyectos.*',   'texto' => 'Proyectos',   'icono' => 'proyectos'],
            ['ruta' => 'admin.actividades.index', 'activo' => 'admin.actividades.*', 'texto' => 'Actividades', 'icono' => 'actividades'],
        ]];
        $secciones[] = ['titulo' => 'Personas', 'enlaces' => [
            ['ruta' => 'admin.inscripciones.index', 'activo' => 'admin.inscripciones.*', 'texto' => 'Inscripciones', 'icono' => 'inscripciones'],
            ['ruta' => 'admin.usuarios.index',      'activo' => 'admin.usuarios.*',      'texto' => 'Usuarios',      'icono' => 'usuarios'],
        ]];
        $secciones[] = ['titulo' => 'Documentación', 'enlaces' => [
            ['ruta' => 'admin.certificados.index',      'activo' => 'admin.certificados.*',      'texto' => 'Certificados',          'icono' => 'certificados'],
            ['ruta' => 'admin.tipos-certificado.index', 'activo' => 'admin.tipos-certificado.*', 'texto' => 'Documentos requeridos', 'icono' => 'documentos'],
        ]];
    } elseif ($usuario->role === 'docente') {
        $secciones[] = ['titulo' => 'Mi trabajo', 'enlaces' => [
            ['ruta' => 'docente.actividades.index',  'activo' => 'docente.actividades.*',  'texto' => 'Mis actividades',   'icono' => 'actividades'],
            ['ruta' => 'docente.certificados.index', 'activo' => 'docente.certificados.*', 'texto' => 'Certificados',      'icono' => 'certificados'],
            ['ruta' => 'docente.proyectos.create',   'activo' => 'docente.proyectos.*',    'texto' => 'Proponer Proyecto', 'icono' => 'proponer'],
        ]];
    } elseif ($usuario->role === 'estudiante') {
        $secciones[] = ['titulo' => 'Mi vinculación', 'enlaces' => [
            ['ruta' => 'estudiante.proyectos.index',    'activo' => 'estudiante.proyectos.*',    'texto' => 'Proyectos disponibles', 'icono' => 'proyectos'],
            ['ruta' => 'certificados-estudiante.index', 'activo' => 'certificados-estudiante.*', 'texto' => 'Certificados',          'icono' => 'certificados'],
        ]];
    }

    // Título de la página actual para la barra superior
    $tituloPagina = request()->routeIs('profile.*') ? 'Mi perfil' : 'Vinculación ISTAM';
    $iconoPagina = request()->routeIs('profile.*') ? 'perfil' : 'inicio';
    foreach ($secciones as $seccion) {
        foreach ($seccion['enlaces'] as $enlace) {
            if (request()->routeIs($enlace['activo'])) {
                $tituloPagina = $enlace['texto'];
                $iconoPagina = $enlace['icono'];
            }
        }
    }

    // Barra de pestañas inferior (móvil): las 4 primeras opciones + "Más"
    $nombresCortos = [
        'Área Personal' => 'Inicio',
        'Actividades Vinculación' => 'Actividades',
        'Proponer Proyecto' => 'Proponer',
        'Documentos requeridos' => 'Documentos',
        'Proyectos disponibles' => 'Proyectos',
        'Mis actividades' => 'Actividades',
        'Proponer Proyecto' => 'Proponer',
    ];
    $pestanas = collect($secciones)->flatMap(fn ($s) => $s['enlaces'])->take(4)->values();
    $masActivo = ! $pestanas->contains(fn ($e) => request()->routeIs($e['activo']));
@endphp

{{-- ================= BARRA LATERAL ================= --}}
<div class="ui-backdrop" x-cloak x-show="movil" x-transition.opacity @click="movil = false"></div>

<aside class="ui-side" aria-label="Menú principal">
    <div class="ui-side-glow" aria-hidden="true"></div>

    {{-- Marca --}}
    <div class="ui-brand">
        <a href="{{ route('dashboard') }}" class="ui-brand-logo" aria-label="Ir al Área Personal">
            <img src="{{ asset('images/logo-istam.png') }}" alt="ISTAM">
        </a>
        <div class="ui-brand-text ui-ocultar-colapsado">
            <strong>Vinculación</strong>
            <span>ISTAM</span>
        </div>
        <button type="button" class="ui-side-cerrar" @click="movil = false" aria-label="Cerrar menú">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    {{-- Enlaces --}}
    <nav class="ui-side-nav">
        @foreach ($secciones as $seccion)
            <p class="ui-side-label ui-ocultar-colapsado">{{ $seccion['titulo'] }}</p>
            <ul>
                @foreach ($seccion['enlaces'] as $enlace)
                    @php $activo = request()->routeIs($enlace['activo']); @endphp
                    <li>
                        <a href="{{ route($enlace['ruta']) }}"
                           class="ui-side-link {{ $activo ? 'is-activo' : '' }}"
                           data-tip="{{ $enlace['texto'] }}"
                           @if($activo) aria-current="page" @endif>
                            <span class="ui-side-ico">
                                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$enlace['icono']] }}"/>
                                </svg>
                            </span>
                            <span class="ui-side-txt ui-ocultar-colapsado">{{ $enlace['texto'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    {{-- Tarjeta del usuario (abajo) --}}
    <div class="ui-side-user">
        @if($foto)
            <img class="ui-avatar" src="{{ $foto }}" alt="Foto de {{ $usuario->name }}">
        @else
            <span class="ui-avatar">{{ $iniciales }}</span>
        @endif
        <div class="ui-side-user-info ui-ocultar-colapsado">
            <strong>{{ $usuario->name }}</strong>
            <span>{{ $rolTexto }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ui-ocultar-colapsado">
            @csrf
            <button type="submit" class="ui-side-salir" aria-label="Cerrar sesión" title="Cerrar sesión">
                <svg fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['salir'] }}"/></svg>
            </button>
        </form>
    </div>
</aside>

{{-- ================= BARRA SUPERIOR ================= --}}
<header class="ui-top">
    {{-- Logo (móvil, estilo app) / botón colapsar (escritorio) --}}
    <a href="{{ route('dashboard') }}" class="ui-top-logo ui-solo-movil" aria-label="Ir al Área Personal">
        <img src="{{ asset('images/logo-istam.png') }}" alt="ISTAM">
    </a>
    <button type="button" class="ui-top-btn ui-solo-escritorio" @click="alternar()"
            :aria-label="colapsado ? 'Expandir menú lateral' : 'Contraer menú lateral'">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="colapsado && 'transform: scaleX(-1)'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 19h16M11 12h9M8 9l-3 3 3 3"/>
        </svg>
    </button>

    <div class="ui-top-titulo">
        <span class="ui-top-ico">
            <svg fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$iconoPagina] }}"/></svg>
        </span>
        <div>
            <small>{{ $rolTexto }}</small>
            <h2>{{ $tituloPagina }}</h2>
        </div>
    </div>

    <div class="ui-top-fecha ui-solo-escritorio">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['periodos'] }}"/></svg>
        {{ \Illuminate\Support\Str::ucfirst(\Carbon\Carbon::now()->translatedFormat('l, d \d\e F')) }}
    </div>

    {{-- Usuario: menú desplegable (Perfil / Cerrar sesión) --}}
    <div class="ui-top-user" x-data="{ abierto: false }" @click.outside="abierto = false" @keydown.escape="abierto = false">
        <button type="button" class="ui-top-user-btn" @click="abierto = !abierto" :aria-expanded="abierto" aria-haspopup="true">
            @if($foto)
                <img class="ui-avatar" src="{{ $foto }}" alt="">
            @else
                <span class="ui-avatar">{{ $iniciales }}</span>
            @endif
            <span class="ui-top-user-txt ui-solo-escritorio">
                <strong>{{ \Illuminate\Support\Str::words($usuario->name, 2, '') }}</strong>
                <small>{{ $rolTexto }}</small>
            </span>
            <svg class="ui-chev" :class="abierto && 'is-abierto'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
        </button>

        <div class="ui-dd" x-cloak x-show="abierto"
             x-transition:enter="ui-dd-enter" x-transition:enter-start="ui-dd-from" x-transition:enter-end="ui-dd-to"
             x-transition:leave="ui-dd-enter" x-transition:leave-start="ui-dd-to" x-transition:leave-end="ui-dd-from">
            <div class="ui-dd-head">
                @if($foto)
                    <img class="ui-avatar ui-avatar-lg" src="{{ $foto }}" alt="">
                @else
                    <span class="ui-avatar ui-avatar-lg">{{ $iniciales }}</span>
                @endif
                <div>
                    <strong>{{ $usuario->name }}</strong>
                    <span>{{ $usuario->email }}</span>
                    <em>{{ $rolTexto }}</em>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="ui-dd-item">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['perfil'] }}"/></svg>
                Ver mi perfil
            </a>
            <a href="{{ route('dashboard') }}" class="ui-dd-item">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['inicio'] }}"/></svg>
                Área Personal
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ui-dd-item ui-dd-salir">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['salir'] }}"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</header>

{{-- ================= PESTAÑAS INFERIORES (móvil, estilo app) ================= --}}
<nav class="ui-tabs" aria-label="Navegación rápida">
    @foreach ($pestanas as $enlace)
        @php $activo = request()->routeIs($enlace['activo']); @endphp
        <a href="{{ route($enlace['ruta']) }}" class="ui-tab {{ $activo ? 'is-activo' : '' }}" @if($activo) aria-current="page" @endif>
            <span class="ui-tab-ico">
                <svg fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$enlace['icono']] }}"/></svg>
            </span>
            <span class="ui-tab-txt">{{ $nombresCortos[$enlace['texto']] ?? $enlace['texto'] }}</span>
        </a>
    @endforeach
    <button type="button" class="ui-tab {{ $masActivo ? 'is-activo' : '' }}" @click="movil = true" aria-label="Más opciones">
        <span class="ui-tab-ico">
            <svg fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h6v6H4V5Zm10 0h6v6h-6V5ZM4 15h6v6H4v-6Zm10 3h6m-3-3v6"/></svg>
        </span>
        <span class="ui-tab-txt">Más</span>
    </button>
</nav>
