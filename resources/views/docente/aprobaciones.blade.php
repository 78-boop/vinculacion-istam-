<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Panel de Aprobación de Horas</h1>

        @if($registrosPendientes->isEmpty())
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <p class="text-green-800">✅ No hay registros de horas pendientes de aprobación.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($registrosPendientes as $registro)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold">{{ $registro->inscripcion->estudiante->name }}</h3>
                                    <p class="text-blue-100 mt-1">{{ $registro->inscripcion->proyecto->nombre }}</p>
                                </div>
                                <span class="bg-yellow-500 text-white px-4 py-2 rounded-full font-semibold">
                                    PENDIENTE
                                </span>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6">
                            <!-- Información del Registro -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-600 text-sm font-semibold">FECHA DE REGISTRO</p>
                                    <p class="text-lg font-bold text-gray-800 mt-2">{{ $registro->fecha->format('d/m/Y') }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-600 text-sm font-semibold">HORAS REGISTRADAS</p>
                                    <p class="text-lg font-bold text-blue-600 mt-2">{{ $registro->horas_registradas }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-600 text-sm font-semibold">ESTADO GENERAL</p>
                                    <p class="text-lg font-bold text-orange-600 mt-2">
                                        {{ $registro->inscripcion->registrosHoras->sum('horas_registradas') }} / {{ $registro->inscripcion->horas_requeridas }}
                                    </p>
                                </div>
                            </div>

                            <!-- Descripción de la Actividad -->
                            <div class="mb-6">
                                <h4 class="font-bold text-gray-800 mb-2">Descripción de la Actividad:</h4>
                                <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $registro->descripcion }}</p>
                            </div>

                            <!-- Evidencias -->
                            @if($registro->evidencias->count() > 0)
                                <div class="mb-6">
                                    <h4 class="font-bold text-gray-800 mb-3">Evidencias ({{ $registro->evidencias->count() }} archivo(s)):</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach($registro->evidencias as $evidencia)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $evidencia->ruta_archivo) }}"
                                                     alt="Evidencia"
                                                     class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition"
                                                     onclick="abrirImagen('{{ asset('storage/' . $evidencia->ruta_archivo) }}')">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-25 rounded-lg transition pointer-events-none"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Formulario de Aprobación/Rechazo -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h4 class="font-bold text-gray-800 mb-4">Decisión del Docente</h4>

                                <form class="form-aprobacion" data-registro="{{ $registro->id }}">
                                    @csrf

                                    <!-- Observaciones -->
                                    <div class="mb-6">
                                        <label class="block text-gray-700 font-bold mb-2">Observaciones (opcional)</label>
                                        <textarea name="observaciones" rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                                  placeholder="Escribe tus observaciones sobre este registro..."></textarea>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="flex gap-4">
                                        <button type="button" class="btn-aprobar flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition"
                                                data-registro="{{ $registro->id }}">
                                            ✅ Aprobar Horas
                                        </button>
                                        <button type="button" class="btn-rechazar flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-lg transition"
                                                data-registro="{{ $registro->id }}">
                                            ❌ Rechazar
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Información del Estudiante (si completa todas las horas) -->
                            @php
                                $horasAprobadas = $registro->inscripcion->registrosHoras->where('estado', 'aprobada')->sum('horas_registradas');
                                $totalConEste = $horasAprobadas + $registro->horas_registradas;
                                $horasRequeridas = $registro->inscripcion->horas_requeridas;
                            @endphp

                            @if($totalConEste >= $horasRequeridas)
                                <div class="mt-6 bg-blue-50 border-2 border-blue-500 rounded-lg p-4">
                                    <p class="text-blue-700 font-semibold">
                                        💡 Si apruebas este registro, el estudiante habrá completado todas sus horas requeridas
                                        ({{ $totalConEste }} / {{ $horasRequeridas }})
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Resumen General -->
            <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">📊 Resumen General</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">REGISTROS PENDIENTES</p>
                        <p class="text-4xl font-bold text-orange-600 mt-2">{{ $registrosPendientes->count() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">ESTUDIANTES AFECTADOS</p>
                        <p class="text-4xl font-bold text-blue-600 mt-2">{{ $agrupados->count() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">HORAS TOTALES A REVISAR</p>
                        <p class="text-4xl font-bold text-green-600 mt-2">{{ $registrosPendientes->sum('horas_registradas') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal para ver imagen ampliada -->
<div id="modal-imagen" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative max-w-4xl mx-auto px-4">
        <img id="imagen-ampliada" src="" alt="Evidencia" class="max-h-[80vh] max-w-full object-contain">
        <button onclick="cerrarImagen()" class="absolute top-4 right-4 bg-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200">
            ✕
        </button>
    </div>
</div>

<script>
function abrirImagen(src) {
    document.getElementById('imagen-ampliada').src = src;
    document.getElementById('modal-imagen').classList.remove('hidden');
}

function cerrarImagen() {
    document.getElementById('modal-imagen').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Aprobar horas
    document.querySelectorAll('.btn-aprobar').forEach(btn => {
        btn.addEventListener('click', async function() {
            const registroId = this.dataset.registro;
            const form = this.closest('.form-aprobacion');
            const observaciones = form.querySelector('textarea[name="observaciones"]').value;

            if (confirm('¿Estás seguro de que deseas aprobar estas horas?')) {
                try {
                    const response = await fetch(`/api/registro-horas/${registroId}/aprobar`, {
                        method: 'POST',
                        body: JSON.stringify({ observaciones }),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Horas aprobadas exitosamente');
                        window.location.reload();
                    } else {
                        alert('❌ Error: ' + (data.error || 'Intenta de nuevo'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error al aprobar las horas');
                }
            }
        });
    });

    // Rechazar horas
    document.querySelectorAll('.btn-rechazar').forEach(btn => {
        btn.addEventListener('click', async function() {
            const registroId = this.dataset.registro;
            const form = this.closest('.form-aprobacion');
            const observaciones = form.querySelector('textarea[name="observaciones"]').value;

            if (!observaciones.trim()) {
                alert('⚠️ Debes proporcionar observaciones para rechazar el registro');
                return;
            }

            if (confirm('¿Estás seguro de que deseas rechazar estas horas?')) {
                try {
                    const response = await fetch(`/api/registro-horas/${registroId}/rechazar`, {
                        method: 'POST',
                        body: JSON.stringify({ observaciones }),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('✅ Registro rechazado');
                        window.location.reload();
                    } else {
                        alert('❌ Error: ' + (data.error || 'Intenta de nuevo'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('❌ Error al rechazar el registro');
                }
            }
        });
    });
});

// Cerrar modal con ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarImagen();
    }
});
</script>
</x-app-layout>