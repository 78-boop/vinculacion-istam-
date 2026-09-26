<x-app-layout>
    <div class="ui-wrap" style="max-width: 820px;">
        <x-ui.hero :volver="route('dashboard')" volver-texto="Mi panel" titulo="Proponer un proyecto"
                   subtitulo="Tu propuesta quedará pendiente hasta que un administrador la revise y la apruebe." />

        <form action="{{ route('docente.proyectos.store') }}" method="POST" class="ui-panel"
              data-confirm-title="¿Enviar la propuesta?" data-confirm-text="El administrador la revisará antes de publicarla." data-confirm-button="Sí, enviar">
            @csrf
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3Zm-2 17h4"/></svg></span> Datos del proyecto</h3>
            </div>
            <div class="ui-panel-body">
                <div class="ui-form-grid">
                    <div class="ui-campo completo">
                        <label for="nombre">Nombre del proyecto</label>
                        <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required class="ui-input @error('nombre') is-invalido @enderror" placeholder="Ej: Alfabetización digital comunitaria">
                        @error('nombre') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="ui-campo completo">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="5" required class="ui-input @error('descripcion') is-invalido @enderror" placeholder="¿Qué se hará, con quién y qué se espera lograr?">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="ui-campo completo">
                        <label for="periodo_academico_id">Período académico</label>
                        <select id="periodo_academico_id" name="periodo_academico_id" required class="ui-input @error('periodo_academico_id') is-invalido @enderror">
                            <option value="">Selecciona un período</option>
                            @foreach ($periodos as $periodo)
                                <option value="{{ $periodo->id }}" @selected(old('periodo_academico_id') == $periodo->id)>{{ $periodo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('periodo_academico_id') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--ui-borde);flex-wrap:wrap">
                    <a href="{{ route('dashboard') }}" class="ui-btn ui-btn-suave">Cancelar</a>
                    <button type="submit" class="ui-btn ui-btn-primario">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h13m-6-7 7 7-7 7"/></svg>
                        Enviar propuesta
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
