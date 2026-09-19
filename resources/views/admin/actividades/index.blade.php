<x-app-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.actividades.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Nueva Actividad
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Proyecto</th>
                            <th class="p-3">Docente</th>
                            <th class="p-3">Estudiantes</th>
                            <th class="p-3">Inicio</th>
                            <th class="p-3">Finalización</th>
                            <th class="p-3">Lugar</th>
                            <th class="p-3">Horas</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($actividades as $actividad)
                            <tr class="border-t">
                                <td class="p-3">{{ $actividad->proyecto->nombre ?? $actividad->inscripcion->proyecto->nombre ?? '—' }}</td>
                                <td class="p-3">{{ $actividad->docente->name ?? '—' }}</td>
                                <td class="p-3 text-sm">
                                    {{ $actividad->inscripciones->pluck('estudiante.name')->filter()->join(', ') ?: ($actividad->inscripcion->estudiante->name ?? '—') }}
                                </td>
                                <td class="p-3">{{ $actividad->fecha_inicio?->format('d/m/Y') ?? $actividad->fecha?->format('d/m/Y') ?? '—' }}</td>
                                <td class="p-3">{{ $actividad->fecha_finalizacion?->format('d/m/Y') ?? '—' }}</td>
                                <td class="p-3">{{ $actividad->lugar ?? '—' }}</td>
                                <td class="p-3">{{ $actividad->horas }}</td>
                                <td class="p-3 capitalize">{{ $actividad->estado }}</td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('admin.actividades.edit', $actividad) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('admin.actividades.destroy', $actividad) }}" method="POST" class="inline" data-confirm-title="¿Eliminar esta actividad?" data-confirm-text="Esta acción no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-3 text-center text-gray-500">No hay actividades registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>