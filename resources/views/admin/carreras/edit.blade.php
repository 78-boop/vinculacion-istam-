<x-app-layout>
    <div class="ui-wrap" style="max-width: 1080px;">
        <div class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">Carreras</span>
                <h1>Editar carrera</h1>
                <p>{{ $carrera->nombre }}</p>
            </div>
            <a href="{{ route('admin.carreras.index') }}" class="ui-btn ui-btn-vidrio">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
                Volver
            </a>
        </div>

        <form action="{{ route('admin.carreras.update', $carrera) }}" method="POST" enctype="multipart/form-data"
              data-confirm-title="¿Guardar los cambios?" data-confirm-text="Los cambios se verán reflejados en todas las secciones." data-confirm-button="Sí, guardar">
            @csrf
            @method('PUT')
            @include('admin.carreras._form', ['textoBoton' => 'Guardar cambios'])
        </form>
    </div>
</x-app-layout>
