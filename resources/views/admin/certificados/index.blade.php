<x-app-layout>
    <div class="ui-wrap">
        <x-ui.hero etiqueta="Documentación" titulo="Certificados de vinculación"
                   subtitulo="Aquí aparecen los estudiantes que ya tienen todos sus documentos aprobados por el docente, listos para emitir su certificado." />

        @if ($inscripciones->isEmpty())
            <section class="ui-panel">
                <div class="ui-vacio" style="padding: 48px 20px;">
                    <div class="ui-vacio-ico"><svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-3 2.5L8 22l4-2 4 2-1-4.5M6 3h12a1 1 0 0 1 1 1v6"/></svg></div>
                    <strong>Todavía no hay estudiantes listos para certificar</strong>
                    Un estudiante aparece aquí cuando el docente le aprueba todos los documentos requeridos.
                    <div style="margin-top:14px"><a href="{{ route('admin.tipos-certificado.index') }}" class="ui-btn ui-btn-suave ui-btn-sm">Ver documentos requeridos</a></div>
                </div>
            </section>
        @else
            <section class="ui-panel">
                <div class="ui-tabla-wrap">
                    <table class="ui-tabla">
                        <thead>
                            <tr><th>Estudiante</th><th>Cédula</th><th>Proyecto</th><th>Horas cumplidas</th><th>Certificado</th><th style="text-align:right">Acciones</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($inscripciones as $inscripcion)
                                @php
                                    $est = $inscripcion->estudiante;
                                    $ini = mb_strtoupper(collect(preg_split('/\s+/', $est->name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                                    $cert = $inscripcion->certificadoAdministrativo;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="ui-celda">
                                            <span class="ui-avatar">{{ $ini }}</span>
                                            <div style="min-width:0"><strong>{{ $est->name }}</strong><small>{{ $est->email }}</small></div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($est->cedula)
                                            {{ $est->cedula }}
                                        @else
                                            <a href="{{ route('admin.usuarios.edit', $est->id) }}" class="ui-tag rojo" style="text-decoration:none">Falta cédula · completar</a>
                                        @endif
                                    </td>
                                    <td>{{ $inscripcion->proyecto->nombre }}</td>
                                    <td>
                                        @if ((int) $inscripcion->horas_cumplidas > 0)
                                            <span class="ui-num">{{ $inscripcion->horas_cumplidas }} h</span>
                                        @else
                                            <span class="ui-tag ambar" title="El docente debe registrarlas en Certificados">Sin registrar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($cert)
                                            <span class="ui-tag verde">Generado</span>
                                            <small style="display:block;margin-top:4px;color:var(--ui-texto-3);font-size:12px">{{ $cert->numero_certificado }}</small>
                                        @else
                                            <span class="ui-tag gris">Sin generar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="ui-acciones">
                                            <form action="{{ route('admin.certificados.generar', $inscripcion->id) }}" method="POST"
                                                  data-confirm-title="{{ $cert ? '¿Regenerar el certificado?' : '¿Generar el certificado?' }}"
                                                  data-confirm-text="Se creará el certificado de vinculación de {{ $est->name }}."
                                                  data-confirm-button="{{ $cert ? 'Sí, regenerar' : 'Sí, generar' }}">
                                                @csrf
                                                <button type="submit" class="ui-btn ui-btn-primario ui-btn-sm">{{ $cert ? 'Regenerar' : 'Generar certificado' }}</button>
                                            </form>
                                            @if ($cert)
                                                <a href="{{ route('admin.certificados.descargar', $cert->id) }}" class="ui-btn ui-btn-suave ui-btn-sm">PDF</a>
                                                <a href="{{ route('admin.certificados.descargar-word', $cert->id) }}" class="ui-btn ui-btn-suave ui-btn-sm">Word</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
