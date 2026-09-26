<x-app-layout>
    <div class="ui-wrap" style="max-width: 1080px;">
        <div class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">Carreras</span>
                <h1>Nueva carrera</h1>
                <p>Completa los datos. En cuanto la guardes estará disponible en todo el sistema.</p>
            </div>
            <a href="{{ route('admin.carreras.index') }}" class="ui-btn ui-btn-vidrio">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
                Volver
            </a>
        </div>

        <form action="{{ route('admin.carreras.store') }}" method="POST" enctype="multipart/form-data"
              data-confirm-title="¿Guardar esta carrera?" data-confirm-text="Se agregará a todas las secciones del sistema." data-confirm-button="Sí, guardar">
            @csrf
            @include('admin.carreras._form', ['textoBoton' => 'Guardar carrera'])
        </form>
    </div>
</x-app-layout>
