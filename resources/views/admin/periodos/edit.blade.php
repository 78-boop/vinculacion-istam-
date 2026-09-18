<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Periodo Académico
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('admin.periodos.update', $periodo) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nombre (ej: 2026-A)</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $periodo->nombre) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('nombre') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $periodo->fecha_inicio) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha_inicio') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha de fin</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $periodo->fecha_fin) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha_fin') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', $periodo->activo) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Periodo activo</span>
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <a href="{{ route('admin.periodos.index') }}" class="px-4 py-2 text-gray-600">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
