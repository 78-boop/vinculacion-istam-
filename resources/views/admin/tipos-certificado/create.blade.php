<x-app-layout>
    <div>
        <div class="ui-wrap" style="max-width: 860px;">
            <x-ui.hero :volver="route('admin.tipos-certificado.index')" volver-texto="Documentos requeridos" titulo="Nuevo documento requerido" subtitulo="Agrega un documento que los estudiantes deberán subir." />

            <div class="bg-white shadow rounded-lg p-6 sm:p-8">
                <form action="{{ route('admin.tipos-certificado.store') }}" method="POST" data-confirm-title="¿Guardar este registro?" data-confirm-text="Revisa que los datos sean correctos." data-confirm-button="Sí, guardar" class="space-y-5">
                    @csrf
                    @include('admin.tipos-certificado.form')
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.tipos-certificado.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
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
