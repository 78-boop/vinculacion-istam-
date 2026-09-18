<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Certificados de Vinculación</h1>
        <p class="text-gray-600 mb-6">Estudiantes que completaron los 8 documentos y ya pueden ser certificados.</p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">{{ session('error') }}</div>
        @endif

        @if($inscripciones->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">Todavía no hay estudiantes con los 8 documentos aprobados.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="py-3 px-4">Estudiante</th>
                            <th class="py-3 px-4">Cédula</th>
                            <th class="py-3 px-4">Proyecto</th>
                            <th class="py-3 px-4">Certificado</th>
                            <th class="py-3 px-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($inscripciones as $inscripcion)
                            <tr>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $inscripcion->estudiante->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    {{ $inscripcion->estudiante->cedula ?? '—' }}
                                    @if(!$inscripcion->estudiante->cedula)
                                        <a href="{{ route('admin.usuarios.edit', $inscripcion->estudiante->id) }}" class="text-red-600 text-xs hover:underline block">Falta cédula, completar</a>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $inscripcion->proyecto->nombre }}</td>
                                <td class="py-3 px-4">
                                    @if($inscripcion->certificadoAdministrativo)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Generado</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Sin generar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('admin.certificados.generar', $inscripcion->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition">
                                                {{ $inscripcion->certificadoAdministrativo ? 'Regenerar' : 'Generar certificado' }}
                                            </button>
                                        </form>

                                        @if($inscripcion->certificadoAdministrativo)
                                            <a href="{{ route('admin.certificados.descargar', $inscripcion->certificadoAdministrativo->id) }}"
                                               class="text-indigo-600 text-sm hover:underline">Descargar PDF</a>
                                        @endif
                                    </div>
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