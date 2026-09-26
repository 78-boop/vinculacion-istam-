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

    $iconos = [
        'inicio'        => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z',
        'periodos'      => 'M7 3v3m10-3v3M4 8h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'proyectos'     => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z',
        'inscripciones' => 'M9 5h6m-6 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2m-6 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 13l2 2 4-4',
        'actividades'   => 'M4 12h4l3-8 4 16 3-8h2',
        'usuarios'      => 'M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17 0v-1a4 4 0 0 0-3-3.87M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm3-3.87a3.5 3.5 0 0 1 0 6.74',
        'certificados'  => 'M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3 2.5L8 22l4-2 4 2-1-4.5M6 3h12a1 1 0 0 1 1 1v6',
        'documentos'    => 'M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13h6m-6 4h4',
        'proponer'      => 'M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3Zm-2 17h4',
        'vinculacion'   => 'M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1',
    ];

    $enlaces = [
        ['ruta' => 'dashboard', 'activo' => 'dashboard', 'texto' => 'Área Personal', 'icono' => 'inicio'],
    ];

    if ($usuario->role === 'admin') {
        $enlaces = array_merge($enlaces, [
            ['ruta' => 'admin.periodos.index',          'activo' => 'admin.periodos.*',          'texto' => 'Períodos',              'icono' => 'periodos'],
            ['ruta' => 'admin.proyectos.index',         'activo' => 'admin.proyectos.*',         'texto' => 'Proyectos',             'icono' => 'proyectos'],
            ['ruta' => 'admin.inscripciones.index',     'activo' => 'admin.inscripciones.*',     'texto' => 'Inscripciones',         'icono' => 'inscripciones'],
            ['ruta' => 'admin.actividades.index',       'activo' => 'admin.actividades.*',       'texto' => 'Actividades',           'icono' => 'actividades'],
            ['ruta' => 'admin.usuarios.index',          'activo' => 'admin.usuarios.*',          'texto' => 'Usuarios',              'icono' => 'usuarios'],
            ['ruta' => 'admin.certificados.index',      'activo' => 'admin.certificados.*',      'texto' => 'Certificados',          'icono' => 'certificados'],
            ['ruta' => 'admin.tipos-certificado.index', 'activo' => 'admin.tipos-certificado.*', 'texto' => 'Documentos requeridos', 'icono' => 'documentos'],
        ]);
    } elseif ($usuario->role === 'docente') {
        $enlaces = array_merge($enlaces, [
            ['ruta' => 'docente.proyectos.create',              'activo' => 'docente.proyectos.*',              'texto' => 'Proponer Proyecto',       'icono' => 'proponer'],
            ['ruta' => 'docente.certificados.index',            'activo' => 'docente.certificados.*',            'texto' => 'Certificados',            'icono' => 'certificados'],
            ['ruta' => 'docente.actividades-vinculacion.index', 'activo' => 'docente.actividades-vinculacion.*', 'texto' => 'Actividades Vinculación', 'icono' => 'vinculacion'],
        ]);
    } elseif ($usuario->role === 'estudiante') {
        $enlaces = array_merge($enlaces, [
            ['ruta' => 'certificados-estudiante.index', 'activo' => 'certificados-estudiante.*', 'texto' => 'Certificados',            'icono' => 'certificados'],
            ['ruta' => 'actividades-vinculacion.index', 'activo' => 'actividades-vinculacion.*', 'texto' => 'Actividades Vinculación', 'icono' => 'vinculacion'],
        ]);
    }
@endphp

{{-- Estilos propios: funcionan sin necesidad de ejecutar npm run build --}}
<style>
    [x-cloak] { display: none !important; }

    .inav { position: relative; z-index: 40; }

    /* Barra única verde oscuro */
    .inav-bar { background: #006B47; border-bottom: 3px solid #4a3520; }
    .inav-bar-in {
        display: flex; align-items: center; gap: 16px;
        height: 64px; padding: 0 20px;
    }

    /* Logo */
    .inav-logo {
        width: 44px; height: 44px; border-radius: 9999px; background: #fff;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.25);
    }
    .inav-logo img { width: 32px; height: 32px; object-fit: contain; }
    .inav-logo:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }

    /* Enlaces */
    .inav-links {
        flex: 1; min-width: 0; display: flex; align-items: center; gap: 4px;
        overflow-x: auto; scrollbar-width: none;
    }
    .inav-links::-webkit-scrollbar { display: none; }
    .inav-link {
        display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0;
        padding: 8px 14px; border-radius: 9999px;
        color: #E6F2EC; font-size: 14px; font-weight: 500;
        text-decoration: none; white-space: nowrap; transition: background .15s, color .15s;
    }
    .inav-link:hover { background: rgba(255,255,255,.14); color: #fff; }
    .inav-link:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
    .inav-link.activo { background: #fff; color: #006B47; font-weight: 600; }
    .inav-link svg { width: 18px; height: 18px; flex-shrink: 0; }

    /* Botón del usuario */
    .inav-user { position: relative; flex-shrink: 0; margin-left: auto; }
    .inav-user-btn {
        display: inline-flex; align-items: center; gap: 10px;
        background: #fff; border: 0; border-radius: 10px;
        padding: 6px 12px 6px 6px; cursor: pointer;
        font-size: 14px; font-weight: 600; color: #1f2937; font-family: inherit;
        box-shadow: 0 1px 2px rgba(0,0,0,.15); transition: color .15s;
    }
    .inav-user-btn:hover { color: #006B47; }
    .inav-user-btn:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
    .inav-avatar {
        width: 32px; height: 32px; border-radius: 9999px; flex-shrink: 0;
        background: #006B47; color: #fff; font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
    }
    img.inav-avatar { object-fit: cover; border: 2px solid #006B47; }
    .inav-user-name { max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .inav-chev { width: 16px; height: 16px; transition: transform .15s; }
    .inav-chev.abierto { transform: rotate(180deg); }

    /* Menú desplegable del usuario */
    .inav-dd {
        position: absolute; right: 0; top: calc(100% + 8px); width: 230px;
        background: #fff; border-radius: 10px; overflow: hidden; z-index: 50;
        box-shadow: 0 10px 25px rgba(0,0,0,.15), 0 0 0 1px rgba(0,0,0,.05);
    }
    .inav-dd-head { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
    .inav-dd-head p { margin: 0; }
    .inav-dd-nombre { font-size: 14px; font-weight: 600; color: #111827; }
    .inav-dd-correo { font-size: 12px; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .inav-dd a, .inav-dd button {
        display: block; width: 100%; text-align: left; padding: 10px 16px;
        font-size: 14px; color: #374151; background: none; border: 0;
        cursor: pointer; text-decoration: none; font-family: inherit;
    }
    .inav-dd a:hover, .inav-dd button:hover,
    .inav-dd a:focus-visible, .inav-dd button:focus-visible { background: #F1F8E9; outline: none; }
    .inav-dd .inav-salir { color: #b91c1c; border-top: 1px solid #e5e7eb; }
    .inav-dd .inav-salir:hover { background: #fef2f2; }

    /* Botón hamburguesa (solo móvil) */
    .inav-burger {
        display: none; align-items: center; justify-content: center; margin-left: auto;
        width: 42px; height: 42px; border-radius: 10px; border: 0;
        background: #fff; color: #1f2937; cursor: pointer;
    }
    .inav-burger svg { width: 24px; height: 24px; }

    /* Menú móvil */
    .inav-movil { background: #fff; border-bottom: 3px solid #006B47; box-shadow: 0 8px 20px rgba(0,0,0,.1); }
    .inav-movil-user { display: flex; align-items: center; gap: 12px; padding: 16px 20px; background: #F1F8E9; }
    .inav-movil-user .inav-avatar { width: 44px; height: 44px; font-size: 15px; }
    .inav-movil-user p { margin: 0; }
    .inav-movil-lista { padding: 8px 12px; }
    .inav-movil-lista a, .inav-movil-lista button {
        display: flex; align-items: center; gap: 12px; width: 100%;
        padding: 12px 14px; border-radius: 10px; border: 0; background: none;
        font-size: 15px; font-weight: 500; color: #374151; text-decoration: none;
        text-align: left; cursor: pointer; font-family: inherit;
    }
    .inav-movil-lista a:hover { background: #F1F8E9; }
    .inav-movil-lista a.activo { background: #006B47; color: #fff; }
    .inav-movil-lista svg { width: 20px; height: 20px; flex-shrink: 0; }
    .inav-movil-pie { border-top: 1px solid #e5e7eb; padding: 8px 12px 12px; }
    .inav-movil-pie .inav-salir { color: #b91c1c; }
    .inav-movil-pie .inav-salir:hover { background: #fef2f2; }

    @media (max-width: 900px) {
        .inav-links, .inav-user { display: none; }
        .inav-burger { display: inline-flex; }
    }
    @media (min-width: 901px) {
        .inav-movil { display: none !important; }
    }
</style>

<nav class="inav" x-data="{ menuMovil: false, menuUsuario: false }"
     @keydown.escape.window="menuMovil = false; menuUsuario = false">

    <div class="inav-bar">
        <div class="inav-bar-in">

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="inav-logo" aria-label="Ir al Área Personal">
                <img src="{{ asset('images/logo-istam.png') }}" alt="ISTAM">
            </a>

            {{-- Enlaces según el rol --}}
            <div class="inav-links">
                @foreach ($enlaces as $enlace)
                    @php $activo = request()->routeIs($enlace['activo']); @endphp
                    <a href="{{ route($enlace['ruta']) }}"
                       class="inav-link {{ $activo ? 'activo' : '' }}"
                       @if($activo) aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$enlace['icono']] }}"/>
                        </svg>
                        {{ $enlace['texto'] }}
                    </a>
                @endforeach
            </div>

            {{-- Usuario: inicial + nombre + menú (Perfil / Cerrar Sesión) --}}
            <div class="inav-user" @click.outside="menuUsuario = false">
                <button type="button" class="inav-user-btn"
                        @click="menuUsuario = !menuUsuario" :aria-expanded="menuUsuario">
                    @if($usuario->profile_photo_path)
                        <img class="inav-avatar" src="{{ asset('storage/' . $usuario->profile_photo_path) }}" alt="Foto de {{ $usuario->name }}">
                    @else
                        <span class="inav-avatar">{{ $iniciales }}</span>
                    @endif
                    <span class="inav-user-name">{{ $usuario->name }}</span>
                    <svg class="inav-chev" :class="menuUsuario && 'abierto'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                    </svg>
                </button>

                <div class="inav-dd" x-cloak x-show="menuUsuario" x-transition.opacity.duration.150ms>
                    <div class="inav-dd-head">
                        <p class="inav-dd-nombre">{{ $usuario->name }}</p>
                        <p class="inav-dd-correo">{{ $usuario->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}">Perfil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inav-salir">Cerrar Sesión</button>
                    </form>
                </div>
            </div>

            {{-- Hamburguesa (móvil) --}}
            <button type="button" class="inav-burger" @click="menuMovil = !menuMovil"
                    :aria-expanded="menuMovil" aria-label="Abrir menú">
                <svg x-show="!menuMovil" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-cloak x-show="menuMovil" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ===== Menú móvil ===== --}}
    <div class="inav-movil" x-cloak x-show="menuMovil" x-transition.opacity.duration.150ms>
        <div class="inav-movil-user">
            @if($usuario->profile_photo_path)
                <img class="inav-avatar" src="{{ asset('storage/' . $usuario->profile_photo_path) }}" alt="Foto de {{ $usuario->name }}">
            @else
                <span class="inav-avatar">{{ $iniciales }}</span>
            @endif
            <div style="min-width:0">
                <p style="font-weight:600;color:#111827">{{ $usuario->name }}</p>
                <p style="font-size:13px;color:#4b5563;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $usuario->email }}</p>
            </div>
        </div>

        <div class="inav-movil-lista">
            @foreach ($enlaces as $enlace)
                @php $activo = request()->routeIs($enlace['activo']); @endphp
                <a href="{{ route($enlace['ruta']) }}" class="{{ $activo ? 'activo' : '' }}"
                   @if($activo) aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos[$enlace['icono']] }}"/>
                    </svg>
                    {{ $enlace['texto'] }}
                </a>
            @endforeach
        </div>

        <div class="inav-movil-lista inav-movil-pie">
            <a href="{{ route('profile.edit') }}">
                <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/>
                </svg>
                Perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inav-salir">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17l5-5-5-5m5 5H9m4 9H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h8"/>
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>