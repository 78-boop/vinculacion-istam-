<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">

        <a href="{{ route('docente.certificados.index') }}" class="text-sm text-indigo-600 hover:underline">← Volver a la lista</a>

        <div class="bg-white rounded-lg shadow-lg p-6 mt-4">
            <div class="border-b-2 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ $inscripcion->estudiante->name }}</h2>
                <p class="text-gray-600 mt-1">{{ $inscripcion->proyecto->nombre }}</p>
            </div>

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

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="py-2 pr-4">Código</th>
                            <th class="py-2 pr-4">Documento</th>
                            <th class="py-2 pr-4">Archivo</th>
                            <th class="py-2 pr-4">Subido el</th>
                            <th class="py-2 pr-4">Estado</th>
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
                                    'pendiente' => 'Pendiente',
                                    'aprobado' => 'Aprobado',
                                    'rechazado' => 'Rechazado',
                                ];
                            @endphp
                            <tr id="fila-certificado-{{ $subido->id ?? 'na' }}">
                                <td class="py-3 pr-4 font-mono text-sm text-gray-700">{{ $tipo->codigo }}</td>
                                <td class="py-3 pr-4 text-sm text-gray-800">{{ $tipo->nombre }}</td>
                                <td class="py-3 pr-4">
                                    @if($subido)
                                        <a href="{{ route('certificados-estudiante.descargar', $subido->id) }}"
                                           target="_blank" class="text-blue-600 text-sm hover:underline">Ver archivo</a>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $subido?->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="estado-badge px-2 py-1 rounded-full text-xs font-semibold {{ $colores[$estado] }}">
                                        {{ $etiquetas[$estado] }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">
                                    @if($subido && $estado === 'pendiente')
                                        <div class="flex items-center gap-2">
                                            <button class="btn-aprobar bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition"
                                                    data-certificado="{{ $subido->id }}">
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition"
                                                    data-certificado="{{ $subido->id }}">
                                                Rechazar
                                            </button>
                                        </div>
                                    @elseif($estado === 'rechazado')
                                        <span class="text-xs text-red-700">Motivo: {{ $subido->observaciones_docente }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-aprobar').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.dataset.certificado;
            fetch(`/api/certificados-estudiante/${id}/aprobar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al aprobar');
                if (data.certificado_final_generado) {
                    alert('✅ Documento aprobado. ¡Con este se completaron los 8! Se generó el certificado final de horas cumplidas.');
                } else {
                    alert('✅ Documento aprobado.');
                }
                window.location.reload();
            })
            .catch((err) => alert('❌ ' + err.message));
        });
    });

    document.querySelectorAll('.btn-rechazar').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.certificado;
            const resultado = await Swal.fire({
                icon: 'warning',
                title: 'Rechazar documento',
                text: 'El estudiante podrá ver este motivo.',
                input: 'textarea',
                inputLabel: 'Motivo del rechazo',
                inputPlaceholder: 'Escribe el motivo...',
                inputAttributes: { 'aria-label': 'Motivo del rechazo' },
                showCancelButton: true,
                confirmButtonText: 'Rechazar documento',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#B91C1C',
                cancelButtonColor: '#6B7280',
                reverseButtons: true,
                inputValidator: (valor) => {
                    if (!valor || valor.trim().length < 5) {
                        return 'Escribe un motivo de al menos 5 caracteres.';
                    }
                }
            });

            if (!resultado.isConfirmed) {
                return;
            }

            const motivo = resultado.value.trim();

            fetch(`/api/certificados-estudiante/${id}/rechazar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ observaciones_docente: motivo }),
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al rechazar');
                alert('✅ ' + data.message);
                window.location.reload();
            })
            .catch((err) => alert('❌ ' + err.message));
        });
    });
});
</script>
</x-app-layout>