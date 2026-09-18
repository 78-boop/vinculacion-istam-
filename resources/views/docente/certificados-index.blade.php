<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Certificados de mis estudiantes</h1>

        @if($inscripciones->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">No tenés estudiantes inscritos en tus proyectos todavía.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="py-3 px-4">Estudiante</th>
                            <th class="py-3 px-4">Proyecto</th>
                            <th class="py-3 px-4">Carrera</th>
                            <th class="py-3 px-4">Horas cumplidas</th>
                            <th class="py-3 px-4">Documentos aprobados</th>
                            <th class="py-3 px-4">Pendientes de revisar</th>
                            <th class="py-3 px-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($inscripciones as $inscripcion)
                            @php
                                $aprobados = $inscripcion->certificadosEstudiante->where('estado', 'aprobado')->count();
                                $pendientes = $inscripcion->certificadosEstudiante->where('estado', 'pendiente')->count();
                                $carrera = $inscripcion->estudiante->carrera;
                            @endphp
                            <tr>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $inscripcion->estudiante->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $inscripcion->proyecto->nombre }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $carrera->nombre ?? '—' }}</td>
                                <td class="py-3 px-4" x-data="{ editando: false }">
                                    <div x-show="!editando">
                                        <span class="text-sm text-gray-800 font-medium">{{ $inscripcion->horas_cumplidas }}</span>
                                        <span class="text-sm text-gray-500">/ {{ $carrera->horas_requeridas ?? '—' }} hrs</span>
                                        <button type="button" @click="editando = true" class="block text-indigo-600 text-xs font-medium hover:underline mt-1">
                                            Editar
                                        </button>
                                    </div>
                                    <form x-show="editando" x-cloak action="{{ route('docente.certificados.actualizar-horas', $inscripcion->id) }}" method="POST">
                                        @csrf
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" name="horas_cumplidas" min="0" max="1000"
                                                   value="{{ $inscripcion->horas_cumplidas }}"
                                                   class="w-16 text-sm border border-gray-300 rounded px-2 py-1">
                                            <span class="text-sm text-gray-500">/ {{ $carrera->horas_requeridas ?? '—' }} hrs</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <button type="submit" class="text-indigo-600 text-xs font-medium hover:underline">Guardar</button>
                                            <button type="button" @click="editando = false" class="text-gray-500 text-xs hover:underline">Cancelar</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $aprobados }} / {{ $totalTipos }}</td>
                                <td class="py-3 px-4">
                                    @if($pendientes > 0)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            {{ $pendientes }} pendiente(s)
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('docente.certificados.show', $inscripcion->id) }}"
                                       class="text-indigo-600 text-sm font-semibold hover:underline">
                                        Revisar documentos →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        @endif
    </div>
</div>
</x-app-layout>