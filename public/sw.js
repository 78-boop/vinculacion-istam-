/* Service worker de Vinculación ISTAM
   - Páginas: siempre desde la red (los datos deben estar al día); sin conexión muestra /offline.html
   - Archivos estáticos (css, js, imágenes, fuentes): se guardan en caché para cargar más rápido */

const VERSION = 'istam-v1';
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/icons/icon-192.png', '/images/logo-istam.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(VERSION).then((cache) => cache.addAll(PRECACHE)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((claves) => Promise.all(claves.filter((c) => c !== VERSION).map((c) => caches.delete(c))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // Navegación entre páginas: red primero, página offline si falla
    if (req.mode === 'navigate') {
        event.respondWith(fetch(req).catch(() => caches.match(OFFLINE_URL)));
        return;
    }

    // Estáticos del mismo sitio o de fuentes: caché primero, se actualiza en segundo plano
    const esEstatico = /\.(css|js|png|jpe?g|webp|svg|gif|woff2?|ico)$/i.test(url.pathname)
        || url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com';

    if (esEstatico && !url.pathname.startsWith('/storage/')) {
        event.respondWith(
            caches.open(VERSION).then(async (cache) => {
                const enCache = await cache.match(req);
                const red = fetch(req).then((resp) => {
                    if (resp.ok || resp.type === 'opaque') cache.put(req, resp.clone());
                    return resp;
                }).catch(() => enCache);
                return enCache || red;
            })
        );
    }
});
