<x-app-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.inscripciones.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Nueva Inscripción
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Estudiante</th>
                            <th class="p-3">Proyecto</th>
                            <th class="p-3">Fecha inscripción</th>
                            <th class="p-3">Horas cumplidas</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inscripciones as $inscripcion)
                            <tr class="border-t">
                                <td class="p-3">{{ $inscripcion->estudiante->name ?? 'Sin asignar' }}</td>
                                <td class="p-3">{{ $inscripcion->proyecto->nombre ?? '—' }}</td>
                                <td class="p-3">{{ $inscripcion->fecha_inscripcion }}</td>
                                <td class="p-3">{{ $inscripcion->horas_cumplidas }}</td>
                                <td class="p-3 capitalize">{{ $inscripcion->estado }}</td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('admin.inscripciones.destroy', $inscripcion) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta inscripción?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-3 text-center text-gray-500">No hay inscripciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>