<x-app-layout>

    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.inscripciones.index')" volver-texto="Inscripciones" titulo="Nueva inscripción" subtitulo="Inscribe a un estudiante en un proyecto de vinculación." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.inscripciones.store') }}" method="POST" data-confirm-title="¿Guardar este registro?" data-confirm-text="Revisa que los datos sean correctos." data-confirm-button="Sí, guardar">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Estudiante</label>
                        <select name="estudiante_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Seleccione un estudiante</option>
                            @foreach ($estudiantes as $estudiante)
                                <option value="{{ $estudiante->id }}" {{ old('estudiante_id') == $estudiante->id ? 'selected' : '' }}>
                                    {{ $estudiante->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('estudiante_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Proyecto de vinculación</label>
                        <select name="proyecto_vinculacion_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Seleccione un proyecto</option>
                            @foreach ($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}" {{ old('proyecto_vinculacion_id') == $proyecto->id ? 'selected' : '' }}>
                                    {{ $proyecto->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('proyecto_vinculacion_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha de inscripción</label>
                        <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha_inscripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Horas cumplidas</label>
                        <input type="number" name="horas_cumplidas" min="0" value="{{ old('horas_cumplidas', 0) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('horas_cumplidas') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Estado</label>
                        <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="retirado" {{ old('estado') == 'retirado' ? 'selected' : '' }}>Retirado</option>
                        </select>
                        @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <a href="{{ route('admin.inscripciones.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-primario">
                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>