<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Documentos requeridos</h1>
                    <p class="mt-1 text-sm text-gray-600">Configura los códigos y nombres que verán estudiantes y docentes.</p>
                </div>
                <a href="{{ route('admin.tipos-certificado.create') }}" style="display: inline-block; background-color: #006B47; color: #FFFFFF; padding: 10px 16px; border-radius: 8px; font-weight: 700; text-decoration: none;">
                    + Agregar certificado
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-4">Orden</th>
                                <th class="p-4">Código</th>
                                <th class="p-4">Nombre del documento</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tipos as $tipo)
                                <tr class="border-t">
                                    <td class="p-4 text-gray-600">{{ $tipo->orden }}</td>
                                    <td class="p-4 font-mono font-semibold text-green-900">{{ $tipo->codigo }}</td>
                                    <td class="p-4 text-gray-800">{{ $tipo->nombre }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $tipo->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="p-4 whitespace-nowrap space-x-3">
                                        <a href="{{ route('admin.tipos-certificado.edit', $tipo) }}" class="text-blue-700 hover:underline">Editar</a>
                                        <form action="{{ route('admin.tipos-certificado.destroy', $tipo) }}" method="POST" class="inline" data-confirm-title="¿Eliminar este documento requerido?" data-confirm-text="Esta acción no se puede deshacer.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-700 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-6 text-center text-gray-500">No hay documentos configurados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
