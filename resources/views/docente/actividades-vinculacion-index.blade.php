<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Actividades de Vinculación</h1>
        <p class="text-gray-600 mb-4">Elegí una carrera para ver o agregar actividades disponibles para esos estudiantes.</p>

        <div class="flex flex-wrap items-center gap-3 mb-6">
            <a href="{{ route('docente.postulaciones-actividad.index') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                Ver postulaciones de mis estudiantes →
            </a>
            <a href="{{ route('docente.actividades-vinculacion.exportar') }}"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                📊 Descargar catálogo (Excel)
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($carreras as $carrera)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
                    <a href="{{ route('docente.actividades-vinculacion.show', $carrera->id) }}" class="block">
                        <div class="h-64 bg-gray-100 overflow-hidden">
                            @if($carrera->imagen)
                                <img src="{{ asset($carrera->imagen) }}" alt="{{ $carrera->nombre }}" class="w-full h-full object-cover object-top">
                            @endif
                        </div>
                        <div class="p-4 pb-2">
                            <h3 class="font-bold text-gray-800">{{ $carrera->nombre }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $carrera->actividades_vinculacion_count }} actividad(es) en el catálogo</p>
                        </div>
                    </a>
                    <div class="px-4 pb-4">
                        <a href="{{ route('docente.actividades-vinculacion.exportar-carrera', $carrera->id) }}"
                           class="inline-flex items-center gap-1 text-green-700 text-xs font-semibold hover:underline">
                            ⬇ Descargar Excel de esta carrera
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</x-app-layout>