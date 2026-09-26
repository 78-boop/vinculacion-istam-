<x-app-layout>
    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.tipos-certificado.index')" volver-texto="Documentos requeridos" titulo="Editar documento requerido" subtitulo="Cambia el código, el nombre o la posición del documento." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.tipos-certificado.update', $tipo) }}" method="POST" data-confirm-title="¿Guardar los cambios?" data-confirm-text="Se actualizará la información." data-confirm-button="Sí, guardar" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('admin.tipos-certificado.form', ['tipo' => $tipo])
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.tipos-certificado.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-primario">
                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
