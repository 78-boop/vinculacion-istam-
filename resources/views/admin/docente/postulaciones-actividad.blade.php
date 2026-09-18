<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Postulaciones a Actividades de Vinculación</h1>
        <p class="text-gray-600 mb-6">Estudiantes que eligieron una actividad y esperan tu aprobación.</p>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="py-3 px-4">Estudiante</th>
                            <th class="py-3 px-4">Proyecto</th>
                            <th class="py-3 px-4">Carrera</th>
                            <th class="py-3 px-4">Actividad elegida</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($postulaciones as $postulacion)
                            <tr id="fila-postulacion-{{ $postulacion->id }}">
                                <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $postulacion->inscripcion->estudiante->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $postulacion->inscripcion->proyecto->nombre }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $postulacion->actividad->carrera->nombre }}</td>
                                <td class="py-3 px-4 text-sm text-gray-800">{{ $postulacion->actividad->titulo }}</td>
                                <td class="py-3 px-4">
                                    @if($postulacion->estado === 'pendiente')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pendiente</span>
                                    @elseif($postulacion->estado === 'aprobada')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aprobada</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rechazada</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($postulacion->estado === 'pendiente')
                                        <div class="flex items-center gap-2">
                                            <button class="btn-aprobar bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition"
                                                    data-postulacion="{{ $postulacion->id }}">
                                                Aprobar
                                            </button>
                                            <button class="btn-rechazar bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition"
                                                    data-postulacion="{{ $postulacion->id }}">
                                                Rechazar
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    No hay postulaciones todavía.
                                </td>
                            </tr>
                        @endforelse
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
            const id = btn.dataset.postulacion;
            fetch(`/api/postulaciones-actividad/${id}/aprobar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al aprobar');
                alert('✅ ' + data.message);
                window.location.reload();
            })
            .catch((err) => alert('❌ ' + err.message));
        });
    });

    document.querySelectorAll('.btn-rechazar').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.dataset.postulacion;
            const motivo = prompt('Motivo del rechazo (el estudiante lo va a ver):');
            if (!motivo || motivo.trim().length < 5) {
                alert('Escribí un motivo de al menos 5 caracteres.');
                return;
            }

            fetch(`/api/postulaciones-actividad/${id}/rechazar`, {
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