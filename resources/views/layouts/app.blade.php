@php
    // Mensajes de Breeze (perfil / contraseña) que llegan como session('status')
    $estadosPerfil = [
        'profile-updated'         => 'Tu perfil se actualizó correctamente.',
        'password-updated'        => 'Tu contraseña se actualizó correctamente.',
        'verification-link-sent'  => 'Te enviamos un nuevo enlace de verificación a tu correo.',
    ];
    $mensajeEstado = $estadosPerfil[session('status')] ?? null;

    $hayAviso = session('success') || session('error') || session('warning') || session('info') || $mensajeEstado || $errors->any();

    if ($errors->any() || session('error')) {
        $avisoIcono = 'error';
        $avisoTitulo = $errors->any() ? 'Revisa la información' : 'No se pudo completar';
    } elseif (session('warning')) {
        $avisoIcono = 'warning';
        $avisoTitulo = 'Atención';
    } elseif (session('info')) {
        $avisoIcono = 'info';
        $avisoTitulo = 'Información';
    } else {
        $avisoIcono = 'success';
        $avisoTitulo = '¡Listo!';
    }
    if (session('aviso_titulo')) {
        $avisoTitulo = session('aviso_titulo');
    }
    $avisoTexto = $errors->any()
        ? $errors->first()
        : (session('success') ?? session('warning') ?? session('info') ?? session('error') ?? $mensajeEstado);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        @include('partials.pwa')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if($hayAviso)
            <meta name="sistema-flash"
                  data-icon="{{ $avisoIcono }}"
                  data-title="{{ $avisoTitulo }}"
                  data-text="{{ $avisoTexto }}"
                  @if($errors->any()) data-lista="{{ json_encode($errors->all()) }}" @endif>
        @endif

        <title>{{ config('app.name', 'Vinculación ISTAM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Tema visual del sistema (archivo estático: no necesita npm run build) --}}
        <link rel="stylesheet" href="{{ asset('css/istam-ui.css') }}?v={{ @filemtime(public_path('css/istam-ui.css')) }}">
        <script src="{{ asset('js/istam-visor.js') }}?v={{ @filemtime(public_path('js/istam-visor.js')) }}" defer></script>
    </head>
    <body class="ui-body">
        <div class="ui-shell"
             x-data="{
                 movil: false,
                 colapsado: (() => { try { return localStorage.getItem('ui-sidebar') === '1' } catch (e) { return false } })(),
                 alternar() {
                     this.colapsado = !this.colapsado;
                     try { localStorage.setItem('ui-sidebar', this.colapsado ? '1' : '0') } catch (e) {}
                 }
             }"
             :class="{ 'is-colapsado': colapsado, 'is-movil-abierto': movil }"
             @keydown.escape.window="movil = false">

            @include('layouts.navigation')

            <div class="ui-content">
                <!-- Page Heading -->
                @isset($header)
                    <header class="ui-page-header">
                        {{ $header }}
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="ui-main">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="ui-footer">
                    <span>&copy; {{ date('Y') }} Vinculación ISTAM · Todos los derechos reservados</span>
                    <span class="ui-footer-ver">v1.0</span>
                </footer>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        @auth
            @if (Auth::user()->role === 'estudiante')
                <x-chat-ayuda />
            @endif
        @endauth
    </body>
</html>
