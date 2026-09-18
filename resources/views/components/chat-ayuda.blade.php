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
            'respuesta' => 'Para elegir tu actividad de vinculación: andá al menú "Actividades Vinculación". Ahí vas a ver las actividades disponibles para tu carrera. Elegí una con el botón "Seleccionar" y esperá la aprobación de tu docente.',
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
            'respuesta' => 'Para inscribirte a un proyecto de vinculación, andá a "Área Personal", bajá hasta "Proyectos Disponibles" y hacé clic en "Inscribirse" en el que te interese.',
        ],
        [
            'palabras' => ['certificado final', 'aprobacion', 'aprobación', 'termine', 'terminé', 'complete', 'completé'],
            'respuesta' => 'Cuando el docente apruebe tus 8 certificados, el sistema genera automáticamente tu certificado de horas cumplidas. Después, el administrador emite tu certificado oficial de vinculación.',
        ],
    ];

    $respuestaSinCoincidencia = 'No encontré una respuesta exacta para eso. Podés reformular la pregunta, o comunicarte directamente con el instituto con los datos de contacto de abajo.';
@endphp

<div x-data="chatAyuda()" class="fixed bottom-5 right-5 z-50">

    <!-- Botón flotante -->
    <button @click="abierto = !abierto"
            class="bg-green-700 hover:bg-green-800 text-white rounded-full w-16 h-16 shadow-lg flex items-center justify-center text-3xl transition"
            aria-label="Ayuda">
        <span x-show="!abierto">💬</span>
        <span x-show="abierto" x-cloak>✕</span>
    </button>

    <!-- Panel del chat -->
    <div x-show="abierto" x-cloak
         x-transition
         class="absolute bottom-20 right-0 w-96 sm:w-[26rem] max-h-[38rem] bg-white rounded-lg shadow-2xl border border-gray-200 flex flex-col overflow-hidden">

        <!-- Encabezado -->
        <div class="bg-green-700 text-white px-4 py-3 flex items-center gap-2">
            <img src="{{ asset('images/logo-istam.png') }}" alt="ISTAM" class="w-8 h-8 rounded-full bg-white p-0.5">
            <div>
                <p class="font-semibold text-sm leading-tight">Ayuda ISTAM</p>
                <p class="text-xs text-green-100 leading-tight">Preguntame algo del sistema</p>
            </div>
        </div>

        <!-- Mensajes -->
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 bg-gray-50" x-ref="contenedorMensajes">
            <template x-for="(mensaje, index) in mensajes" :key="index">
                <div :class="mensaje.propio ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="mensaje.propio
                            ? 'bg-green-600 text-white rounded-lg rounded-br-none px-3 py-2 text-sm max-w-[85%]'
                            : 'bg-white text-gray-800 rounded-lg rounded-bl-none px-3 py-2 text-sm max-w-[85%] border border-gray-200'"
                         x-text="mensaje.texto">
                    </div>
                </div>
            </template>
        </div>

        <!-- Preguntas rápidas -->
        <div class="px-3 pt-2 pb-1 flex flex-wrap gap-1 bg-gray-50 border-t border-gray-100">
            <button @click="preguntar('¿Cómo subo un certificado?')" class="text-xs bg-white border border-gray-300 rounded-full px-2 py-1 hover:bg-gray-100">Subir certificado</button>
            <button @click="preguntar('¿Cómo elijo una actividad?')" class="text-xs bg-white border border-gray-300 rounded-full px-2 py-1 hover:bg-gray-100">Elegir actividad</button>
            <button @click="preguntar('Olvidé mi contraseña')" class="text-xs bg-white border border-gray-300 rounded-full px-2 py-1 hover:bg-gray-100">Contraseña</button>
        </div>

        <!-- Input -->
        <form @submit.prevent="preguntar(textoInput); textoInput = ''" class="flex items-center gap-2 p-2 border-t border-gray-200 bg-white">
            <input type="text" x-model="textoInput" placeholder="Escribí tu pregunta..."
                   class="flex-1 text-sm border border-gray-300 rounded-full px-3 py-1.5 focus:outline-none focus:border-green-500">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm">➤</button>
        </form>

        <!-- Contacto + mapa -->
        <div class="border-t border-gray-200 bg-white">
            <button @click="mostrarContacto = !mostrarContacto" class="w-full text-left px-3 py-2 text-xs font-semibold text-green-700 hover:bg-gray-50 flex items-center justify-between">
                <span>📍 Contactar al instituto</span>
                <span x-text="mostrarContacto ? '▲' : '▼'"></span>
            </button>
            <div x-show="mostrarContacto" x-cloak class="px-3 pb-3 text-xs text-gray-700 space-y-2">
                <p>📞 <a href="https://wa.me/593994778820" target="_blank" class="text-green-700 hover:underline">+593 99 477 8820</a> (WhatsApp)</p>
                <p>📘 <a href="https://www.facebook.com/ISTAmazonico/" target="_blank" class="text-green-700 hover:underline">Facebook: Instituto Superior Tecnológico Amazónico</a></p>
                <p>📍 Av. Primero de Mayo, Yantzaza, Zamora Chinchipe, Ecuador</p>
                <div class="rounded-lg overflow-hidden border border-gray-200">
                    <iframe
                        src="https://maps.google.com/maps?q=Instituto%20Superior%20Tecnol%C3%B3gico%20Amaz%C3%B3nico%2C%20Av%20Primero%20de%20Mayo%2C%20Yantzaza&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        width="100%" height="220" style="border:0;" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function chatAyuda() {
    return {
        abierto: false,
        mostrarContacto: false,
        textoInput: '',
        preguntasFrecuentes: @json($preguntasFrecuentes),
        respuestaSinCoincidencia: @json($respuestaSinCoincidencia),
        mensajes: [
            { propio: false, texto: '¡Hola! Soy el asistente de ayuda, preguntame cualquier duda que tengas, estoy para ayudarte.' }
        ],

        preguntar(texto) {
            if (!texto || !texto.trim()) return;

            this.mensajes.push({ propio: true, texto: texto });

            const textoNormalizado = texto.toLowerCase();
            const coincidencia = this.preguntasFrecuentes.find(item =>
                item.palabras.some(palabra => textoNormalizado.includes(palabra))
            );

            const respuesta = coincidencia ? coincidencia.respuesta : this.respuestaSinCoincidencia;
            this.mensajes.push({ propio: false, texto: respuesta });

            this.textoInput = '';
            this.$nextTick(() => {
                this.$refs.contenedorMensajes.scrollTop = this.$refs.contenedorMensajes.scrollHeight;
            });
        }
    }
}
</script>