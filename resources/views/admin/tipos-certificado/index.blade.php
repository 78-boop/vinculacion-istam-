<x-app-layout>
    <div class="ui-wrap" style="max-width: 1100px;">
        <x-ui.hero etiqueta="Documentación" titulo="Documentos requeridos"
                   subtitulo="Los documentos que cada estudiante debe subir. La posición define en qué orden los ven el estudiante y el docente.">
            <x-slot:acciones>
                <a href="{{ route('admin.tipos-certificado.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nuevo documento
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        <section class="ui-panel">
            <div class="ui-tabla-wrap">
                <table class="ui-tabla">
                    <thead>
                        <tr><th style="width:90px">Posición</th><th>Código</th><th>Documento</th><th>Estado</th><th style="text-align:right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr style="{{ $tipo->activo ? '' : 'opacity:.6' }}">
                                <td><span class="ui-celda-ini" style="width:34px;height:34px;font-size:13px">{{ $tipo->orden }}</span></td>
                                <td><span style="font-family:ui-monospace,monospace;font-weight:800;color:var(--ui-verde);background:var(--ui-menta);padding:5px 9px;border-radius:9px;font-size:13px">{{ $tipo->codigo }}</span></td>
                                <td style="font-weight:600">{{ $tipo->nombre }}</td>
                                <td><span class="ui-tag {{ $tipo->activo ? 'verde' : 'gris' }}">{{ $tipo->activo ? 'Visible' : 'Oculto' }}</span></td>
                                <td>
                                    <div class="ui-acciones">
                                        <a href="{{ route('admin.tipos-certificado.edit', $tipo) }}" class="ui-btn ui-btn-suave ui-btn-sm">Editar</a>
                                        <form action="{{ route('admin.tipos-certificado.destroy', $tipo) }}" method="POST"
                                              data-confirm-title="¿Eliminar {{ $tipo->codigo }}?" data-confirm-text="Solo se puede eliminar si ningún estudiante ha subido este documento.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">
                                <div class="ui-vacio">
                                    <strong>No hay documentos configurados</strong>
                                    Agrega los formularios que los estudiantes deben entregar (FPVS01, FPVS02…).
                                    <div style="margin-top:12px"><a href="{{ route('admin.tipos-certificado.create') }}" class="ui-btn ui-btn-primario ui-btn-sm">Agregar documento</a></div>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
