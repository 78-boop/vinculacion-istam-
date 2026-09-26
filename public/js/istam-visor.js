/* ==========================================================================
   Visor de imágenes de Vinculación ISTAM
   Clic en cualquier imagen del contenido -> se abre en grande y completa.
   - Rueda del mouse: acercar / alejar (hacia donde está el puntero)
   - Arrastrar: mover la imagen cuando está ampliada
   - Doble clic: acercar / volver al tamaño normal
   - Celular: pellizcar para zoom, arrastrar con un dedo
   - Teclado: + / - para zoom, 0 para restablecer, Esc para cerrar
   Para excluir una imagen: agregarle el atributo data-sin-zoom
   ========================================================================== */
(function () {
    'use strict';

    var MIN = 1;
    var MAX = 8;

    var visor, lienzo, img, titulo, porcentaje;
    var escala = 1, x = 0, y = 0;
    var arrastrando = false, inicioX = 0, inicioY = 0, origenX = 0, origenY = 0;
    var toques = {}, distanciaInicial = 0, escalaInicial = 1;
    var focoPrevio = null;

    function crearVisor() {
        visor = document.createElement('div');
        visor.className = 'iv';
        visor.setAttribute('role', 'dialog');
        visor.setAttribute('aria-modal', 'true');
        visor.setAttribute('aria-label', 'Visor de imagen');
        visor.innerHTML =
            '<div class="iv-barra">' +
                '<span class="iv-titulo"></span>' +
                '<div class="iv-botones">' +
                    '<button type="button" class="iv-btn" data-accion="menos" aria-label="Alejar">' + icono('M5 12h14') + '</button>' +
                    '<button type="button" class="iv-porcentaje" data-accion="reset" aria-label="Restablecer zoom">100%</button>' +
                    '<button type="button" class="iv-btn" data-accion="mas" aria-label="Acercar">' + icono('M12 5v14M5 12h14') + '</button>' +
                    '<button type="button" class="iv-btn iv-cerrar" data-accion="cerrar" aria-label="Cerrar">' + icono('M6 6l12 12M18 6 6 18') + '</button>' +
                '</div>' +
            '</div>' +
            '<div class="iv-lienzo"><img class="iv-img" alt="" draggable="false"></div>' +
            '<p class="iv-ayuda">Rueda del mouse para acercar · arrastra para mover · doble clic para ampliar</p>';

        document.body.appendChild(visor);
        lienzo = visor.querySelector('.iv-lienzo');
        img = visor.querySelector('.iv-img');
        titulo = visor.querySelector('.iv-titulo');
        porcentaje = visor.querySelector('.iv-porcentaje');

        visor.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-accion]');
            if (btn) {
                var accion = btn.dataset.accion;
                if (accion === 'cerrar') cerrar();
                if (accion === 'mas') zoomCentro(escala * 1.4);
                if (accion === 'menos') zoomCentro(escala / 1.4);
                if (accion === 'reset') restablecer();
                return;
            }
            // Clic en el fondo (fuera de la imagen) cierra
            if (e.target === lienzo && escala === 1) cerrar();
        });

        lienzo.addEventListener('wheel', function (e) {
            e.preventDefault();
            var factor = Math.exp(-e.deltaY * 0.0022);
            zoomEn(escala * factor, e.clientX, e.clientY);
        }, { passive: false });

        img.addEventListener('dblclick', function (e) {
            if (escala > 1.05) restablecer();
            else zoomEn(2.5, e.clientX, e.clientY);
        });

        // Arrastre con mouse / un dedo, pellizco con dos dedos
        lienzo.addEventListener('pointerdown', function (e) {
            toques[e.pointerId] = { x: e.clientX, y: e.clientY };
            lienzo.setPointerCapture(e.pointerId);
            var ids = Object.keys(toques);
            if (ids.length === 2) {
                distanciaInicial = distancia();
                escalaInicial = escala;
                arrastrando = false;
            } else if (ids.length === 1 && escala > 1) {
                arrastrando = true;
                inicioX = e.clientX; inicioY = e.clientY;
                origenX = x; origenY = y;
                lienzo.classList.add('is-arrastrando');
            }
        });
        lienzo.addEventListener('pointermove', function (e) {
            if (!toques[e.pointerId]) return;
            toques[e.pointerId] = { x: e.clientX, y: e.clientY };
            var ids = Object.keys(toques);
            if (ids.length === 2 && distanciaInicial) {
                var centro = puntoMedio();
                zoomEn(escalaInicial * distancia() / distanciaInicial, centro.x, centro.y);
            } else if (arrastrando) {
                x = origenX + (e.clientX - inicioX);
                y = origenY + (e.clientY - inicioY);
                aplicar(false);
            }
        });
        function soltar(e) {
            delete toques[e.pointerId];
            if (Object.keys(toques).length < 2) distanciaInicial = 0;
            if (Object.keys(toques).length === 0) {
                arrastrando = false;
                lienzo.classList.remove('is-arrastrando');
            }
        }
        lienzo.addEventListener('pointerup', soltar);
        lienzo.addEventListener('pointercancel', soltar);

        document.addEventListener('keydown', function (e) {
            if (!visor.classList.contains('is-abierto')) return;
            if (e.key === 'Escape') cerrar();
            else if (e.key === '+' || e.key === '=') zoomCentro(escala * 1.4);
            else if (e.key === '-') zoomCentro(escala / 1.4);
            else if (e.key === '0') restablecer();
        });
    }

    function icono(d) {
        return '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="' + d + '"/></svg>';
    }

    function distancia() {
        var p = Object.values(toques);
        return Math.hypot(p[0].x - p[1].x, p[0].y - p[1].y);
    }

    function puntoMedio() {
        var p = Object.values(toques);
        return { x: (p[0].x + p[1].x) / 2, y: (p[0].y + p[1].y) / 2 };
    }

    // Zoom manteniendo fijo el punto (px, py) de la pantalla
    function zoomEn(nueva, px, py) {
        nueva = Math.min(MAX, Math.max(MIN, nueva));
        var r = lienzo.getBoundingClientRect();
        var cx = px - (r.left + r.width / 2);
        var cy = py - (r.top + r.height / 2);
        x = cx - (cx - x) * (nueva / escala);
        y = cy - (cy - y) * (nueva / escala);
        escala = nueva;
        if (escala === 1) { x = 0; y = 0; }
        aplicar(false);
    }

    function zoomCentro(nueva) {
        var r = lienzo.getBoundingClientRect();
        zoomEn(nueva, r.left + r.width / 2, r.top + r.height / 2);
        aplicar(true);
    }

    function restablecer() {
        escala = 1; x = 0; y = 0;
        aplicar(true);
    }

    function aplicar(suave) {
        img.style.transition = suave ? 'transform .22s ease' : 'none';
        img.style.transform = 'translate(' + x + 'px,' + y + 'px) scale(' + escala + ')';
        porcentaje.textContent = Math.round(escala * 100) + '%';
        lienzo.classList.toggle('is-ampliado', escala > 1);
    }

    function abrir(origen) {
        if (!visor) crearVisor();
        focoPrevio = document.activeElement;
        img.src = origen.currentSrc || origen.src;
        img.alt = origen.alt || '';
        titulo.textContent = origen.alt || '';
        restablecer();
        visor.classList.add('is-abierto');
        document.documentElement.classList.add('iv-sin-scroll');
        visor.querySelector('.iv-cerrar').focus();
    }

    function cerrar() {
        visor.classList.remove('is-abierto');
        document.documentElement.classList.remove('iv-sin-scroll');
        toques = {};
        if (focoPrevio && focoPrevio.focus) focoPrevio.focus();
    }

    // ¿Esta imagen se puede ampliar?
    function esAmpliable(el) {
        if (!(el instanceof HTMLImageElement)) return false;
        if (!el.closest('.ui-main, [data-con-zoom]')) return false;
        if (el.hasAttribute('data-sin-zoom') || el.closest('[data-sin-zoom]')) return false;
        if (el.closest('a, button, label, .ui-avatar, .iv')) return false;
        if (el.classList.contains('ui-avatar')) return false;
        var r = el.getBoundingClientRect();
        return r.width >= 60 && r.height >= 60;
    }

    document.addEventListener('click', function (e) {
        var el = e.target;
        if (!esAmpliable(el)) return;
        e.preventDefault();
        abrir(el);
    });

    // Cursor de lupa en las imágenes que se pueden ampliar
    document.addEventListener('mouseover', function (e) {
        var el = e.target;
        if (el instanceof HTMLImageElement && !el.dataset.ivRevisada) {
            el.dataset.ivRevisada = '1';
            if (esAmpliable(el)) el.classList.add('iv-ampliable');
        }
    });
})();
