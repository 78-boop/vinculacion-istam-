<x-app-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.proyectos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Nuevo Proyecto
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Docente</th>
                            <th class="p-3">Periodo</th>
                            <th class="p-3">Horas req.</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proyectos as $proyecto)
                            <tr class="border-t">
                                <td class="p-3">{{ $proyecto->nombre }}</td>
                                <td class="p-3">{{ $proyecto->docente->name ?? 'Sin asignar' }}</td>
                                <td class="p-3">{{ $proyecto->periodoAcademico->nombre ?? '—' }}</td>
                                <td class="p-3">{{ $proyecto->horas_requeridas }}</td>
                                <td class="p-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-semibold',
                                        'bg-yellow-100 text-yellow-700' => $proyecto->estado === 'pendiente',
                                        'bg-green-100 text-green-700' => $proyecto->estado === 'aprobado',
                                        'bg-red-100 text-red-700' => $proyecto->estado === 'rechazado',
                                    ])>
                                        {{ ucfirst($proyecto->estado) }}
                                    </span>
                                </td>
                                <td class="p-3 space-x-2 whitespace-nowrap">
                                    @if ($proyecto->estado === 'pendiente')
                                        <form action="{{ route('admin.proyectos.aprobar', $proyecto) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600">Aprobar</button>
                                        </form>
                                        <form action="{{ route('admin.proyectos.rechazar', $proyecto) }}" method="POST" class="inline" onsubmit="return confirm('¿Rechazar esta propuesta?')">
                                            @csrf
                                            <button type="submit" class="text-red-600">Rechazar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.proyectos.edit', $proyecto) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('admin.proyectos.destroy', $proyecto) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este proyecto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-3 text-center text-gray-500">No hay proyectos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>