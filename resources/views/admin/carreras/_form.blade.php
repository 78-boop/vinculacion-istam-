{{-- Formulario compartido por crear y editar carrera --}}
<style>
    .carf-layout { margin-top: 24px; display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(260px, 1fr); gap: 22px; align-items: start; }
    .carf-drop {
        position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
        min-height: 190px; border-radius: 18px; border: 2px dashed #BFDCCB; background: #F7FCF9;
        cursor: pointer; overflow: hidden; text-align: center; padding: 16px; transition: border-color .2s, background .2s;
    }
    .carf-drop:hover, .carf-drop.is-over { border-color: var(--ui-verde); background: #EEF8F2; }
    .carf-drop input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .carf-drop svg { width: 34px; height: 34px; color: var(--ui-verde); }
    .carf-drop strong { font-size: 14px; color: var(--ui-texto); }
    .carf-drop span { font-size: 12.5px; color: var(--ui-texto-3); }
    .carf-drop { min-height: 230px; }
    .carf-drop.con-foto { padding: 0; min-height: 0; border-style: solid; }
    .carf-drop.con-foto > svg, .carf-drop.con-foto > strong, .carf-drop.con-foto > span { display: none; }
    .carf-drop .carf-foto { display: block; width: 100%; height: auto; border-radius: 16px; }

    .carf-preview { position: sticky; top: 96px; }
    .carf-preview .ui-panel-body { padding: 0; }
    .carf-prev-img { min-height: 150px; background: linear-gradient(135deg, var(--ui-verde), var(--ui-lima)); display: grid; place-items: center; overflow: hidden; }
    .carf-prev-img.con-foto { min-height: 0; background: none; display: block; }
    .carf-prev-img .carf-foto { display: block; width: 100%; height: auto; }
    .carf-prev-img span { font-size: 48px; font-weight: 800; color: rgba(255,255,255,.92); }
    .carf-prev-body { padding: 18px 20px 20px; }
    .carf-prev-body h4 { margin: 8px 0 4px; font-size: 18px; font-weight: 800; color: var(--ui-texto); word-break: break-word; }
    .carf-prev-body p { margin: 0; font-size: 13.5px; color: var(--ui-texto-2); }
    .carf-botones { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--ui-borde); flex-wrap: wrap; }
    .carf-quitar { display: inline-flex; align-items: center; gap: 8px; margin-top: 4px; font-size: 13px; color: #B91C1C; font-weight: 600; cursor: pointer; }
    @media (max-width: 900px) { .carf-layout { grid-template-columns: 1fr; } .carf-preview { position: static; } }
</style>

@php
    $imagenActual = $carrera->imagen ? asset($carrera->imagen) : null;
@endphp

<div class="carf-layout"
     x-data="{
        nombre: @js(old('nombre', $carrera->nombre ?? '')),
        horas: @js((string) old('horas_requeridas', $carrera->horas_requeridas ?? 90)),
        activo: {{ old('activo', $carrera->activo ?? true) ? 'true' : 'false' }},
        imagen: @js($imagenActual),
        quitar: false,
        sobre: false,
        get siglas() {
            return this.nombre.split(/\s+/).filter(p => p.length > 2).slice(0, 2).map(p => p[0]).join('').toUpperCase() || 'C';
        },
        elegir(e) {
            const archivo = e.target.files[0];
            if (!archivo) return;
            this.quitar = false;
            this.imagen = URL.createObjectURL(archivo);
        }
     }">

    <div class="ui-panel">
        <div class="ui-panel-head">
            <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5"/></svg></span> Datos de la carrera</h3>
        </div>
        <div class="ui-panel-body">
            <div class="ui-form-grid">
                <div class="ui-campo completo">
                    <label for="nombre">Nombre de la carrera</label>
                    <input id="nombre" name="nombre" type="text" x-model="nombre" maxlength="120" required
                           placeholder="Ej: Tecnología en Desarrollo de Software"
                           class="ui-input @error('nombre') is-invalido @enderror">
                    @error('nombre') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="ui-campo">
                    <label for="horas_requeridas">Horas de vinculación requeridas</label>
                    <input id="horas_requeridas" name="horas_requeridas" type="number" min="1" max="2000" x-model="horas" required
                           class="ui-input @error('horas_requeridas') is-invalido @enderror">
                    <span class="ayuda">Total de horas que un estudiante debe cumplir.</span>
                    @error('horas_requeridas') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="ui-campo">
                    <label>Estado</label>
                    <label class="ui-switch" style="margin-top: 6px;">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" x-model="activo">
                        <span class="ui-switch-pista"></span>
                        <span style="font-size: 14px; font-weight: 600;" x-text="activo ? 'Activa: visible en el sistema' : 'Inactiva: oculta en los listados'"></span>
                    </label>
                </div>

                <div class="ui-campo completo">
                    <label for="imagen">Imagen de portada <span style="font-weight:500;color:var(--ui-texto-3)">(opcional)</span></label>
                    <div class="carf-drop" :class="{ 'is-over': sobre, 'con-foto': imagen && !quitar }" @dragover="sobre = true" @dragleave="sobre = false" @drop="sobre = false">
                        <template x-if="imagen && !quitar"><img class="carf-foto" :src="imagen" alt="Vista previa"></template>
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.6-4.6a2 2 0 0 1 2.8 0L16 16m-2-2 1.6-1.6a2 2 0 0 1 2.8 0L20 14M14 8h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/></svg>
                        <strong>Arrastra una imagen o haz clic para elegir</strong>
                        <span>JPG, PNG o WEBP · máximo 3 MB</span>
                        <input id="imagen" name="imagen" type="file" accept="image/jpeg,image/png,image/webp" @change="elegir($event)">
                    </div>
                    @error('imagen') <span class="error">{{ $message }}</span> @enderror
                    @if ($imagenActual)
                        <label class="carf-quitar">
                            <input type="checkbox" name="quitar_imagen" value="1" x-model="quitar">
                            Quitar la imagen actual
                        </label>
                    @endif
                </div>
            </div>

            <div class="carf-botones">
                <a href="{{ route('admin.carreras.index') }}" class="ui-btn ui-btn-suave">Cancelar</a>
                <button type="submit" class="ui-btn ui-btn-primario">
                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    {{ $textoBoton }}
                </button>
            </div>
        </div>
    </div>

    {{-- Vista previa en vivo --}}
    <div class="ui-panel carf-preview" aria-hidden="true">
        <div class="ui-panel-head"><h3>Vista previa</h3></div>
        <div class="ui-panel-body">
            <div class="carf-prev-img" :class="imagen && !quitar && 'con-foto'">
                <template x-if="imagen && !quitar"><img class="carf-foto" :src="imagen" alt=""></template>
                <template x-if="!imagen || quitar"><span x-text="siglas"></span></template>
            </div>
            <div class="carf-prev-body">
                <span class="ui-tag" :class="activo ? 'verde' : 'gris'" x-text="activo ? 'Activa' : 'Inactiva'"></span>
                <h4 x-text="nombre || 'Nombre de la carrera'"></h4>
                <p><strong x-text="horas || 0"></strong> horas de vinculación requeridas</p>
            </div>
        </div>
    </div>
</div>
