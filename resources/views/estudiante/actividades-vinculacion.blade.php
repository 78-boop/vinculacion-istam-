<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Actividades de Vinculación</h1>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">{{ session('error') }}</div>
        @endif

        @if(!$tieneCarrera)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">Todavía no tenés una carrera asignada en tu perfil. Pedile al administrador que te la asigne para poder ver las actividades disponibles.</p>
            </div>
        @elseif($inscripciones->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">No tenés inscripciones activas en ningún proyecto de vinculación.</p>
            </div>
        @else
            @foreach($inscripciones as $inscripcion)
                @php
                    $postulacionActiva = $inscripcion->postulacionesActividad
                        ->whereIn('estado', ['pendiente', 'aprobada'])
                        ->first();
                    $ultimaRechazada = $inscripcion->postulacionesActividad
                        ->where('estado', 'rechazada')
                        ->sortByDesc('created_at')
                        ->first();
                @endphp

                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="font-semibold text-gray-800 mb-1">{{ $inscripcion->proyecto->nombre }}</h2>

                    @if($postulacionActiva)
                        <div class="mt-4 p-4 rounded-lg {{ $postulacionActiva->estado === 'aprobada' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                            <p class="text-sm font-semibold {{ $postulacionActiva->estado === 'aprobada' ? 'text-green-800' : 'text-yellow-800' }}">
                                {{ $postulacionActiva->estado === 'aprobada' ? '✅ Aprobada' : '⏳ Pendiente de aprobación' }}
                            </p>
                            <p class="text-gray-800 font-medium mt-1">{{ $postulacionActiva->actividad->titulo }}</p>
                            @if($postulacionActiva->actividad->descripcion)
                                <p class="text-sm text-gray-600 mt-1">{{ $postulacionActiva->actividad->descripcion }}</p>
                            @endif
                        </div>
                    @else
                        @if($ultimaRechazada)
                            <div class="mt-4 mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
                                <p class="text-sm font-semibold text-red-800">❌ Tu docente rechazó: {{ $ultimaRechazada->actividad->titulo }}</p>
                                @if($ultimaRechazada->observaciones_docente)
                                    <p class="text-sm text-red-700 mt-1">Motivo: {{ $ultimaRechazada->observaciones_docente }}</p>
                                @endif
                                <p class="text-sm text-gray-600 mt-2">Elegí otra actividad de la lista de abajo.</p>
                            </div>
                        @endif

                        @if($actividadesDisponibles->isEmpty())
                            <p class="text-gray-500 text-sm mt-4">Todavía no hay actividades cargadas para tu carrera. Consultá con tu docente.</p>
                        @else
                            <div class="mt-4 divide-y divide-gray-100">
                                @foreach($actividadesDisponibles as $actividad)
                                    <div class="py-4 flex items-center justify-between gap-4">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $actividad->titulo }}</p>
                                            @if($actividad->descripcion)
                                                <p class="text-sm text-gray-500 mt-1">{{ $actividad->descripcion }}</p>
                                            @endif
                                        </div>
                                        <form action="{{ route('actividades-vinculacion.postular') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="actividad_vinculacion_id" value="{{ $actividad->id }}">
                                            <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition whitespace-nowrap">
                                                Seleccionar
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
</x-app-layout>