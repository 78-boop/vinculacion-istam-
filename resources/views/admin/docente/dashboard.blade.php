<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Panel del Docente</h1>
                <p class="text-gray-600 mt-2">Bienvenido {{ Auth::user()->name }}, aquí puedes gestionar tus estudiantes y proyectos</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('docente.proyectos.create') }}"
                       class="inline-block px-4 py-2 rounded-lg text-white font-medium hover:opacity-90"
                       style="background-color: #006B47;">
                        + Proponer Proyecto
                    </a>
                    <a href="{{ route('docente.certificados.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white font-medium bg-blue-600 hover:bg-blue-700">
                        📄 Ver Certificados Pendientes
                        @if($certificadosPendientes->total() > 0)
                            <span class="bg-white text-blue-600 rounded-full px-2 py-0.5 text-xs font-bold">{{ $certificadosPendientes->total() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('docente.postulaciones-actividad.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white font-medium bg-indigo-600 hover:bg-indigo-700">
                        🎯 Ver Actividades Pendientes
                        @if($postulacionesActividadPendientes > 0)
                            <span class="bg-white text-indigo-600 rounded-full px-2 py-0.5 text-xs font-bold">{{ $postulacionesActividadPendientes }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Certificados Pendientes -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Certificados Pendientes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $certificadosPendientes->total() }}</p>
                        </div>
                        <div class="text-yellow-500 text-4xl">⏳</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Requieren tu aprobación</p>
                </div>

                <!-- Certificados Aprobados -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Certificados Aprobados</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $certificadosAprobados ?? 0 }}</p>
                        </div>
                        <div class="text-green-500 text-4xl">✅</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">En total, entre todos tus estudiantes</p>
                </div>

                <!-- Actividades Pendientes -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Actividades Pendientes</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $postulacionesActividadPendientes }}</p>
                        </div>
                        <div class="text-indigo-500 text-4xl">🎯</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Estudiantes esperando tu aprobación</p>
                </div>

                <!-- Total Estudiantes -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 hover:-translate-y-1 hover:shadow-lg transition-all duration-200" style="border-color: #8BC34A;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Estudiantes Asignados</p>
                            <p class="text-3xl font-bold text-gray-900">{{ count($estudiantesAsignados) }}</p>
                        </div>
                        <div class="text-4xl">👥</div>
                    </div>
                </div>
            </div>

            <!-- Alertas Importantes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Certificados por Revisar -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex">
                        <div class="flex-shrink-0 text-yellow-400 text-2xl">⚠️</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Por Revisar</p>
                            <p class="text-2xl font-bold text-yellow-900 mt-2">{{ $certificadosPendientes->total() }}</p>
                            <p class="text-sm text-yellow-700 mt-2">Certificados en espera de aprobación</p>
                            @if($certificadosPendientes->total() > 0)
                                <a href="{{ route('docente.certificados.index') }}" class="text-yellow-700 text-sm font-medium hover:underline inline-block mt-2">Revisar ahora →</a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Proyectos Activos -->
                <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex">
                        <div class="flex-shrink-0 text-green-400 text-2xl">🎯</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Proyectos Activos</p>
                            <p class="text-2xl font-bold text-green-900 mt-2">{{ count($proyectos) }}</p>
                            <p class="text-sm text-green-700 mt-2">Proyectos en los que eres docente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Certificados Pendientes de Aprobación -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Certificados Pendientes de Aprobación</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subido el</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($certificadosPendientes as $certificado)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $certificado->inscripcion->estudiante->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $certificado->inscripcion->proyecto->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $certificado->tipoCertificado->codigo }} - {{ $certificado->tipoCertificado->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $certificado->updated_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('docente.certificados.show', $certificado->inscripcion_id) }}" class="text-blue-600 hover:text-blue-900 font-medium">Revisar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        No hay certificados pendientes de aprobación
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($certificadosPendientes->hasPages())
                    @php
                        $paginaActual = $certificadosPendientes->currentPage();
                        $ultimaPagina = $certificadosPendientes->lastPage();
                        $paginas = collect([1, $ultimaPagina, $paginaActual - 1, $paginaActual, $paginaActual + 1])
                            ->filter(fn ($pagina) => $pagina >= 1 && $pagina <= $ultimaPagina)
                            ->unique()
                            ->sort()
                            ->values();
                    @endphp

                    <div class="px-6 py-5 border-t border-gray-200 bg-gray-50 flex justify-center">
                        <nav class="flex items-center gap-2" aria-label="Paginación de certificados">
                            @if($certificadosPendientes->onFirstPage())
                                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                                    Anterior
                                </span>
                            @else
                                <a href="{{ $certificadosPendientes->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-green-900 bg-white border border-gray-200 rounded-lg hover:bg-green-50">
                                    Anterior
                                </a>
                            @endif

                            @php $paginaAnterior = null; @endphp
                            @foreach($paginas as $pagina)
                                @if($paginaAnterior !== null && $pagina > $paginaAnterior + 1)
                                    <span class="px-2 py-2 text-sm font-medium text-green-900">...</span>
                                @endif

                                @if($pagina === $paginaActual)
                                    <span class="px-3 py-2 text-sm font-bold text-green-900 bg-white border-2 border-green-900 rounded-lg" aria-current="page">
                                        {{ $pagina }}
                                    </span>
                                @else
                                    <a href="{{ $certificadosPendientes->url($pagina) }}" class="px-3 py-2 text-sm font-medium text-green-900 bg-white border border-gray-200 rounded-lg hover:bg-green-50">
                                        {{ $pagina }}
                                    </a>
                                @endif

                                @php $paginaAnterior = $pagina; @endphp
                            @endforeach

                            @if($certificadosPendientes->hasMorePages())
                                <a href="{{ $certificadosPendientes->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-green-900 bg-white border border-gray-200 rounded-lg hover:bg-green-50">
                                    Siguiente
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                                    Siguiente
                                </span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>

            <!-- Sección: Mis Proyectos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Mis Proyectos</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($proyectos as $proyecto)
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $proyecto->nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $proyecto->inscripciones_count ?? 0 }} estudiantes inscritos</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold" style="color: #8BC34A;">{{ $proyecto->inscripciones_count ?? 0 }}</p>
                                        <p class="text-xs text-gray-500">inscritos</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                No tienes proyectos asignados aún
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Estudiantes Asignados -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Estudiantes Asignados</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($estudiantesAsignados as $inscripcion)
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <p class="text-sm font-medium text-gray-900">{{ $inscripcion->estudiante->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $inscripcion->estudiante->email }}</p>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                Sin datos disponibles
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>