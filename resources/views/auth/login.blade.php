<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · Sistema de Vinculación con la Sociedad</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --verde-profundo: #0B3F29;
            --verde: #0B3F29;
            --verde-medio: #15573a;
            --verde-claro: #dcece3;
            --fondo: #f4f7f5;
            --texto: #17251d;
            --texto-suave: #5b6b61;
            --borde: #cbd8d0;
            --error: #b42318;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; }

        body {
            font-family: "Public Sans", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--texto);
            background: var(--fondo);
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            min-height: 100vh;
        }

        /* ---------- Panel izquierdo ---------- */
        .panel {
            position: relative;
            background-color: var(--verde);
            background-image:
                radial-gradient(circle at 85% 10%, rgba(46,139,87,.35) 0, transparent 42%),
                radial-gradient(rgba(255,255,255,.08) 1.5px, transparent 1.5px);
            background-size: auto, 22px 22px;
            color: #fff;
            padding: 48px 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 36px;
            overflow: hidden;
        }

        .institucion {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            align-self: flex-start;
            background: #fff;
            padding: 8px 18px 8px 8px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .95rem;
            color: var(--verde-profundo);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
        }

        .sello {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--verde-profundo);
            display: grid; place-items: center;
        }

        .saludo {
            font-family: "Bricolage Grotesque", "Segoe UI", sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: #8fd4ad;
            margin-bottom: 10px;
        }

        .titulo {
            font-family: "Bricolage Grotesque", "Segoe UI", sans-serif;
            font-weight: 800;
            font-size: clamp(2.6rem, 4.6vw, 4.4rem);
            line-height: 1;
            letter-spacing: -0.025em;
            color: #fff;
            text-shadow: 4px 4px 0 #1f7a50;
            max-width: 12ch;
        }

        .bajada {
            margin-top: 22px;
            font-size: 1.12rem;
            font-weight: 500;
            line-height: 1.6;
            color: #d4e8dc;
            max-width: 40ch;
        }

        .ejes {
            margin-top: 26px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            list-style: none;
        }

        .ejes li {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border-radius: 999px;
            padding: 9px 16px;
            font-size: .92rem;
            font-weight: 600;
            color: var(--verde-profundo);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .2);
        }

        .ejes svg { width: 18px; height: 18px; flex: none; }

        .panel > div { margin: auto 0; }

        @media (max-height: 760px) and (min-width: 901px) {
            .panel { padding-top: 32px; gap: 20px; }
            .titulo { font-size: clamp(2.4rem, 3.8vw, 3.6rem); }
        }

        /* ---------- Formulario ---------- */
        .acceso {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
        }

        .form {
            width: 100%;
            max-width: 400px;
        }

        .form h2 {
            font-family: "Bricolage Grotesque", "Segoe UI", sans-serif;
            font-weight: 700;
            font-size: 1.9rem;
            letter-spacing: -0.01em;
        }

        .form .ayuda {
            margin-top: 8px;
            color: var(--texto-suave);
            line-height: 1.5;
        }

        .campo { margin-top: 26px; }

        .campo label {
            display: block;
            font-weight: 600;
            font-size: .92rem;
            margin-bottom: 8px;
        }

        .entrada {
            position: relative;
            display: flex;
            align-items: center;
        }

        .entrada svg.icono {
            position: absolute;
            left: 14px;
            width: 20px; height: 20px;
            color: var(--texto-suave);
        }

        .entrada input {
            width: 100%;
            font: inherit;
            font-size: 1rem;
            padding: 14px 48px 14px 46px;
            border: 1.5px solid var(--borde);
            border-radius: 10px;
            background: #fff;
            color: var(--texto);
            transition: border-color .15s, box-shadow .15s;
        }

        .entrada input:focus {
            outline: none;
            border-color: var(--verde);
            box-shadow: 0 0 0 4px rgba(11, 63, 41, .18);
        }

        .entrada input.invalido { border-color: var(--error); }

        .ver {
            position: absolute;
            right: 8px;
            background: none;
            border: 0;
            padding: 8px;
            cursor: pointer;
            color: var(--texto-suave);
            border-radius: 6px;
        }

        .ver:focus-visible { outline: 2px solid var(--verde); }

        .mensaje-error {
            margin-top: 6px;
            color: var(--error);
            font-size: .88rem;
        }

        .alerta {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 10px;
            background: var(--verde-claro);
            color: var(--verde-profundo);
            font-size: .92rem;
        }

        .opciones {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-size: .92rem;
        }

        .recordar {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .recordar input { accent-color: var(--verde); width: 16px; height: 16px; }

        .opciones a {
            color: var(--verde);
            font-weight: 600;
            text-decoration: none;
        }

        .opciones a:hover { text-decoration: underline; }

        .boton {
            margin-top: 28px;
            width: 100%;
            font: inherit;
            font-weight: 600;
            font-size: 1.02rem;
            padding: 15px;
            border: 0;
            border-radius: 10px;
            background: var(--verde);
            color: #fff;
            cursor: pointer;
            transition: background .15s;
        }

        .boton:hover { background: var(--verde-medio); }
        .boton:focus-visible { outline: 3px solid var(--verde-profundo); outline-offset: 3px; }

        .pie {
            margin-top: 40px;
            font-size: .85rem;
            color: var(--texto-suave);
            line-height: 1.5;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }
            .panel { padding: 32px 24px; gap: 24px; }
            .titulo { font-size: 2.3rem; }
            .ejes { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>
</head>
<body>

    <!-- Panel institucional -->
    <section class="panel" aria-label="Presentación del sistema">
        <div class="institucion">
            <span class="sello" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="7" r="3"/><circle cx="17" cy="9" r="2.5"/>
                    <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M15 14.5c2.8 0 5 2.2 5 5"/>
                </svg>
            </span>
            Dirección de Vinculación con la Sociedad
        </div>

        <div>
            <p class="saludo">¡Hola, estudiante!</p>
            <h1 class="titulo">Sistema de Vinculación con la Sociedad</h1>
            <p class="bajada">
                Tus proyectos comunitarios, prácticas preprofesionales y horas de
                vinculación, todo en un solo lugar.
            </p>

            <ul class="ejes">
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V10l7-5 7 5v11"/><path d="M10 21v-5h4v5"/></svg>Proyectos comunitarios</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>Prácticas preprofesionales</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>Registro de horas</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"/><path d="M9 13h7M9 17h5"/></svg>Informes y certificados</li>
            </ul>
        </div>

    </section>

    <!-- Formulario de acceso -->
    <main class="acceso">
        <form class="form" method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <h2>¡Bienvenido de nuevo!</h2>
            <p class="ayuda">Ingresa con tu correo institucional para ver tus proyectos y avances.</p>

            @if (session('status'))
                <div class="alerta" role="status">{{ session('status') }}</div>
            @endif

            <div class="campo">
                <label for="email">Correo institucional</label>
                <div class="entrada">
                    <svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
                    </svg>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           placeholder="nombre@universidad.edu.ec" autocomplete="username" required autofocus
                           class="@error('email') invalido @enderror">
                </div>
                @error('email')
                    <p class="mensaje-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <div class="entrada">
                    <svg class="icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                    <input id="password" type="password" name="password"
                           placeholder="Tu contraseña" autocomplete="current-password" required
                           class="@error('password') invalido @enderror">
                    <button type="button" class="ver" id="verClave" aria-label="Mostrar contraseña">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mensaje-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="opciones">
                <label class="recordar">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Mantener sesión iniciada
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Recuperar contraseña</a>
                @endif
            </div>

            <button type="submit" class="boton">Iniciar sesión</button>

            <p class="pie">
                ¿Problemas para ingresar? Comunícate con la Dirección de Vinculación con la Sociedad.
            </p>
        </form>
    </main>

    <script>
        (function () {
            var boton = document.getElementById('verClave');
            var campo = document.getElementById('password');
            boton.addEventListener('click', function () {
                var oculto = campo.type === 'password';
                campo.type = oculto ? 'text' : 'password';
                boton.setAttribute('aria-label', oculto ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        })();
    </script>
</body>
</html>