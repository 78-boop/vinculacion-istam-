<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar documento requerido</h1>
                <form action="{{ route('admin.tipos-certificado.update', $tipo) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('admin.tipos-certificado.form', ['tipo' => $tipo])
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.tipos-certificado.index') }}" class="px-4 py-2 text-gray-600">Cancelar</a>
                        <button class="px-4 py-2 rounded-lg bg-green-800 hover:bg-green-900 text-white font-semibold">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
