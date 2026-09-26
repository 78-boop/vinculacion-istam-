@php
    // Base de preguntas frecuentes: cada entrada tiene palabras clave y una respuesta.
    // Es un buscador simple por palabra clave, no una IA — así no depende de ningún
    // servicio externo ni costo, y cubre las dudas más comunes del sistema.
    $preguntasFrecuentes = [
        [
            'palabras' => ['certificado', 'certificados', 'fpvs', 'documento', 'documentos', 'subir'],
            'respuesta' => 'Para subir tus certificados: andá al menú "Certificados", vas a ver los 8 documentos requeridos (FPVS01 al FPVS10). Hacé clic en "Subir" al lado de cada uno y seleccioná el archivo (PDF, imagen o Word/Excel). Tu docente los va a revisar y aprobar uno por uno.',
        ],
        [
            'palabras' => ['rechaz', 'rechazado', 'rechazaron'],
            'respuesta' => 'Si un certificado te aparece "Rechazado", fijate el motivo que dejó tu docente en esa fila. Corregí el documento y volvé a subirlo con el mismo botón — se reemplaza automáticamente, no hace falta hacer nada más.',
        ],
        [
            'palabras' => ['actividad', 'actividades', 'vinculacion', 'vinculación', 'elegir', 'seleccionar'],
            'respuesta' => 'Para elegir tu actividad: entra al menú "Proyectos disponibles", abre un proyecto y verás sus actividades con fechas, lugar y horas. Pulsa "Inscribirme" en la que quieras. Recuerda que solo puedes participar en UNA actividad.',
        ],
        [
            'palabras' => ['carrera'],
            'respuesta' => 'Tu carrera la asigna el administrador del sistema. Si no ves actividades disponibles o creés que tu carrera está mal, contactá al instituto con los datos de abajo.',
        ],
        [
            'palabras' => ['hora', 'horas', 'cumplidas'],
            'respuesta' => 'Las horas cumplidas las registra tu docente tutor según tu carrera (cada carrera tiene un número distinto de horas requeridas). Si tenés dudas sobre tus horas, consultale directamente a tu docente.',
        ],
        [
            'palabras' => ['contraseña', 'clave', 'password', 'olvide', 'olvidé', 'recuperar'],
            'respuesta' => 'Si olvidaste tu contraseña, usá la opción "¿Olvidaste tu contraseña?" en la pantalla de inicio de sesión. Si no te llega el correo, contactá al instituto con los datos de abajo para que el administrador te ayude.',
        ],
        [
            'palabras' => ['proyecto', 'inscrib', 'inscripcion', 'inscripción'],
            'respuesta' => 'Para participar en un proyecto: entra a "Proyectos disponibles", abre el proyecto y pulsa "Inscribirme" en una de sus actividades. Al hacerlo quedas inscrito en el proyecto automáticamente. Solo puedes elegir una actividad.',
        ],
        [
            'palabras' => ['certificado final', 'aprobacion', 'aprobación', 'termine', 'terminé', 'complete', 'completé'],
            'respuesta' => 'Cuando el docente apruebe tus 8 certificados, el sistema genera automáticamente tu certificado de horas cumplidas. Después, el administrador emite tu certificado oficial de vinculación.',
        ],
    ];

    $respuestaSinCoincidencia = 'No encontré una respuesta exacta para eso. Podés reformular la pregunta, o comunicarte directamente con el instituto desde la pestaña "Contacto".';

    $preguntasRapidas = [
        ['📄', 'Subir documentos', '¿Cómo subo un certificado?'],
        ['🎯', 'Elegir actividad', '¿Cómo elijo una actividad?'],
        ['📁', 'Inscribirme', '¿Cómo me inscribo en un proyecto?'],
        ['⏱️', 'Mis horas', '¿Quién registra mis horas?'],
        ['❌', 'Doc. rechazado', 'Me rechazaron un documento'],
        ['🔑', 'Contraseña', 'Olvidé mi contraseña'],
    ];

    $primerNombre = \Illuminate\Support\Str::before(trim(Auth::user()->name ?? '') . ' ', ' ');
@endphp

<style>
    .ca { position: fixed; right: 22px; bottom: 22px; z-index: 50; font-family: var(--ui-fuente, system-ui, sans-serif); }

    /* ---------- Botón flotante ---------- */
    .ca-fab {
        position: relative; width: 64px; height: 64px; border-radius: 22px; border: 0; cursor: pointer;
        display: grid; place-items: center; color: #fff;
        background: linear-gradient(135deg, #00A36C 0%, #006B47 55%, #053324 100%);
        box-shadow: 0 16px 34px -12px rgba(0, 107, 71, .85), inset 0 1px 0 rgba(255,255,255,.25);
        transition: transform .25s cubic-bezier(.2,.9,.3,1.3), border-radius .25s, box-shadow .25s;
    }
    .ca-fab:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 20px 40px -12px rgba(0, 107, 71, .95); }
    .ca-fab:focus-visible { outline: 3px solid #A3D65C; outline-offset: 3px; }
    .ca-fab.is-abierto { border-radius: 50%; transform: rotate(0deg); }
    .ca-fab svg { width: 30px; height: 30px; transition: transform .3s, opacity .2s; }
    .ca-fab .ca-ico-cerrar { position: absolute; opacity: 0; transform: rotate(-90deg) scale(.6); }
    .ca-fab.is-abierto .ca-ico-chat { opacity: 0; transform: rotate(90deg) scale(.6); }
    .ca-fab.is-abierto .ca-ico-cerrar { opacity: 1; transform: none; }

    /* Onda animada alrededor del botón */
    .ca-fab::before, .ca-fab::after {
        content: ""; position: absolute; inset: 0; border-radius: inherit; pointer-events: none;
        border: 2px solid rgba(0, 163, 108, .55); animation: ca-onda 2.6s ease-out infinite;
    }
    .ca-fab::after { animation-delay: 1.3s; }
    .ca-fab.is-abierto::before, .ca-fab.is-abierto::after { animation: none; opacity: 0; }
    @keyframes ca-onda { from { transform: scale(1); opacity: .9; } to { transform: scale(1.6); opacity: 0; } }

    /* Punto de notificación */
    .ca-punto {
        position: absolute; top: -4px; right: -4px; min-width: 22px; height: 22px; padding: 0 6px;
        border-radius: 99px; background: #EF4444; color: #fff; font-size: 12px; font-weight: 800;
        display: grid; place-items: center; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(239,68,68,.45);
    }

    /* Burbuja "¿Necesitas ayuda?" */
    .ca-globo {
        position: absolute; right: 78px; bottom: 10px; width: max-content; max-width: 230px;
        background: #fff; color: #0F2A20; padding: 12px 34px 12px 14px; border-radius: 16px 16px 4px 16px;
        box-shadow: 0 16px 36px -12px rgba(3, 36, 26, .35), 0 0 0 1px rgba(15, 42, 32, .06);
        font-size: 13.5px; line-height: 1.4; cursor: pointer;
    }
    .ca-globo strong { display: block; font-size: 14px; margin-bottom: 2px; }
    .ca-globo button {
        position: absolute; top: 6px; right: 6px; width: 22px; height: 22px; border-radius: 50%; border: 0;
        background: #F1F5F3; color: #5B6B63; cursor: pointer; font-size: 14px; line-height: 1;
    }

    /* ---------- Panel ---------- */
    .ca-panel {
        position: absolute; right: 0; bottom: 82px; width: 390px; height: min(600px, calc(100vh - 140px));
        display: flex; flex-direction: column; overflow: hidden;
        background: #F6F9F7; border-radius: 26px;
        box-shadow: 0 30px 70px -20px rgba(3, 36, 26, .5), 0 0 0 1px rgba(15, 42, 32, .06);
        transform-origin: bottom right;
    }
    .ca-entrar { transition: opacity .22s ease, transform .22s cubic-bezier(.2,.9,.3,1.2); }
    .ca-desde { opacity: 0; transform: translateY(14px) scale(.94); }
    .ca-hasta { opacity: 1; transform: none; }

    .ca-cab {
        position: relative; padding: 18px 18px 0; color: #fff; overflow: hidden;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(163, 214, 92, .35), transparent 55%),
            linear-gradient(135deg, #008457, #006B47 50%, #053324);
    }
    .ca-cab::after {
        content: ""; position: absolute; right: -40px; top: -60px; width: 160px; height: 160px; border-radius: 50%;
        border: 22px solid rgba(255,255,255,.06);
    }
    .ca-cab-fila { position: relative; z-index: 1; display: flex; align-items: center; gap: 12px; }
    .ca-avatar { position: relative; width: 46px; height: 46px; border-radius: 15px; background: #fff; display: grid; place-items: center; flex-shrink: 0; box-shadow: 0 6px 16px -6px rgba(0,0,0,.4); }
    .ca-avatar img { width: 32px; height: 32px; object-fit: contain; }
    .ca-avatar::after { content: ""; position: absolute; right: -3px; bottom: -3px; width: 14px; height: 14px; border-radius: 50%; background: #4ADE80; border: 3px solid #006B47; }
    .ca-cab-txt { flex: 1; min-width: 0; }
    .ca-cab-txt strong { display: block; font-size: 16px; font-weight: 800; }
    .ca-cab-txt span { font-size: 12.5px; color: #CFEADC; display: flex; align-items: center; gap: 6px; }
    .ca-cab-txt span::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px #4ADE80; }
    .ca-cerrar { width: 36px; height: 36px; border-radius: 12px; border: 0; background: rgba(255,255,255,.14); color: #fff; cursor: pointer; display: grid; place-items: center; }
    .ca-cerrar:hover { background: rgba(255,255,255,.24); }
    .ca-cerrar svg { width: 18px; height: 18px; }

    .ca-tabs { position: relative; z-index: 1; display: flex; gap: 4px; margin-top: 16px; }
    .ca-tab {
        flex: 1; padding: 10px 0 12px; border: 0; background: none; cursor: pointer; font-family: inherit;
        font-size: 13.5px; font-weight: 700; color: rgba(255,255,255,.7); border-radius: 14px 14px 0 0; transition: background .15s, color .15s;
        display: flex; align-items: center; justify-content: center; gap: 7px;
    }
    .ca-tab svg { width: 16px; height: 16px; }
    .ca-tab:hover { color: #fff; }
    .ca-tab.is-activa { background: #F6F9F7; color: #006B47; }

    /* Mensajes */
    .ca-mensajes { flex: 1; overflow-y: auto; padding: 18px 16px 8px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth; }
    .ca-fecha { align-self: center; font-size: 11px; font-weight: 700; color: #7A8C83; background: #E9F0EC; padding: 4px 10px; border-radius: 99px; }
    .ca-msg { display: flex; gap: 8px; align-items: flex-end; max-width: 88%; animation: ca-msg .28s ease-out; }
    .ca-msg.propio { align-self: flex-end; flex-direction: row-reverse; }
    .ca-msg-av { width: 28px; height: 28px; border-radius: 10px; background: #fff; display: grid; place-items: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
    .ca-msg-av img { width: 20px; height: 20px; object-fit: contain; }
    .ca-burbuja {
        padding: 10px 13px; border-radius: 18px 18px 18px 6px; font-size: 13.8px; line-height: 1.5;
        background: #fff; color: #1F3329; box-shadow: 0 2px 8px -2px rgba(6, 40, 28, .12);
        word-wrap: break-word;
    }
    .ca-texto { white-space: pre-line; }
    .ca-msg.propio .ca-burbuja { border-radius: 18px 18px 6px 18px; background: linear-gradient(135deg, #00875A, #006B47); color: #fff; }
    .ca-hora { display: block; font-size: 10.5px; margin-top: 4px; opacity: .6; text-align: right; }
    @keyframes ca-msg { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

    .ca-escribiendo { display: inline-flex; gap: 4px; padding: 14px 16px; }
    .ca-escribiendo i { width: 7px; height: 7px; border-radius: 50%; background: #8BB5A1; animation: ca-dot 1.2s infinite; }
    .ca-escribiendo i:nth-child(2) { animation-delay: .15s; }
    .ca-escribiendo i:nth-child(3) { animation-delay: .3s; }
    @keyframes ca-dot { 0%, 60%, 100% { transform: translateY(0); opacity: .5; } 30% { transform: translateY(-5px); opacity: 1; } }

    /* Preguntas rápidas */
    .ca-rapidas { display: flex; gap: 8px; overflow-x: auto; padding: 8px 16px 10px; scrollbar-width: none; }
    .ca-rapidas::-webkit-scrollbar { display: none; }
    .ca-chip {
        flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 99px;
        border: 1px solid #CFEADC; background: #fff; color: #0B5B3C; font-family: inherit; font-size: 12.5px; font-weight: 700;
        cursor: pointer; transition: background .15s, transform .15s, border-color .15s;
    }
    .ca-chip:hover { background: #E7F5EE; border-color: #006B47; transform: translateY(-1px); }

    /* Entrada */
    .ca-form { display: flex; align-items: center; gap: 8px; margin: 0 12px 12px; padding: 6px 6px 6px 16px; background: #fff; border-radius: 18px; box-shadow: 0 4px 16px -8px rgba(6, 40, 28, .25), 0 0 0 1px #E3EAE6; transition: box-shadow .15s; }
    .ca-form:focus-within { box-shadow: 0 4px 16px -8px rgba(6, 40, 28, .25), 0 0 0 2px #006B47; }
    .ca-form input { flex: 1; min-width: 0; border: 0 !important; outline: 0; background: none; font-family: inherit; font-size: 14px; color: #0F2A20; padding: 6px 0; box-shadow: none !important; }
    .ca-enviar { width: 40px; height: 40px; border-radius: 13px; border: 0; cursor: pointer; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #00875A, #006B47); transition: transform .15s, opacity .15s; }
    .ca-enviar:disabled { opacity: .4; cursor: default; }
    .ca-enviar:not(:disabled):hover { transform: scale(1.06); }
    .ca-enviar svg { width: 18px; height: 18px; }

    /* Contacto */
    .ca-contacto { flex: 1; overflow-y: auto; padding: 18px 16px; display: flex; flex-direction: column; gap: 10px; }
    .ca-contacto > p { margin: 0 0 4px; font-size: 13.5px; color: #4A5F55; }
    .ca-canal {
        display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 16px; background: #fff;
        text-decoration: none !important; color: #0F2A20 !important; box-shadow: 0 2px 8px -3px rgba(6, 40, 28, .15);
        transition: transform .15s, box-shadow .15s;
    }
    a.ca-canal:hover { transform: translateX(3px); box-shadow: 0 8px 18px -8px rgba(6, 40, 28, .3); }
    .ca-canal-ico { width: 42px; height: 42px; border-radius: 13px; display: grid; place-items: center; flex-shrink: 0; color: #fff; }
    .ca-canal-ico svg { width: 22px; height: 22px; }
    .ca-canal strong { display: block; font-size: 14px; }
    .ca-canal small { font-size: 12.5px; color: #6B7C74; }
    .ca-canal .ca-ir { margin-left: auto; width: 18px; height: 18px; color: #9AAAA2; }
    .ca-mapa { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px -3px rgba(6, 40, 28, .15); background: #E9F0EC; }
    .ca-mapa iframe { display: block; width: 100%; height: 200px; border: 0; }

    /* ---------- Celular ---------- */
    @media (max-width: 1023.98px) {
        .ca { right: 14px; bottom: calc(84px + env(safe-area-inset-bottom)); }
        .ca-fab { width: 56px; height: 56px; border-radius: 19px; }
        .ca-fab svg { width: 26px; height: 26px; }
        .ca-panel {
            position: fixed; left: 10px; right: 10px; width: auto;
            bottom: calc(150px + env(safe-area-inset-bottom));
            height: min(560px, calc(100dvh - 240px - env(safe-area-inset-bottom) - env(safe-area-inset-top)));
        }
        .ca-globo { right: 68px; bottom: 6px; max-width: 200px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .ca-fab::before, .ca-fab::after, .ca-msg, .ca-escribiendo i { animation: none; }
    }
</style>

<div class="ca" x-data="chatAyuda()" x-init="iniciar()" @keydown.escape.window="abierto && cerrar()">

    {{-- Burbuja de invitación --}}
    <div class="ca-globo" x-cloak x-show="globo && !abierto" x-transition.opacity.duration.300ms @click="abrir()">
        <strong>¿Necesitas ayuda, {{ $primerNombre }}? 👋</strong>
        Pregúntame lo que quieras sobre el sistema.
        <button type="button" @click.stop="ocultarGlobo()" aria-label="Cerrar mensaje">×</button>
    </div>

    {{-- Panel --}}
    <section class="ca-panel" x-cloak x-show="abierto" role="dialog" aria-label="Chat de ayuda ISTAM"
             x-transition:enter="ca-entrar" x-transition:enter-start="ca-desde" x-transition:enter-end="ca-hasta"
             x-transition:leave="ca-entrar" x-transition:leave-start="ca-hasta" x-transition:leave-end="ca-desde">

        <header class="ca-cab">
            <div class="ca-cab-fila">
                <span class="ca-avatar"><img src="{{ asset('images/logo-istam.png') }}" alt=""></span>
                <div class="ca-cab-txt">
                    <strong>Asistente ISTAM</strong>
                    <span>En línea · responde al instante</span>
                </div>
                <button type="button" class="ca-cerrar" @click="cerrar()" aria-label="Cerrar ayuda">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                </button>
            </div>
            <nav class="ca-tabs" role="tablist">
                <button type="button" class="ca-tab" :class="pestana === 'chat' && 'is-activa'" @click="pestana = 'chat'" role="tab" :aria-selected="pestana === 'chat'">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 6 2.5-3.5H18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v14Z"/></svg>
                    Chat
                </button>
                <button type="button" class="ca-tab" :class="pestana === 'contacto' && 'is-activa'" @click="pestana = 'contacto'" role="tab" :aria-selected="pestana === 'contacto'">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                    Contacto
                </button>
            </nav>
        </header>

        {{-- ===== Pestaña Chat ===== --}}
        <template x-if="pestana === 'chat'">
            <div style="display:flex;flex-direction:column;flex:1;min-height:0;">
                <div class="ca-mensajes" x-ref="contenedorMensajes" aria-live="polite">
                    <span class="ca-fecha">Hoy</span>
                    <template x-for="(mensaje, index) in mensajes" :key="index">
                        <div class="ca-msg" :class="mensaje.propio && 'propio'">
                            <template x-if="!mensaje.propio">
                                <span class="ca-msg-av"><img src="{{ asset('images/logo-istam.png') }}" alt=""></span>
                            </template>
                            <div class="ca-burbuja">
                                <template x-if="mensaje.escribiendo">
                                    <span class="ca-escribiendo" aria-label="Escribiendo"><i></i><i></i><i></i></span>
                                </template>
                                <template x-if="!mensaje.escribiendo">
                                    <div>
                                        <span class="ca-texto" x-text="mensaje.texto"></span>
                                        <span class="ca-hora" x-text="mensaje.hora"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="ca-rapidas" aria-label="Preguntas rápidas">
                    @foreach ($preguntasRapidas as [$emoji, $etiqueta, $pregunta])
                        <button type="button" class="ca-chip" @click="preguntar(@js($pregunta))">{{ $emoji }} {{ $etiqueta }}</button>
                    @endforeach
                </div>

                <form class="ca-form" @submit.prevent="preguntar(textoInput)">
                    <input type="text" x-model="textoInput" x-ref="entrada" placeholder="Escribe tu pregunta..." aria-label="Escribe tu pregunta" autocomplete="off">
                    <button type="submit" class="ca-enviar" :disabled="!textoInput.trim() || ocupado" aria-label="Enviar">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m-6-7 7 7-7 7"/></svg>
                    </button>
                </form>
            </div>
        </template>

        {{-- ===== Pestaña Contacto ===== --}}
        <template x-if="pestana === 'contacto'">
            <div class="ca-contacto">
                <p>¿Prefieres hablar con una persona? Comunícate con el instituto:</p>

                <a class="ca-canal" href="https://wa.me/593994778820" target="_blank" rel="noopener">
                    <span class="ca-canal-ico" style="background:#25D366">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
                    </span>
                    <div><strong>WhatsApp</strong><small>+593 99 477 8820</small></div>
                    <svg class="ca-ir" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/></svg>
                </a>

                <a class="ca-canal" href="https://www.facebook.com/ISTAmazonico/" target="_blank" rel="noopener">
                    <span class="ca-canal-ico" style="background:#1877F2">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H8v4h2v8h4v-8h3l1-4h-4V8Z"/></svg>
                    </span>
                    <div><strong>Facebook</strong><small>Instituto Superior Tecnológico Amazónico</small></div>
                    <svg class="ca-ir" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8"/></svg>
                </a>

                <div class="ca-canal">
                    <span class="ca-canal-ico" style="background:#DC2626">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                    </span>
                    <div><strong>Dirección</strong><small>Av. Primero de Mayo, Yantzaza, Zamora Chinchipe, Ecuador</small></div>
                </div>

                <div class="ca-mapa">
                    <iframe title="Mapa del instituto"
                        src="https://maps.google.com/maps?q=Instituto%20Superior%20Tecnol%C3%B3gico%20Amaz%C3%B3nico%2C%20Av%20Primero%20de%20Mayo%2C%20Yantzaza&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </template>
    </section>

    {{-- Botón flotante --}}
    <button type="button" class="ca-fab" :class="abierto && 'is-abierto'" @click="abierto ? cerrar() : abrir()"
            :aria-expanded="abierto" aria-label="Abrir ayuda">
        <svg class="ca-ico-chat" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M4 20l2.5-3.5H18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v14Z"/>
        </svg>
        <svg class="ca-ico-cerrar" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
        <span class="ca-punto" x-cloak x-show="!visto && !abierto">1</span>
    </button>
</div>

<script>
function chatAyuda() {
    const CLAVE = 'ayuda-istam-vista';

    return {
        abierto: false,
        pestana: 'chat',
        globo: false,
        visto: false,
        ocupado: false,
        textoInput: '',
        preguntasFrecuentes: @json($preguntasFrecuentes),
        respuestaSinCoincidencia: @json($respuestaSinCoincidencia),
        mensajes: [],

        iniciar() {
            this.mensajes.push({
                propio: false,
                texto: '¡Hola, ' + @js($primerNombre) + '! 👋 Soy el asistente de ayuda del sistema de vinculación. Elige una pregunta rápida o escríbeme tu duda.',
                hora: this.horaActual(),
            });
            try { this.visto = sessionStorage.getItem(CLAVE) === '1'; } catch (e) { this.visto = false; }
            if (!this.visto) setTimeout(() => { if (!this.abierto) this.globo = true; }, 1800);
        },

        abrir() {
            this.abierto = true;
            this.globo = false;
            this.marcarVisto();
            this.$nextTick(() => { this.bajar(); this.$refs.entrada?.focus(); });
        },

        cerrar() {
            this.abierto = false;
        },

        ocultarGlobo() {
            this.globo = false;
            this.marcarVisto();
        },

        marcarVisto() {
            this.visto = true;
            try { sessionStorage.setItem(CLAVE, '1'); } catch (e) {}
        },

        horaActual() {
            return new Date().toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
        },

        normalizar(texto) {
            return (texto || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        },

        bajar() {
            const caja = this.$refs.contenedorMensajes;
            if (caja) caja.scrollTop = caja.scrollHeight;
        },

        preguntar(texto) {
            if (!texto || !texto.trim() || this.ocupado) return;
            this.pestana = 'chat';
            this.ocupado = true;

            this.mensajes.push({ propio: true, texto: texto.trim(), hora: this.horaActual() });
            this.textoInput = '';

            const consulta = this.normalizar(texto);
            const coincidencia = this.preguntasFrecuentes.find(item =>
                item.palabras.some(palabra => consulta.includes(this.normalizar(palabra)))
            );
            const respuesta = coincidencia ? coincidencia.respuesta : this.respuestaSinCoincidencia;

            // Indicador "escribiendo..." antes de responder
            this.mensajes.push({ propio: false, escribiendo: true });
            this.$nextTick(() => this.bajar());

            setTimeout(() => {
                this.mensajes.pop();
                this.mensajes.push({ propio: false, texto: respuesta, hora: this.horaActual() });
                this.ocupado = false;
                this.$nextTick(() => { this.bajar(); this.$refs.entrada?.focus(); });
            }, 650 + Math.min(respuesta.length * 4, 900));
        },
    };
}
</script>
