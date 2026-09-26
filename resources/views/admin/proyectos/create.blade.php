<x-app-layout>

    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.proyectos.index')" volver-texto="Proyectos" titulo="Nuevo proyecto de vinculación" subtitulo="Registra un proyecto y asígnale un docente responsable." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.proyectos.store') }}" method="POST" data-confirm-title="¿Guardar este registro?" data-confirm-text="Revisa que los datos sean correctos." data-confirm-button="Sí, guardar">
                    @csrf

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Nombre del proyecto</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                        @error('nombre')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="4"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Docente Responsable</label>
                        <select name="docente_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                            <option value="">-- Seleccione un docente --</option>
                            @foreach ($docentes as $docente)
                                <option value="{{ $docente->id }}" {{ old('docente_id') == $docente->id ? 'selected' : '' }}>
                                    {{ $docente->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('docente_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Período Académico</label>
                        <select name="periodo_academico_id"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                            <option value="">-- Seleccione un período --</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}" {{ old('periodo_academico_id') == $periodo->id ? 'selected' : '' }}>
                                    {{ $periodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('periodo_academico_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="ui-btn ui-btn-primario">
                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Crear Proyecto
                        </button>
                        <a href="{{ route('admin.proyectos.index') }}"
                           class="ui-btn ui-btn-suave">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
