@php
    $etiquetas = [
        'sin_subir' => ['Sin subir', 'gris'],
        'pendiente' => ['En revisión', 'ambar'],
        'aprobado'  => ['Aprobado', 'verde'],
        'rechazado' => ['Rechazado', 'rojo'],
    ];
@endphp

<x-app-layout>
    <style>
        .mc-resumen { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; padding: 20px 22px; }
        .mc-resumen .ui-progreso { flex: 1 1 260px; height: 12px; }
        .mc-resumen strong { font-size: 26px; font-weight: 800; color: var(--ui-texto); }
        .mc-doc { display: grid; grid-template-columns: 70px minmax(0, 1fr) auto; gap: 16px; align-items: center; padding: 16px 22px; }
        .mc-doc + .mc-doc { border-top: 1px solid #EEF2F0; }
        .mc-doc.rechazado { background: #FFF7F7; }
        .mc-doc.aprobado .mc-cod { background: #DCFCE7; color: #15803D; }
        .mc-cod { font-size: 12px; font-weight: 800; text-align: center; color: var(--ui-verde); background: var(--ui-menta); padding: 8px 4px; border-radius: 12px; }
        .mc-info strong { display: block; font-size: 14.5px; color: var(--ui-texto); }
        .mc-info small { display: block; margin-top: 4px; font-size: 12.5px; color: var(--ui-texto-3); }
        .mc-motivo { margin-top: 8px; padding: 8px 10px; border-radius: 10px; background: #FEF2F2; color: #991B1B; font-size: 12.5px; }
        .mc-acciones { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
        .mc-subir { position: relative; overflow: hidden; cursor: pointer; }
        .mc-subir input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .mc-fin { margin: 18px 22px 22px; padding: 16px 18px; border-radius: 16px; background: linear-gradient(135deg, #DCFCE7, #F0FDF4); color: #166534; font-weight: 700; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .mc-fin > span { font-size: 26px; }
        .mc-fin-texto { flex: 1 1 260px; }
        .mc-fin-texto small { display: block; margin-top: 4px; font-size: 12.5px; font-weight: 600; color: #15803D; }
        @media (max-width: 760px) {
            .mc-doc { grid-template-columns: 58px minmax(0, 1fr); }
            .mc-acciones { grid-column: 1 / -1; justify-content: flex-start; }
        }
    </style>

    <div class="ui-wrap" style="max-width: 1100px;">
        <x-ui.hero etiqueta="Mi vinculación" titulo="Mis documentos"
                   subtitulo="Sube cada documento requerido. Tu docente los revisará y te avisará si alguno debe corregirse." />

        @if ($inscripciones->isEmpty())
            <section class="ui-panel">
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg></div>
                    <strong>Aún no participas en ningún proyecto</strong>
                    Inscríbete en una actividad para poder subir tus documentos.
                    <div style="margin-top:12px"><a href="{{ route('estudiante.proyectos.index') }}" class="ui-btn ui-btn-primario ui-btn-sm">Ver proyectos disponibles</a></div>
                </div>
            </section>
        @else
            @foreach ($inscripciones as $inscripcion)
                @php
                    $subidos = $inscripcion->certificadosEstudiante->whereIn('tipo_certificado_id', $tiposCertificado->pluck('id'));
                    $aprobados = $subidos->where('estado', 'aprobado')->count();
                    $total = $tiposCertificado->count();
                    $pct = $total > 0 ? round($aprobados / $total * 100) : 0;
                @endphp
                <section class="ui-panel" style="margin-bottom: 22px;">
                    <div class="ui-panel-head">
                        <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg></span> {{ $inscripcion->proyecto->nombre }}</h3>
                    </div>
                    <div class="mc-resumen">
                        <div>
                            <small style="display:block;font-size:12px;font-weight:700;color:var(--ui-texto-3);text-transform:uppercase">Documentos aprobados</small>
                            <strong>{{ $aprobados }} / {{ $total }}</strong>
                        </div>
                        <div class="ui-progreso"><span style="width: {{ $pct }}%"></span></div>
                        <span class="ui-tag {{ $pct === 100 ? 'verde' : 'gris' }}">{{ $pct }}%</span>
                    </div>

                    @forelse ($tiposCertificado as $tipo)
                        @php
                            $subido = $subidos->firstWhere('tipo_certificado_id', $tipo->id);
                            $estado = $subido->estado ?? 'sin_subir';
                        @endphp
                        <div class="mc-doc {{ $estado }}">
                            <span class="mc-cod">{{ $tipo->codigo }}</span>
                            <div class="mc-info">
                                <strong>{{ $tipo->nombre }}</strong>
                                <small>
                                    <span class="ui-tag {{ $etiquetas[$estado][1] }}">{{ $etiquetas[$estado][0] }}</span>
                                    @if ($subido) &nbsp;Subido {{ $subido->updated_at?->format('d/m/Y H:i') }} @endif
                                </small>
                                @if ($estado === 'rechazado' && $subido->observaciones_docente)
                                    <div class="mc-motivo"><strong style="display:inline">Tu docente indica:</strong> {{ $subido->observaciones_docente }}</div>
                                @endif
                            </div>
                            <div class="mc-acciones">
                                @if ($subido && $estado !== 'rechazado')
                                    <a href="{{ route('certificados-estudiante.descargar', $subido->id) }}" target="_blank" class="ui-btn ui-btn-suave ui-btn-sm">Ver archivo</a>
                                @endif
                                @if ($estado !== 'aprobado')
                                    <label class="ui-btn ui-btn-primario ui-btn-sm mc-subir">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0-4 4m4-4 4 4M5 20h14"/></svg>
                                        {{ $subido ? 'Reemplazar' : 'Subir' }}
                                        <input type="file" class="input-certificado"
                                               data-inscripcion="{{ $inscripcion->id }}" data-tipo="{{ $tipo->id }}" data-nombre="{{ $tipo->codigo }}"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" aria-label="Subir {{ $tipo->nombre }}">
                                    </label>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ui-vacio">El administrador aún no configuró los documentos requeridos.</div>
                    @endforelse

                    @if ($aprobados >= $total && $total > 0)
                        <div class="mc-fin">
                            <span>🎉</span>
                            <div class="mc-fin-texto">
                                ¡Todos tus documentos fueron aprobados! Tu certificado de horas cumplidas se generó automáticamente.
                                @if ($inscripcion->certificadoAdministrativo)
                                    <small>Tu certificado de vinculación ya fue emitido. Se descarga con la fecha del día.</small>
                                @else
                                    <small>Cuando el administrador emita tu certificado de vinculación, podrás descargarlo aquí.</small>
                                @endif
                            </div>
                            @if ($inscripcion->certificadoAdministrativo)
                                <div class="mc-acciones">
                                    <a href="{{ route('certificados-estudiante.vinculacion.pdf', $inscripcion->certificadoAdministrativo) }}" class="ui-btn ui-btn-primario ui-btn-sm">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0-4-4m4 4 4-4M5 20h14"/></svg>
                                        Descargar PDF
                                    </a>
                                    <a href="{{ route('certificados-estudiante.vinculacion.word', $inscripcion->certificadoAdministrativo) }}" class="ui-btn ui-btn-suave ui-btn-sm">Word</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </section>
            @endforeach
        @endif
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.input-certificado').forEach(function (input) {
        input.addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                sistemaAlerta('error', 'Archivo muy grande', 'El archivo no puede pesar más de 10 MB.');
                input.value = '';
                return;
            }

            if (!await sistemaPreguntar('¿Subir ' + file.name + '?', 'Se enviará como ' + input.dataset.nombre + ' para que tu docente lo revise.', { boton: 'Sí, subir' })) {
                input.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('inscripcion_id', input.dataset.inscripcion);
            formData.append('tipo_certificado_id', input.dataset.tipo);
            formData.append('archivo', file);

            Swal.fire({ title: 'Subiendo documento...', text: 'Un momento, por favor.', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

            fetch('/api/certificados-estudiante', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok) {
                    const errores = data.errors ? Object.values(data.errors).flat().join(' ') : null;
                    throw new Error(errores || data.error || data.message || 'Error al subir el archivo');
                }
                sistemaAviso('success', 'Documento enviado', data.message);
                window.location.reload();
            })
            .catch((error) => {
                input.value = '';
                sistemaAlerta('error', 'No se pudo subir', error.message);
            });
        });
    });
});
</script>
</x-app-layout>
