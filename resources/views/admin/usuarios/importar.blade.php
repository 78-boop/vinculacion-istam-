@php($resumen = session('resumen_importacion'))

<x-app-layout>
    <style>
        .imp-pasos { margin-top: 22px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .imp-paso { position: relative; background: #fff; border-radius: 20px; border: 1px solid var(--ui-borde); box-shadow: var(--ui-sombra); padding: 20px; }
        .imp-num {
            width: 38px; height: 38px; border-radius: 12px; display: grid; place-items: center; font-weight: 800;
            background: linear-gradient(135deg, var(--ui-verde-2), var(--ui-verde)); color: #fff; box-shadow: 0 8px 16px -8px rgba(0,107,71,.8);
        }
        .imp-paso h3 { margin: 14px 0 6px; font-size: 15.5px; font-weight: 800; color: var(--ui-texto); }
        .imp-paso p { margin: 0; font-size: 13.5px; color: var(--ui-texto-2); }
        .imp-paso .ui-btn { margin-top: 14px; }
        .imp-columnas { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .imp-columnas span { font-size: 12px; font-weight: 700; padding: 4px 9px; border-radius: 8px; background: #F1F5F3; color: var(--ui-texto-2); }
        .imp-columnas span.req { background: var(--ui-menta); color: var(--ui-verde); }

        .imp-form { margin-top: 20px; }
        .imp-drop {
            position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
            min-height: 200px; padding: 24px; text-align: center; border-radius: 20px; cursor: pointer;
            border: 2px dashed #BFDCCB; background: #F7FCF9; transition: border-color .2s, background .2s;
        }
        .imp-drop:hover, .imp-drop.is-over { border-color: var(--ui-verde); background: #EEF8F2; }
        .imp-drop.tiene { border-style: solid; border-color: var(--ui-verde); background: #EEF8F2; }
        .imp-drop input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .imp-drop-ico { width: 60px; height: 60px; border-radius: 18px; display: grid; place-items: center; background: #fff; color: var(--ui-verde); box-shadow: var(--ui-sombra); }
        .imp-drop-ico svg { width: 30px; height: 30px; }
        .imp-drop strong { font-size: 15px; color: var(--ui-texto); }
        .imp-drop span { font-size: 13px; color: var(--ui-texto-3); }
        .imp-opciones { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; justify-content: space-between; margin-top: 18px; }

        .imp-tabla { width: 100%; }
        .imp-tabla td, .imp-tabla th { padding: 10px 22px !important; font-size: 13.5px; }
        .imp-tabla td:first-child { font-weight: 800; color: var(--ui-texto-3); width: 70px; }
        .imp-scroll { max-height: 340px; overflow: auto; }

        @media (max-width: 860px) { .imp-pasos { grid-template-columns: 1fr; } }
    </style>

    <div class="ui-wrap" style="max-width: 1100px;">
        <section class="ui-hero">
            <div>
                <span class="ui-hero-eyebrow">Usuarios</span>
                <h1>Carga masiva de usuarios</h1>
                <p>Registra cientos de estudiantes y docentes de una sola vez desde un archivo Excel o CSV.</p>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="ui-btn ui-btn-vidrio">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
                Volver a usuarios
            </a>
        </section>

        {{-- ===== Resultado de la última importación ===== --}}
        @if ($resumen)
            <section class="ui-kpis" style="margin-top: 22px;">
                <div class="ui-kpi">
                    <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg></span></div>
                    <div class="ui-kpi-valor">{{ $resumen['creados'] }}</div>
                    <div class="ui-kpi-label">Usuarios creados de {{ $resumen['total'] }}</div>
                </div>
                <div class="ui-kpi azul">
                    <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4 2 9l10 5 10-5-10-5Zm-6 7.5V16c0 1.66 2.69 3 6 3s6-1.34 6-3v-4.5"/></svg></span></div>
                    <div class="ui-kpi-valor">{{ $resumen['estudiantes'] }} / {{ $resumen['docentes'] }}</div>
                    <div class="ui-kpi-label">Estudiantes / docentes</div>
                </div>
                <div class="ui-kpi rosa">
                    <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg></span></div>
                    <div class="ui-kpi-valor">{{ count($resumen['errores']) }}</div>
                    <div class="ui-kpi-label">Filas omitidas</div>
                </div>
                <div class="ui-kpi ambar">
                    <div class="ui-kpi-top"><span class="ui-kpi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg></span></div>
                    <div class="ui-kpi-valor">{{ count($resumen['avisos']) }}</div>
                    <div class="ui-kpi-label">Avisos</div>
                </div>
            </section>

            @foreach ([['errores', 'Filas omitidas (corrígelas y vuelve a subir solo esas)', 'rojo'], ['avisos', 'Avisos (los usuarios sí se crearon)', 'ambar']] as [$clave, $titulo, $color])
                @if (count($resumen[$clave]))
                    <section class="ui-panel" style="margin-top: 20px;">
                        <div class="ui-panel-head">
                            <h3><span class="ui-tag {{ $color }}">{{ count($resumen[$clave]) }}</span> {{ $titulo }}</h3>
                        </div>
                        <div class="imp-scroll">
                            <table class="imp-tabla">
                                <thead><tr><th>Fila</th><th>Nombre</th><th>Motivo</th></tr></thead>
                                <tbody>
                                    @foreach ($resumen[$clave] as $item)
                                        <tr><td>{{ $item['fila'] }}</td><td>{{ $item['nombre'] }}</td><td>{{ $item['motivo'] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            @endforeach
        @endif

        {{-- ===== Pasos ===== --}}
        <section class="imp-pasos">
            <div class="imp-paso">
                <span class="imp-num">1</span>
                <h3>Descarga la plantilla</h3>
                <p>Viene con las columnas listas y listas desplegables de rol y carrera.</p>
                <a href="{{ route('admin.usuarios.importar.plantilla') }}" class="ui-btn ui-btn-primario ui-btn-sm">
                    <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14"/></svg>
                    Descargar plantilla Excel
                </a>
            </div>
            <div class="imp-paso">
                <span class="imp-num">2</span>
                <h3>Llena los datos</h3>
                <p>Una fila por usuario. También puedes usar tu propio Excel con estos encabezados:</p>
                <div class="imp-columnas">
                    <span class="req">Nombre</span><span class="req">Cédula</span><span class="req">Correo</span>
                    <span>Rol</span><span>Carrera</span><span>Contraseña</span>
                </div>
            </div>
            <div class="imp-paso">
                <span class="imp-num">3</span>
                <h3>Sube el archivo</h3>
                <p>Si no pones contraseña, será la <strong>cédula</strong>. Los correos o cédulas repetidos se omiten.</p>
            </div>
        </section>

        {{-- ===== Formulario ===== --}}
        <form action="{{ route('admin.usuarios.importar.store') }}" method="POST" enctype="multipart/form-data" class="ui-panel imp-form"
              data-confirm-title="¿Importar los usuarios de este archivo?"
              data-confirm-text="Se crearán las cuentas nuevas. Puede tardar un momento si son muchos usuarios."
              data-confirm-button="Sí, importar"
              x-data="{ archivo: '', sobre: false }">
            @csrf
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M12 4v12m0-12-4 4m4-4 4 4"/></svg></span> Subir archivo</h3>
            </div>
            <div class="ui-panel-body">
                <label class="imp-drop" :class="{ 'is-over': sobre, 'tiene': archivo }" @dragover="sobre = true" @dragleave="sobre = false" @drop="sobre = false">
                    <span class="imp-drop-ico">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13l2 2 4-4"/></svg>
                    </span>
                    <strong x-text="archivo || 'Arrastra aquí tu archivo o haz clic para elegirlo'"></strong>
                    <span x-text="archivo ? 'Listo para importar · haz clic para cambiarlo' : 'Excel (.xlsx, .xls) o CSV · máximo 10 MB y 5000 filas'"></span>
                    <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required @change="archivo = $event.target.files[0]?.name || ''">
                </label>
                @error('archivo') <p style="color:#B91C1C;font-size:13px;font-weight:600;margin:8px 0 0">{{ $message }}</p> @enderror

                <div class="imp-opciones">
                    <div class="ui-campo" style="min-width: 260px;">
                        <label for="rol_defecto">Rol para las filas sin rol</label>
                        <select id="rol_defecto" name="rol_defecto" class="ui-input">
                            <option value="estudiante" @selected(old('rol_defecto') !== 'docente')>Estudiante</option>
                            <option value="docente" @selected(old('rol_defecto') === 'docente')>Docente</option>
                        </select>
                    </div>
                    <button type="submit" class="ui-btn ui-btn-primario" :disabled="!archivo" :style="!archivo && 'opacity:.5;cursor:not-allowed'">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m17-8h-6m3-3v6M13 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/></svg>
                        Importar usuarios
                    </button>
                </div>

                @if ($carreras->isNotEmpty())
                    <p style="margin: 16px 0 0; font-size: 12.5px; color: var(--ui-texto-3);">
                        Carreras reconocidas: {{ $carreras->pluck('nombre')->implode(' · ') }}. También sirve escribir parte del nombre (ej. "Software").
                    </p>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
