<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">

        @if($inscripciones->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">No tienes inscripciones activas en ningún proyecto de vinculación.</p>
            </div>
        @else
            @foreach($inscripciones as $inscripcion)
                <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                    <!-- Header del Proyecto -->
                    <div class="border-b-2 pb-4 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $inscripcion->proyecto->nombre }}</h2>
                        <p class="text-gray-600 mt-1">{{ $inscripcion->proyecto->descripcion }}</p>
                    </div>

                    <!-- Información de Progreso -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-gray-600 text-sm font-semibold">HORAS REGISTRADAS</p>
                            <div class="flex items-baseline gap-2 mt-2">
                                <span class="text-3xl font-bold text-blue-600" id="horas-registradas-{{ $inscripcion->id }}">
                                    {{ $inscripcion->registrosHoras->sum('horas_registradas') }}
                                </span>
                                <span class="text-gray-600">/ {{ $inscripcion->horas_requeridas }}</span>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-gray-600 text-sm font-semibold">HORAS APROBADAS</p>
                            <div class="flex items-baseline gap-2 mt-2">
                                <span class="text-3xl font-bold text-green-600" id="horas-aprobadas-{{ $inscripcion->id }}">
                                    {{ $inscripcion->registrosHoras->where('estado', 'aprobada')->sum('horas_registradas') }}
                                </span>
                                <span class="text-gray-600">/ {{ $inscripcion->horas_requeridas }}</span>
                            </div>
                        </div>

                        <div class="rounded-lg p-4" id="estado-{{ $inscripcion->id }}"
                             data-inscripcion="{{ $inscripcion->id }}">
                            <p class="text-gray-600 text-sm font-semibold">ESTADO</p>
                            <div class="mt-2">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold text-white bg-yellow-500">
                                    Pendiente
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Progreso -->
                    <div class="mb-8">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Progreso</span>
                            <span class="text-sm font-semibold text-gray-700" id="porcentaje-{{ $inscripcion->id }}">
                                {{ round(($inscripcion->registrosHoras->sum('horas_registradas') / ($inscripcion->horas_requeridas ?: 90)) * 100) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all"
                                 style="width: {{ min(100, round(($inscripcion->registrosHoras->sum('horas_registradas') / ($inscripcion->horas_requeridas ?: 90)) * 100)) }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Botón Registrar Horas Diarias -->
                    <div class="mb-8">
                        <button class="btn-registrar-horas bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition"
                                data-inscripcion="{{ $inscripcion->id }}">
                            + Registrar Horas Diarias
                        </button>
                    </div>

                    <!-- Modal de Registro de Horas -->
                    <div id="modal-registro-{{ $inscripcion->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                        <div class="bg-white rounded-lg p-6 sm:p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                            <h3 class="text-xl font-bold mb-6">Registrar Horas del Día</h3>

                            <form class="form-registro-horas" data-inscripcion="{{ $inscripcion->id }}">
                                @csrf
                                <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">

                                <!-- Fecha -->
                                <div class="mb-6">
                                    <label class="block text-gray-700 font-bold mb-2">Fecha</label>
                                    <input type="date" name="fecha" required max="{{ date('Y-m-d') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                </div>

                                <!-- Horas Registradas -->
                                <div class="mb-6">
                                    <label class="block text-gray-700 font-bold mb-2">Horas Registradas (0.5 - 8)</label>
                                    <input type="number" name="horas_registradas" step="0.5" min="0.5" max="8" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                </div>

                                <!-- Descripción de Actividad -->
                                <div class="mb-6">
                                    <label class="block text-gray-700 font-bold mb-2">Descripción de la Actividad</label>
                                    <textarea name="descripcion" rows="4" required
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                              placeholder="Describe las actividades que realizaste..."></textarea>
                                </div>

                                <!-- Evidencias (Imágenes) -->
                                <div class="mb-6">
                                    <label class="block text-gray-700 font-bold mb-2">Subir Evidencias (Imágenes)</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition"
                                         onclick="document.getElementById('file-input-{{ $inscripcion->id }}').click()">
                                        <p class="text-gray-600">Arrastra archivos aquí o haz clic para seleccionar</p>
                                        <p class="text-sm text-gray-500 mt-1">Máximo 5 imágenes, 5MB cada una</p>
                                    </div>
                                    <input type="file" id="file-input-{{ $inscripcion->id }}" name="evidencias[]"
                                           multiple accept="image/*" class="hidden">
                                    <div id="preview-{{ $inscripcion->id }}" class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4"></div>
                                </div>

                                <!-- Botones -->
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition flex-1 order-1 sm:order-none">
                                        Registrar Horas
                                    </button>
                                    <button type="button" class="btn-cerrar-modal bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition"
                                            data-inscripcion="{{ $inscripcion->id }}">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de Registros Existentes -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 text-gray-800">Registros de Horas</h3>
                        @if($inscripcion->registrosHoras->isEmpty())
                            <p class="text-gray-500 py-4">No hay registros de horas aún.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-4 py-2 text-left">Fecha</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">Horas</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">Descripción</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left">Evidencias</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inscripcion->registrosHoras->sortByDesc('fecha') as $registro)
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $registro->fecha->format('d/m/Y') }}</td>
                                                <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $registro->horas_registradas }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-sm">{{ Str::limit($registro->descripcion, 50) }}</td>
                                                <td class="border border-gray-300 px-4 py-2">
                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                        @if($registro->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                                        @elseif($registro->estado === 'aprobada') bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst($registro->estado) }}
                                                    </span>
                                                </td>
                                                <td class="border border-gray-300 px-4 py-2">
                                                    <span class="text-blue-600 cursor-pointer" onclick="verEvidencias({{ $registro->id }})">
                                                        0 archivo(s)
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Botón Solicitar Certificado -->
                    @if($inscripcion->registrosHoras->where('estado', 'aprobada')->sum('horas_registradas') >= ($inscripcion->horas_requeridas ?: 90))
                        <div class="mt-8">
                            <button class="btn-solicitar-certificado bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-lg transition"
                                    data-inscripcion="{{ $inscripcion->id }}">
                                📄 Solicitar Certificado de Horas
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Abre modal de registro
    document.querySelectorAll('.btn-registrar-horas').forEach(btn => {
        btn.addEventListener('click', function() {
            const inscripcionId = this.dataset.inscripcion;
            document.getElementById('modal-registro-' + inscripcionId).classList.remove('hidden');
        });
    });

    // Cierra modal
    document.querySelectorAll('.btn-cerrar-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const inscripcionId = this.dataset.inscripcion;
            document.getElementById('modal-registro-' + inscripcionId).classList.add('hidden');
        });
    });

    // Maneja archivos
    document.querySelectorAll('[id^="file-input-"]').forEach(input => {
        input.addEventListener('change', function() {
            const inscripcionId = this.id.replace('file-input-', '');
            const preview = document.getElementById('preview-' + inscripcionId);
            preview.innerHTML = '';

            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-32 object-cover rounded-lg';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    });

    // Envía formulario
    document.querySelectorAll('.form-registro-horas').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const inscripcionId = form.dataset.inscripcion;
            const formData = new FormData(form);

            try {
                const response = await fetch('/api/registro-horas', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ Horas registradas exitosamente');
                    document.getElementById('modal-registro-' + inscripcionId).classList.add('hidden');
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + (data.error || 'Intenta de nuevo'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error al registrar las horas');
            }
        });
    });

    // Solicitar certificado
    document.querySelectorAll('.btn-solicitar-certificado').forEach(btn => {
        btn.addEventListener('click', async function() {
            const inscripcionId = this.dataset.inscripcion;

            try {
                const response = await fetch('/api/solicitar-certificado', {
                    method: 'POST',
                    body: JSON.stringify({ inscripcion_id: inscripcionId }),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('✅ ' + data.message);
                    window.location.reload();
                } else {
                    alert('❌ Error: ' + (data.error || 'Intenta de nuevo'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error al solicitar el certificado');
            }
        });
    });
});

function verEvidencias(registroId) {
    alert('Ver evidencias del registro #' + registroId);
    // Implementar modal para ver imágenes
}
</script>
</x-app-layout>
