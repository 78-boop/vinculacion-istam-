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
                        <p class="text-gray-600 mt-1">Certificados de Vinculación con la Sociedad</p>
                    </div>

                    <!-- Progreso general -->
                    @php
                        $subidos = $inscripcion->certificadosEstudiante;
                        $aprobados = $subidos->where('estado', 'aprobado')->count();
                        $total = $tiposCertificado->count();
                    @endphp
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Documentos aprobados</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $aprobados }} / {{ $total }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full transition-all"
                                 style="width: {{ $total > 0 ? round(($aprobados / $total) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Tabla de los 8 certificados -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                                    <th class="py-2 pr-4">Código</th>
                                    <th class="py-2 pr-4">Documento</th>
                                    <th class="py-2 pr-4">Subido el</th>
                                    <th class="py-2 pr-4">Estado</th>
                                    <th class="py-2 pr-4">Observación del docente</th>
                                    <th class="py-2 pr-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tiposCertificado as $tipo)
                                    @php
                                        $subido = $subidos->firstWhere('tipo_certificado_id', $tipo->id);
                                        $estado = $subido->estado ?? 'sin_subir';
                                        $colores = [
                                            'sin_subir' => 'bg-gray-100 text-gray-600',
                                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                                            'aprobado' => 'bg-green-100 text-green-800',
                                            'rechazado' => 'bg-red-100 text-red-800',
                                        ];
                                        $etiquetas = [
                                            'sin_subir' => 'Sin subir',
                                            'pendiente' => 'Pendiente de revisión',
                                            'aprobado' => 'Aprobado',
                                            'rechazado' => 'Rechazado',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="py-3 pr-4 font-mono text-sm text-gray-700">{{ $tipo->codigo }}</td>
                                        <td class="py-3 pr-4 text-sm text-gray-800">{{ $tipo->nombre }}</td>
                                        <td class="py-3 pr-4 text-sm text-gray-600 whitespace-nowrap">
                                            {{ $subido?->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$estado] }}">
                                                {{ $etiquetas[$estado] }}
                                            </span>
                                        </td>
                                        <td class="py-3 pr-4 text-sm text-gray-600">
                                            {{ $subido->observaciones_docente ?? '—' }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                @if($subido && $estado !== 'rechazado')
                                                    <a href="{{ route('certificados-estudiante.descargar', $subido->id) }}"
                                                       target="_blank"
                                                       class="text-blue-600 text-sm hover:underline">Ver archivo</a>
                                                @endif

                                                @if($estado !== 'aprobado')
                                                    <label class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition">
                                                        {{ $subido ? 'Reemplazar' : 'Subir' }}
                                                        <input type="file"
                                                               class="hidden input-certificado"
                                                               data-inscripcion="{{ $inscripcion->id }}"
                                                               data-tipo="{{ $tipo->id }}"
                                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                                    </label>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($aprobados >= $total && $total > 0)
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-800 font-semibold">
                                🎉 ¡Todos tus documentos fueron aprobados! Tu certificado de horas cumplidas se generó automáticamente.
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.input-certificado').forEach(function (input) {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const inscripcionId = input.dataset.inscripcion;
            const tipoId = input.dataset.tipo;

            const formData = new FormData();
            formData.append('inscripcion_id', inscripcionId);
            formData.append('tipo_certificado_id', tipoId);
            formData.append('archivo', file);

            fetch('/api/certificados-estudiante', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || data.message || 'Error al subir el archivo');
                }
                alert('✅ ' + data.message);
                window.location.reload();
            })
            .catch((error) => {
                alert('❌ ' + error.message);
            });
        });
    });
});
</script>
</x-app-layout>