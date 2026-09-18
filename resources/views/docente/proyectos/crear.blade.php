<x-app-layout>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4 p-4 bg-blue-50 text-blue-700 rounded text-sm">
                Tu propuesta quedará <strong>pendiente</strong> hasta que un administrador la revise y la apruebe.
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('docente.proyectos.store') }}" method="POST">
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

                    <div class="flex gap-4">
                        <button type="submit" class="px-6 py-2 rounded-lg text-white font-medium hover:opacity-90"
                                style="background-color: #006B47;">
                            Enviar Propuesta
                        </button>
                        <a href="{{ route('dashboard') }}"
                           class="px-6 py-2 rounded-lg text-gray-700 font-medium border border-gray-300 hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
