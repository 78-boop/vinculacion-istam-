<x-app-layout>

    <div>
        <style>
            .actividad-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(240px, 1fr); gap: 24px; align-items: start; }
            @media (max-width: 1023px) { .actividad-layout { grid-template-columns: 1fr; } }
        </style>
        <div class="ui-wrap" style="max-width: 1100px;">
            <x-ui.hero :volver="route('admin.actividades.index')" volver-texto="Actividades" titulo="Editar actividad" subtitulo="Actualiza fechas, lugar, horas o estudiantes de la actividad." />

            <div class="actividad-layout">
                <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.actividades.update', $actividad) }}" method="POST" data-confirm-title="¿Guardar los cambios?" data-confirm-text="Se actualizará la información." data-confirm-button="Sí, guardar">
                    @csrf
                    @method('PUT')
                    @include('admin.actividades._form', ['actividad' => $actividad])
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('admin.actividades.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-primario">
                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Guardar cambios
                        </button>
                    </div>
                </form>
                </div>

                <aside class="space-y-4">
                    <div class="bg-white shadow-lg rounded-xl p-5">
                        <label for="buscar_docente" class="block font-medium text-sm text-gray-700">Buscar docente</label>
                        <input id="buscar_docente" type="search" placeholder="Haz clic para ver todos o escribe..." autocomplete="off" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-700 focus:ring-green-700">
                        <div id="resultados-docente" role="listbox" class="mt-2 hidden max-h-80 overflow-y-auto border border-green-100 rounded-lg bg-white shadow-lg divide-y divide-gray-100"></div>
                    </div>
                    <div class="bg-white shadow-lg rounded-xl p-5">
                        <label for="buscar_estudiante" class="block font-medium text-sm text-gray-700">Buscar estudiante</label>
                        <input id="buscar_estudiante" type="search" placeholder="Haz clic para ver todos o escribe..." autocomplete="off" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-700 focus:ring-green-700">
                        <div id="resultados-estudiante" role="listbox" class="mt-2 hidden max-h-80 overflow-y-auto border border-green-100 rounded-lg bg-white shadow-lg divide-y divide-gray-100"></div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
