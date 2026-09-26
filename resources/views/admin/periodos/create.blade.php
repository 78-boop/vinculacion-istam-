<x-app-layout>

    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.periodos.index')" volver-texto="Períodos" titulo="Nuevo período académico" subtitulo="Define el nombre y las fechas del período." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.periodos.store') }}" method="POST" data-confirm-title="¿Guardar este registro?" data-confirm-text="Revisa que los datos sean correctos." data-confirm-button="Sí, guardar">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Nombre (ej: 2026-A)</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('nombre') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha_inicio') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Fecha de fin</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha_fin') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="activo" value="1" checked class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Periodo activo</span>
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <a href="{{ route('admin.periodos.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
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