{{-- Convierte el sitio en una app instalable (PWA). Incluir dentro de <head>. --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#053324">
<meta name="application-name" content="Vinculación">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Vinculación">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="format-detection" content="telephone=no">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">

<script>
    // Registro del service worker (solo funciona en https o en localhost)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
        });
    }

    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
        document.documentElement.classList.add('pwa-app');
    }
</script>
