<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Actividad</h2>
    </x-slot>

    <div class="py-8">
        <style>
            .actividad-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(240px, 1fr); gap: 24px; align-items: start; }
            @media (max-width: 1023px) { .actividad-layout { grid-template-columns: 1fr; } }
        </style>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="actividad-layout">
                <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8">
                <form action="{{ route('admin.actividades.store') }}" method="POST">
                    @csrf
                    @include('admin.actividades._form')
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('admin.actividades.index') }}" class="px-4 py-2 text-gray-600">Cancelar</a>
                        <button type="submit" style="background-color: #006B47; color: #FFFFFF; padding: 10px 20px; border-radius: 8px; font-weight: 700;">Guardar</button>
                    </div>
                </form>
                </div>

                <aside class="space-y-4">
                    <div class="bg-white shadow-lg rounded-xl p-5">
                        <label for="buscar_docente" class="block font-medium text-sm text-gray-700">Buscar docente</label>
                        <input id="buscar_docente" type="search" placeholder="Escribe nombre o cédula..." autocomplete="off" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-700 focus:ring-green-700">
                        <div id="resultados-docente" role="listbox" class="mt-2 hidden max-h-56 overflow-y-auto border border-green-100 rounded-lg bg-white shadow-lg divide-y divide-gray-100"></div>
                    </div>
                    <div class="bg-white shadow-lg rounded-xl p-5">
                        <label for="buscar_estudiante" class="block font-medium text-sm text-gray-700">Buscar estudiante</label>
                        <input id="buscar_estudiante" type="search" placeholder="Escribe nombre o cédula..." autocomplete="off" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-700 focus:ring-green-700">
                        <div id="resultados-estudiante" role="listbox" class="mt-2 hidden max-h-56 overflow-y-auto border border-green-100 rounded-lg bg-white shadow-lg divide-y divide-gray-100"></div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
