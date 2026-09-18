<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">

        <a href="{{ route('docente.actividades-vinculacion.index') }}" class="text-sm text-indigo-600 hover:underline">← Volver a carreras</a>

        <div class="flex items-center gap-4 mt-4 mb-6">
            @if($carrera->imagen)
                <img src="{{ asset($carrera->imagen) }}" class="w-16 h-16 rounded-lg object-cover">
            @endif
            <h1 class="text-2xl font-bold text-gray-800">{{ $carrera->nombre }}</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">{{ session('success') }}</div>
        @endif

        <!-- Formulario para agregar actividad -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="font-semibold text-gray-800 mb-4">Agregar nueva actividad</h2>
            <form action="{{ route('docente.actividades-vinculacion.store') }}" method="POST">
                @csrf
                <input type="hidden" name="carrera_id" value="{{ $carrera->id }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título de la actividad</label>
                    <input type="text" name="titulo" required
                           class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500">
                    @error('titulo')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500"></textarea>
                    @error('descripcion')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    + Agregar al catálogo
                </button>
            </form>
        </div>

        <!-- Lista de actividades existentes -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-800">Actividades en el catálogo</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($actividades as $actividad)
                    <div class="px-6 py-4 flex items-center justify-between {{ !$actividad->activo ? 'opacity-50' : '' }}">
                        <div>
                            <p class="font-medium text-gray-800">{{ $actividad->titulo }}</p>
                            @if($actividad->descripcion)
                                <p class="text-sm text-gray-500 mt-1">{{ $actividad->descripcion }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Subida por {{ $actividad->creador->name }}
                                @if(!$actividad->activo) · <span class="text-red-500">Inactiva</span>@endif
                            </p>
                        </div>
                        @if($actividad->activo)
                            <form action="{{ route('docente.actividades-vinculacion.desactivar', $actividad->id) }}" method="POST"
                                  onsubmit="return confirm('¿Desactivar esta actividad? Ya no aparecerá para los estudiantes.');">
                                @csrf
                                <button type="submit" class="text-red-600 text-sm hover:underline">Desactivar</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        Todavía no hay actividades cargadas para esta carrera.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-app-layout>