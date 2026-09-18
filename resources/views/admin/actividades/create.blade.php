<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Actividad
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('admin.actividades.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Inscripción (Estudiante — Proyecto)</label>
                        <select name="inscripcion_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Seleccione una inscripción</option>
                            @foreach ($inscripciones as $inscripcion)
                                <option value="{{ $inscripcion->id }}" {{ old('inscripcion_id') == $inscripcion->id ? 'selected' : '' }}>
                                    {{ $inscripcion->estudiante->name ?? 'Sin asignar' }} — {{ $inscripcion->proyecto->nombre ?? 'Sin proyecto' }}
                                </option>
                            @endforeach
                        </select>
                        @error('inscripcion_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Lugar</label>
                        <input type="text" name="lugar" value="{{ old('lugar') }}" placeholder="Ej. Comunidad El Rosario" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('lugar') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Descripción de la actividad</label>
                        <textarea name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Horas</label>
                        <input type="number" step="0.5" min="0.5" max="24" name="horas" value="{{ old('horas') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('horas') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Estado</label>
                        <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobada" {{ old('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                            <option value="rechazada" {{ old('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                        </select>
                        @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Comentario del docente (opcional)</label>
                        <textarea name="comentario_docente" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('comentario_docente') }}</textarea>
                        @error('comentario_docente') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('admin.actividades.index') }}" class="px-4 py-2 text-gray-600">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>