<x-app-layout>
    <style>
        .po-item { display: grid; grid-template-columns: 46px minmax(0, 1fr) auto; gap: 14px; align-items: center; padding: 16px 22px; }
        .po-item + .po-item { border-top: 1px solid #EEF2F0; }
        .po-item.pendiente { background: #FFFCF2; }
        .po-item .ui-avatar { width: 46px; height: 46px; border-radius: 15px; font-size: 15px; }
        .po-info strong { display: block; font-size: 14.5px; color: var(--ui-texto); }
        .po-info small { display: block; font-size: 12.5px; color: var(--ui-texto-3); margin-top: 2px; }
        .po-act { display: inline-flex; align-items: center; gap: 6px; margin-top: 6px; font-size: 13px; font-weight: 700; color: var(--ui-verde); background: var(--ui-menta); padding: 4px 10px; border-radius: 99px; }
        .po-acciones { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
        @media (max-width: 700px) { .po-item { grid-template-columns: 46px minmax(0, 1fr); } .po-acciones { grid-column: 1 / -1; justify-content: flex-start; } }
    </style>

    <div class="ui-wrap" style="max-width: 1000px;">
        <x-ui.hero :volver="route('docente.actividades-vinculacion.index')" volver-texto="Actividades de vinculación"
                   titulo="Postulaciones de mis estudiantes" subtitulo="Estudiantes que eligieron una actividad del catálogo y esperan tu aprobación." />

        <section class="ui-panel">
            @forelse ($postulaciones as $postulacion)
                @php
                    $nombreEst = $postulacion->inscripcion->estudiante->name;
                    $ini = mb_strtoupper(collect(preg_split('/\s+/', $nombreEst))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                @endphp
                <div class="po-item {{ $postulacion->estado }}" id="fila-postulacion-{{ $postulacion->id }}">
                    <span class="ui-avatar">{{ $ini }}</span>
                    <div class="po-info">
                        <strong>{{ $nombreEst }}</strong>
                        <small>{{ $postulacion->inscripcion->proyecto->nombre }} · {{ $postulacion->actividad->carrera->nombre }}</small>
                        <span class="po-act">🎯 {{ $postulacion->actividad->titulo }}</span>
                    </div>
                    <div class="po-acciones">
                        @if ($postulacion->estado === 'pendiente')
                            <button type="button" class="btn-aprobar ui-btn ui-btn-primario ui-btn-sm" data-postulacion="{{ $postulacion->id }}">Aprobar</button>
                            <button type="button" class="btn-rechazar ui-btn ui-btn-peligro ui-btn-sm" data-postulacion="{{ $postulacion->id }}">Rechazar</button>
                        @elseif ($postulacion->estado === 'aprobada')
                            <span class="ui-tag verde">Aprobada</span>
                        @else
                            <span class="ui-tag rojo">Rechazada</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="ui-vacio">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg></div>
                    <strong>No hay postulaciones todavía</strong>
                    Cuando un estudiante elija una actividad aparecerá aquí.
                </div>
            @endforelse
        </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.querySelectorAll('.btn-aprobar').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.postulacion;
            if (!await sistemaPreguntar('¿Aprobar esta postulación?', 'El estudiante quedará asignado a la actividad.', { boton: 'Sí, aprobar' })) return;
            fetch(`/api/postulaciones-actividad/${id}/aprobar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al aprobar');
                sistemaAviso('success', 'Listo', data.message);
                window.location.reload();
            })
            .catch((err) => sistemaAlerta('error', 'No se pudo completar', err.message));
        });
    });

    document.querySelectorAll('.btn-rechazar').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = btn.dataset.postulacion;
            const motivo = await sistemaPedirTexto('Motivo del rechazo', {
                texto: 'El estudiante podrá ver este motivo.',
                placeholder: 'Explica por qué se rechaza la postulación...',
                minimo: 5,
                boton: 'Rechazar postulación',
            });
            if (!motivo) return;

            fetch(`/api/postulaciones-actividad/${id}/rechazar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ observaciones_docente: motivo }),
            })
            .then(async (res) => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Error al rechazar');
                sistemaAviso('success', 'Listo', data.message);
                window.location.reload();
            })
            .catch((err) => sistemaAlerta('error', 'No se pudo completar', err.message));
        });
    });
});
</script>
</x-app-layout>