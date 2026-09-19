<x-app-layout>
    <x-ui.page-header title="Períodos Académicos" subtitle="Gestiona los períodos activos del sistema">
        <x-slot name="actions">
            <x-ui.btn :href="route('admin.periodos.create')">
                <span>+</span> Nuevo Período
            </x-ui.btn>
        </x-slot>
    </x-ui.page-header>

    <div class="space-y-3">
        @forelse ($periodos as $periodo)
            <div class="bg-white shadow-sm rounded-xl p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <!-- Info principal: nombre + fechas -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-gray-900">{{ $periodo->nombre }}</h3>
                            <x-ui.badge :status="$periodo->activo ? 'Sí' : 'No'" />
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $periodo->fecha_inicio }} — {{ $periodo->fecha_fin }}
                        </p>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center gap-4 shrink-0">
                        <a href="{{ route('admin.periodos.edit', $periodo) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</a>
                        <form action="{{ route('admin.periodos.destroy', $periodo) }}" method="POST" data-confirm-title="¿Eliminar este periodo?" data-confirm-text="Esta acción no se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white shadow-sm rounded-xl">
                <x-ui.empty-state message="No hay periodos registrados." icon="📅" />
            </div>
        @endforelse
    </div>
</x-app-layout>