@php
    $subidos = $inscripcion->certificadosEstudiante->whereIn('tipo_certificado_id', $tiposCertificado->pluck('id'));
    $aprobados = $subidos->where('estado', 'aprobado')->count();
    $pendientes = $subidos->where('estado', 'pendiente')->count();
    $total = $tiposCertificado->count();
    $pct = $total > 0 ? round($aprobados / $total * 100) : 0;
    $etiquetas = [
        'sin_subir' => ['Sin subir', 'gris'],
        'pendiente' => ['Por revisar', 'ambar'],
        'aprobado'  => ['Aprobado', 'verde'],
        'rechazado' => ['Rechazado', 'rojo'],
    ];
@endphp

<x-app-layout>
    <style>
        .cs-resumen { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; padding: 20px 22px; }
        .cs-resumen .ui-progreso { flex: 1 1 260px; height: 12px; }
        .cs-resumen strong { font-size: 26px; font-weight: 800; color: var(--ui-texto); }
        .cs-doc { display: grid; grid-template-columns: 70px minmax(0, 1fr) auto; gap: 16px; align-items: center; padding: 16px 22px; }
        .cs-doc + .cs-doc { border-top: 1px solid #EEF2F0; }
        .cs-doc.es-pendiente { background: #FFFCF2; }
        .cs-cod { font-size: 12px; font-weight: 800; text-align: center; color: var(--ui-verde); background: var(--ui-menta); padding: 8px 4px; border-radius: 12px; }
        .cs-info strong { display: block; font-size: 14.5px; color: var(--ui-texto); }
        .cs-info small { display: block; margin-top: 3px; font-size: 12.5px; color: var(--ui-texto-3); }
        .cs-info .cs-motivo { margin-top: 6px; padding: 8px 10px; border-radius: 10px; background: #FEF2F2; color: #991B1B; font-size: 12.5px; }
        .cs-acciones { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
        .cs-btn-ok { background: linear-gradient(135deg, #22C55E, #15803D); color: #fff !important; }
        .cs-btn-no { background: #fff; color: #B91C1C !important; border: 1px solid #FECACA; }
        .cs-btn-no:hover { background: #FEF2F2; }
        @media (max-width: 760px) {
            .cs-doc { grid-template-columns: 58px minmax(0, 1fr); }
            .cs-acciones { grid-column: 1 / -1; justify-content: flex-start; }
        }
    </style>

    <div class="ui-wrap" style="max-width: 1100px;">
        <x-ui.hero :volver="route('docente.certificados.index')" volver-texto="Mis estudiantes"
                   :titulo="$inscripcion->estudiante->name" :subtitulo="$inscripcion->proyecto->nombre">
            <x-slot:acciones>
                @if ($pendientes)
                    <span class="ui-btn ui-btn-blanco" style="cursor:default">{{ $pendientes }} {{ $pendientes === 1 ? 'documento' : 'documentos' }} por revisar</span>
                @endif
            </x-slot:acciones>
        </x-ui.hero>

        <section class="ui-panel">
            <div class="cs-resumen">
                <div>
                    <small style="display:block;font-size:12px;font-weight:700;color:var(--ui-texto-3);text-transform:uppercase">Documentos aprobados</small>
                    <strong>{{ $aprobados }} / {{ $total }}</strong>
                </div>
                <div class="ui-progreso"><span style="width: {{ $pct }}%"></span></div>
                <span class="ui-tag {{ $pct === 100 ? 'verde' : 'gris' }}">{{ $pct }}%</span>
            </div>
        </section>

        <section class="ui-panel" style="margin-top: 20px;">
            <div class="ui-panel-head">
                <h3><span class="ui-chip-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8l-5-5Zm0 0v5h5M9 13h6m-6 4h4"/></svg></span> Documentos del estudiante</h3>
            </div>

            @forelse ($tiposCertificado as $tipo)
                @php
                    $subido = $subidos->firstWhere('tipo_certificado_id', $tipo->id);
                    $estado = $subido->estado ?? 'sin_subir';
                @endphp
                <div class="cs-doc {{ $estado === 'pendiente' ? 'es-pendiente' : '' }}">
                    <span class="cs-cod">{{ $tipo->codigo }}</span>
                    <div class="cs-info">
                        <strong>{{ $tipo->nombre }}</strong>
                        <small>
                            <span class="ui-tag {{ $etiquetas[$estado][1] }}">{{ $etiquetas[$estado][0] }}</span>
                            @if ($subido) &nbsp;Subido {{ $subido->updated_at?->format('d/m/Y H:i') }} @endif
                        </small>
                        @if ($estado === 'rechazado' && $subido->observaciones_docente)
                            <div class="cs-motivo"><strong style="display:inline">Motivo:</strong> {{ $subido->observaciones_docente }}</div>
                        @endif
                    </div>
                    <div class="cs-acciones">
                        @if ($subido)
                            <a href="{{ route('certificados-estudiante.descargar', $subido->id) }}" target="_blank" class="ui-btn ui-btn-suave ui-btn-sm">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                                Ver archivo
                            </a>
                        @endif
                        @if ($subido && $estado === 'pendiente')
                            <button type="button" class="ui-btn ui-btn-sm cs-btn-ok btn-aprobar" data-certificado="{{ $subido->id }}" data-nombre="{{ $tipo->codigo }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                Aprobar
                            </button>
                            <button type="button" class="ui-btn ui-btn-sm cs-btn-no btn-rechazar" data-certificado="{{ $subido->id }}" data-nombre="{{ $tipo->codigo }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                                Rechazar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="ui-vacio">No hay documentos requeridos configurados.</div>
            @endforelse
        </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-aprobar').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.certificado;
            if (!await sistemaPreguntar('¿Aprobar el documento ' + btn.dataset.nombre + '?', 'El estudiante verá que su documento fue aprobado.', { boton: 'Sí, aprobar' })) return;

            fetch(`/api/certificados-estudiante/${id}/aprobar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al aprobar');
                if (data.certificado_final_generado) {
                    sistemaAviso('success', '¡Documentos completos!', 'Con este documento se completaron todos. Se generó el certificado final de horas cumplidas.');
                } else {
                    sistemaAviso('success', 'Documento aprobado', btn.dataset.nombre + ' quedó aprobado.');
                }
                window.location.reload();
            })
            .catch((err) => sistemaAlerta('error', 'No se pudo aprobar', err.message));
        });
    });

    document.querySelectorAll('.btn-rechazar').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.certificado;
            const motivo = await sistemaPedirTexto('Rechazar ' + btn.dataset.nombre, {
                texto: 'El estudiante verá este motivo y podrá volver a subir el documento.',
                placeholder: 'Explica qué debe corregir...',
                minimo: 5,
                boton: 'Rechazar documento',
            });
            if (!motivo) return;

            fetch(`/api/certificados-estudiante/${id}/rechazar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ observaciones_docente: motivo }),
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al rechazar');
                sistemaAviso('success', 'Documento rechazado', data.message || 'Se notificó al estudiante.');
                window.location.reload();
            })
            .catch((err) => sistemaAlerta('error', 'No se pudo rechazar', err.message));
        });
    });
});
</script>
</x-app-layout>
