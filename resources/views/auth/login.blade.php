<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Entorno Virtual de Aprendizaje') }} - Iniciar Sesión</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #e8e8e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            padding: 45px 40px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 35px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-divider {
            width: 2px;
            align-self: stretch;
            background-color: #8BC449;
        }

        .brand-title {
            font-size: 20px;
            line-height: 1.25;
            font-weight: 700;
            color: #2E814D;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            color: #333;
            transition: all 0.2s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8BC449;
            box-shadow: 0 0 0 3px rgba(139, 196, 73, 0.15);
        }

        input::placeholder {
            color: #999;
        }

        input.error {
            border-color: #dc2626 !important;
            background-color: #fef2f2;
        }

        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 12px;
            border: 1px solid #fecaca;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background-color: #006B47;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 6px;
        }

        .btn-login:hover {
            background-color: #00512f;
            box-shadow: 0 4px 12px rgba(0, 107, 71, 0.3);
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .forgot-password {
            display: block;
            margin-top: 18px;
            color: #006B47;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            gap: 12px;
            flex-wrap: wrap;
        }

        .language-select {
            position: relative;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #006B47;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
        }

        .language-select:hover {
            text-decoration: underline;
        }

        .language-select svg {
            width: 14px;
            height: 14px;
        }

        .language-dropdown {
            display: none;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            min-width: 160px;
            z-index: 10;
            overflow: hidden;
        }

        .language-dropdown.active {
            display: block;
        }

        .language-dropdown a {
            display: block;
            padding: 10px 14px;
            font-size: 13px;
            color: #333;
            text-decoration: none;
        }

        .language-dropdown a:hover {
            background-color: #f2f7f4;
        }

        .language-dropdown a.current {
            font-weight: 700;
            color: #006B47;
        }

        .cookie-btn {
            background-color: #e5e5e5;
            color: #333;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
        }

        .cookie-btn:hover {
            background-color: #d5d5d5;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 8px;
            border-top: 4px solid #006B47;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.3;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #888;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }

        .modal-body {
            padding: 20px 24px;
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .modal-body p {
            margin-bottom: 14px;
        }

        .modal-body p:last-child {
            margin-bottom: 0;
        }

        .modal-footer {
            padding: 16px 24px 20px;
            display: flex;
            justify-content: flex-end;
        }

        .modal-ok-btn {
            background-color: #006B47;
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
        }

        .modal-ok-btn:hover {
            background-color: #00512f;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 35px 25px;
            }

            .brand-title {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo + Nombre de marca -->
        <div class="brand">
            <div class="brand-logo">
                <img src="{{ asset('images/logo-istam.png') }}" alt="Logo">
            </div>
            <div class="brand-divider"></div>
            <div class="brand-title">Entorno Virtual<br>de Aprendizaje</div>
        </div>

        <!-- Formulario de Login -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="@error('email') error @enderror"
                    value="{{ old('email') }}"
                    placeholder="{{ __('Correo electrónico') }}"
                    required
                    autofocus
                    autocomplete="username"
                />
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="@error('password') error @enderror"
                    placeholder="{{ __('Contraseña') }}"
                    required
                    autocomplete="current-password"
                />
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Botón -->
            <button type="submit" class="btn-login">{{ __('Acceder') }}</button>

            <!-- Olvidó su contraseña -->
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">{{ __('¿Olvidó su contraseña?') }}</a>
            @endif
        </form>

        <!-- Idioma y Aviso de Cookies -->
        <div class="login-footer">
            <div style="position: relative;">
                <button type="button" class="language-select" onclick="document.getElementById('langDropdown').classList.toggle('active')">
                    {{ app()->getLocale() === 'en' ? 'English - International (en)' : 'Español - Internacional (es)' }}
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="langDropdown" class="language-dropdown">
                    <a href="{{ route('language.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'current' : '' }}">Español - Internacional (es)</a>
                    <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'current' : '' }}">English - International (en)</a>
                </div>
            </div>
            <button type="button" class="cookie-btn" onclick="document.getElementById('cookieModal').classList.add('active')">Aviso de Cookies</button>
        </div>
    </div>

    <!-- Modal Aviso de Cookies -->
    <div id="cookieModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>{{ __("Las 'Cookies' deben estar habilitadas en su navegador") }}</h2>
                <button type="button" class="modal-close" onclick="document.getElementById('cookieModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <p>{{ __('Este sitio web utiliza dos cookies:') }}</p>
                <p>La cookie esencial es la cookie de sesión, normalmente llamada <strong>laravel_session</strong>. {{ __('Debe permitir esta cookie en su navegador para dar continuidad y permanecer conectado mientras navega por el sitio. Cuando cierre la sesión o cierre el navegador, esta cookie se borra (en el navegador y en el servidor).') }}</p>
                <p>La otra cookie es puramente por conveniencia, normalmente llamada <strong>remember_web</strong>. {{ __('Esta solo recuerda su sesión iniciada en el navegador cuando marca "Recuérdame". Es seguro rechazar esta cookie — solo tendrá que volver a iniciar sesión cada vez que cierre el navegador.') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-ok-btn" onclick="document.getElementById('cookieModal').classList.remove('active')">{{ __('OK') }}</button>
            </div>
        </div>
    </div>

    <script>
        // Cerrar el modal si se hace clic fuera de la tarjeta blanca
        document.getElementById('cookieModal').addEventListener('click', function (event) {
            if (event.target === this) {
                this.classList.remove('active');
            }
        });
    </script>
</body>
</html>