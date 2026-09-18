<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bienvenido {{ Auth::user()->name }}</h1>
                    <p class="text-gray-600 mt-2">Aquí puedes ver tu progreso en los proyectos de vinculación</p>
                </div>
                <a href="{{ route('certificados-estudiante.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105">
                    📄 Mis Certificados
                </a>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Proyectos Inscritos -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Proyectos Inscritos</p>
                            <p class="text-3xl font-bold text-gray-900">{{ count($inscripciones) }}</p>
                        </div>
                        <div class="text-blue-500 text-4xl">📚</div>
                    </div>
                </div>

                <!-- Certificados Aprobados -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Certificados Aprobados</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $certificadosAprobados ?? 0 }} / {{ $totalTipos ?? 8 }}</p>
                        </div>
                        <div class="text-green-500 text-4xl">✅</div>
                    </div>
                </div>

                <!-- Certificados Pendientes -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Pendientes de Revisión</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $certificadosPendientes ?? 0 }}</p>
                        </div>
                        <div class="text-yellow-500 text-4xl">⏳</div>
                    </div>
                </div>

                <!-- Certificados Rechazados -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Rechazados</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $certificadosRechazados ?? 0 }}</p>
                        </div>
                        <div class="text-red-500 text-4xl">❌</div>
                    </div>
                </div>
            </div>

            <!-- Actividad de Vinculación -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Mi Actividad de Vinculación</h2>
                    <a href="{{ route('actividades-vinculacion.index') }}" class="text-blue-600 text-sm font-medium hover:underline">Ver / elegir →</a>
                </div>
                <div class="p-6">
                    @if($actividadesAprobadas->isNotEmpty())
                        @foreach($actividadesAprobadas as $postulacion)
                            <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                                <div class="text-green-500 text-2xl">✅</div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $postulacion->actividad->titulo }}</p>
                                    <p class="text-sm text-gray-500">{{ $postulacion->actividad->carrera->nombre }} · Aprobada</p>
                                    @if($postulacion->actividad->descripcion)
                                        <p class="text-sm text-gray-600 mt-1">{{ $postulacion->actividad->descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @elseif($actividadPendiente)
                        <div class="flex items-start gap-3">
                            <div class="text-yellow-500 text-2xl">⏳</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $actividadPendiente->actividad->titulo }}</p>
                                <p class="text-sm text-gray-500">{{ $actividadPendiente->actividad->carrera->nombre }} · Pendiente de aprobación del docente</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">
                            Todavía no elegiste ninguna actividad de vinculación.
                            <a href="{{ route('actividades-vinculacion.index') }}" class="text-blue-600 hover:underline">Elegir una →</a>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Mis Proyectos -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Mis Proyectos</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inscripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($inscripciones as $inscripcion)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $inscripcion->proyecto->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $inscripcion->proyecto->docente->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $inscripcion->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Activo</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        No estás inscrito en ningún proyecto aún
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Proyectos Disponibles -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Proyectos Disponibles</h2>
                </div>
                @if($proyectosDisponibles->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="text-gray-400 mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">No hay proyectos disponibles en este momento</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach($proyectosDisponibles as $proyecto)
                            <div class="border border-gray-200 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $proyecto->nombre }}</h3>

                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                        {{ $proyecto->descripcion ?? 'Sin descripción disponible' }}
                                    </p>

                                    <div class="mb-4 pb-4 border-b border-gray-200">
                                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Docente</p>
                                        <p class="text-gray-900 text-sm font-medium">
                                            {{ $proyecto->docente->name ?? 'No asignado' }}
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        onclick="openInscriptionModal({{ $proyecto->id }}, '{{ addslashes($proyecto->nombre) }}')"
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                        Inscribirse
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Mis Últimos Certificados -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Mis Últimos Certificados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actualizado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ultimosCertificados as $certificado)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $certificado->inscripcion->proyecto->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $certificado->tipoCertificado->codigo }} - {{ $certificado->tipoCertificado->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $certificado->updated_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($certificado->estado === 'aprobado')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Aprobado</span>
                                        @elseif($certificado->estado === 'pendiente')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pendiente</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rechazado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        Todavía no subiste ningún certificado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Inscription Modal -->
    <div id="inscriptionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Confirmar Inscripción</h3>
                <button
                    type="button"
                    onclick="closeInscriptionModal()"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="inscriptionForm" action="{{ route('inscripciones.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4">
                    <p class="text-gray-700 mb-4">
                        ¿Deseas inscribirte al proyecto <strong id="projectNameDisplay"></strong>?
                    </p>
                    <input type="hidden" id="projectIdInput" name="proyecto_vinculacion_id" value="">
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeInscriptionModal()"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors duration-200">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200">
                        Confirmar Inscripción
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openInscriptionModal(projectId, projectName) {
            document.getElementById('projectIdInput').value = projectId;
            document.getElementById('projectNameDisplay').textContent = projectName;
            document.getElementById('inscriptionModal').classList.remove('hidden');
        }

        function closeInscriptionModal() {
            document.getElementById('inscriptionModal').classList.add('hidden');
            document.getElementById('inscriptionForm').reset();
        }

        // Close modal when clicking outside of it
        document.getElementById('inscriptionModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeInscriptionModal();
            }
        });
    </script>
</x-app-layout>