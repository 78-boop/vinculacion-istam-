<x-app-layout>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.usuarios.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Nuevo Usuario
                </a>
            </div>

            <!-- Filtros por rol -->
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.usuarios.index') }}"
                   @class([
                       'px-4 py-2 rounded-lg text-sm font-medium',
                       'bg-gray-800 text-white' => !$rolFiltro,
                       'bg-gray-100 text-gray-700 hover:bg-gray-200' => $rolFiltro,
                   ])>
                    Todos ({{ $conteos['todos'] }})
                </a>
                <a href="{{ route('admin.usuarios.index', ['rol' => 'docente']) }}"
                   @class([
                       'px-4 py-2 rounded-lg text-sm font-medium',
                       'bg-green-600 text-white' => $rolFiltro === 'docente',
                       'bg-gray-100 text-gray-700 hover:bg-gray-200' => $rolFiltro !== 'docente',
                   ])>
                    Docentes ({{ $conteos['docente'] }})
                </a>
                <a href="{{ route('admin.usuarios.index', ['rol' => 'estudiante']) }}"
                   @class([
                       'px-4 py-2 rounded-lg text-sm font-medium',
                       'bg-yellow-500 text-white' => $rolFiltro === 'estudiante',
                       'bg-gray-100 text-gray-700 hover:bg-gray-200' => $rolFiltro !== 'estudiante',
                   ])>
                    Estudiantes ({{ $conteos['estudiante'] }})
                </a>
                <a href="{{ route('admin.usuarios.index', ['rol' => 'admin']) }}"
                   @class([
                       'px-4 py-2 rounded-lg text-sm font-medium',
                       'bg-purple-600 text-white' => $rolFiltro === 'admin',
                       'bg-gray-100 text-gray-700 hover:bg-gray-200' => $rolFiltro !== 'admin',
                   ])>
                    Admins ({{ $conteos['admin'] }})
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Correo</th>
                            <th class="p-3">Rol</th>
                            <th class="p-3">Carrera</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="border-t">
                                <td class="p-3">{{ $usuario->name }}</td>
                                <td class="p-3">{{ $usuario->email }}</td>
                                <td class="p-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-semibold',
                                        'bg-purple-100 text-purple-700' => $usuario->role === 'admin',
                                        'bg-blue-100 text-blue-700' => $usuario->role === 'coordinador',
                                        'bg-green-100 text-green-700' => $usuario->role === 'docente',
                                        'bg-yellow-100 text-yellow-700' => $usuario->role === 'estudiante',
                                    ])>
                                        {{ ucfirst($usuario->role) }}
                                    </span>
                                </td>
                                <td class="p-3 text-sm text-gray-600">
                                    {{ $usuario->carrera->nombre ?? '—' }}
                                </td>
                                <td class="p-3 space-x-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-blue-600">Editar</a>
                                    @if ($usuario->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="inline" data-confirm-title="¿Eliminar este usuario?" data-confirm-text="Esta acción no se puede deshacer.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-3 text-center text-gray-500">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>