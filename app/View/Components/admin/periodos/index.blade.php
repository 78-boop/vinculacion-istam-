<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Periodos Académicos
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.periodos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Nuevo Periodo
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Inicio</th>
                            <th class="p-3">Fin</th>
                            <th class="p-3">Activo</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periodos as $periodo)
                            <tr class="border-t">
                                <td class="p-3">{{ $periodo->nombre }}</td>
                                <td class="p-3">{{ $periodo->fecha_inicio }}</td>
                                <td class="p-3">{{ $periodo->fecha_fin }}</td>
                                <td class="p-3">{{ $periodo->activo ? 'Sí' : 'No' }}</td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('admin.periodos.edit', $periodo) }}" class="text-blue-600">Editar</a>
                                    <form action="{{ route('admin.periodos.destroy', $periodo) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este periodo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-3 text-center text-gray-500">No hay periodos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>