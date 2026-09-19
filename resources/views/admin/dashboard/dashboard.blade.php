<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Panel de Administración</h1>
                <p class="text-gray-600 mt-2">Bienvenido {{ Auth::user()->name }}, aquí puedes gestionar todo el sistema</p>
            </div>

            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Períodos -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Períodos Académicos</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalPeriodos }}</p>
                        </div>
                        <div class="text-blue-500 text-4xl">📅</div>
                    </div>
                    <a href="{{ route('admin.periodos.index') }}" class="text-blue-500 text-sm mt-4 hover:underline inline-block">Ver más →</a>
                </div>

                <!-- Total Proyectos -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Proyectos de Vinculación</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalProyectos }}</p>
                        </div>
                        <div class="text-green-500 text-4xl">🎯</div>
                    </div>
                    <a href="{{ route('admin.proyectos.index') }}" class="text-green-500 text-sm mt-4 hover:underline inline-block">Ver más →</a>
                </div>

                <!-- Total Inscripciones -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500 hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Inscripciones</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalInscripciones }}</p>
                        </div>
                        <div class="text-purple-500 text-4xl">👥</div>
                    </div>
                    <a href="{{ route('admin.inscripciones.index') }}" class="text-purple-500 text-sm mt-4 hover:underline inline-block">Ver más →</a>
                </div>

                <!-- Total Actividades -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 hover:-translate-y-1 hover:shadow-lg transition-all duration-200" style="border-color: #8BC34A;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Actividades</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalActividades }}</p>
                        </div>
                        <div class="text-4xl">⚡</div>
                    </div>
                    <a href="{{ route('admin.actividades.index') }}" class="text-sm mt-4 hover:underline inline-block" style="color: #8BC34A;">Ver más →</a>
                </div>
            </div>

            <!-- Alertas Importantes -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Actividades Pendientes -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex">
                        <div class="flex-shrink-0 text-yellow-400 text-2xl">⚠️</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Actividades Pendientes</p>
                            <p class="text-2xl font-bold text-yellow-900 mt-2">{{ $actividadesPendientes }}</p>
                            <p class="text-sm text-yellow-700 mt-2">Requieren aprobación de docentes</p>
                            <a href="{{ route('admin.actividades.index') }}" class="text-yellow-700 text-sm font-medium hover:underline inline-block mt-2">Revisar →</a>
                        </div>
                    </div>
                </div>

                <!-- Horas Totales Aprobadas -->
                <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex">
                        <div class="flex-shrink-0 text-green-400 text-2xl">✅</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Horas Aprobadas</p>
                            <p class="text-2xl font-bold text-green-900 mt-2">{{ number_format($horasTotales ?? 0, 1) }}</p>
                            <p class="text-sm text-green-700 mt-2">Total acumulado del sistema</p>
                        </div>
                    </div>
                </div>

                <!-- Acceso Rápido -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                    <div class="flex">
                        <div class="flex-shrink-0 text-blue-400 text-2xl">🔧</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Acciones Rápidas</p>
                            <div class="mt-3 space-y-2">
                                <a href="{{ route('admin.periodos.create') }}" class="block text-sm text-blue-700 hover:text-blue-900">+ Nuevo Período</a>
                                <a href="{{ route('admin.proyectos.create') }}" class="block text-sm text-blue-700 hover:text-blue-900">+ Nuevo Proyecto</a>
                                <a href="{{ route('admin.actividades.create') }}" class="block text-sm text-blue-700 hover:text-blue-900">+ Nueva Actividad</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Últimas Actividades -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Últimas Actividades Registradas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ultimasActividades as $actividad)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $actividad->proyecto?->nombre ?? $actividad->inscripcion?->proyecto?->nombre ?? 'Sin proyecto' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ ($actividad->fecha_inicio ?? $actividad->fecha)?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $actividad->horas }} h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($actividad->estado === 'aprobada')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Aprobada</span>
                                        @elseif($actividad->estado === 'pendiente')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pendiente</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rechazada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                        No hay actividades registradas aún
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección: Estudiantes Más Activos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Estudiantes Más Activos</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($estudiantesActivos as $inscripcion)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $inscripcion->estudiante->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">{{ $inscripcion->actividades_count }}</p>
                                    <p class="text-xs text-gray-500">actividades</p>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                Sin datos disponibles
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Proyectos Más Activos -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Proyectos Más Activos</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($proyectosActivos as $proyecto)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $proyecto->nombre }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold" style="color: #8BC34A;">{{ $proyecto->actividades_aprobadas }}</p>
                                    <p class="text-xs text-gray-500">actividades</p>
                                </div>
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