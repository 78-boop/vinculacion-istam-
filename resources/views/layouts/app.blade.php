<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(session('success') || session('error') || session('warning') || $errors->any())
            <meta name="sistema-flash"
                  data-icon="{{ $errors->any() || session('error') ? 'error' : (session('warning') ? 'warning' : 'success') }}"
                  data-title="{{ $errors->any() || session('error') ? 'Revisa la información' : (session('warning') ? 'Aviso' : 'Operación realizada') }}"
                  data-text="{{ $errors->any() ? $errors->all()[0] : (session('success') ?? session('warning') ?? session('error')) }}">
        @endif

        <title>{{ config('app.name', 'Vinculación ISTAM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="d-flex flex-column min-vh-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-bottom py-4 mb-4">
                    <div class="container-fluid">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow-1">
                <div class="container-fluid py-4">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="mt-auto border-top">
                <div class="container-fluid py-3">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">&copy; 2026 Vinculación ISTAM. Todos los derechos reservados.</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">v1.0</small>
                        </div>
                    </div>
                </div>
            </footer>
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