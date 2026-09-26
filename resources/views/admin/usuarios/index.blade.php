@php
    $roles = [
        'admin' => ['Administrador', '#7C3AED', '#EDE9FE'],
        'docente' => ['Docente', '#15803D', '#DCFCE7'],
        'estudiante' => ['Estudiante', '#B45309', '#FEF3C7'],
        'coordinador' => ['Coordinador', '#1D4ED8', '#DBEAFE'],
    ];
    $filtros = [null => ['Todos', $conteos['todos']], 'estudiante' => ['Estudiantes', $conteos['estudiante']], 'docente' => ['Docentes', $conteos['docente']], 'admin' => ['Administradores', $conteos['admin']]];
@endphp

<x-app-layout>
    <style>
        .permiso-btn {
            display: inline-flex; align-items: center; gap: 5px; padding: 7px 12px; border-radius: 11px; cursor: pointer;
            font-family: inherit; font-size: 12.5px; font-weight: 700; border: 1px dashed #93C5FD; background: #EFF6FF; color: #1D4ED8; white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .permiso-btn:hover { background: #DBEAFE; border-style: solid; }
        .permiso-btn.permiso-activo { border: 1px solid #86EFAC; background: #DCFCE7; color: #15803D; }
        .permiso-btn.permiso-activo:hover { background: #BBF7D0; }
        .us-rol { display: inline-flex; font-size: 12px; font-weight: 800; padding: 4px 10px; border-radius: 99px; }
        .us-buscar { display: flex; gap: 8px; flex: 1 1 320px; max-width: 520px; }
        .us-buscar .ui-input { flex: 1; }
    </style>

    <div class="ui-wrap">
        <x-ui.hero etiqueta="Personas" titulo="Usuarios"
                   :subtitulo="$conteos['todos'] . ' usuarios · ' . $conteos['estudiante'] . ' estudiantes · ' . $conteos['docente'] . ' docentes'">
            <x-slot:acciones>
                <a href="{{ route('admin.usuarios.create') }}" class="ui-btn ui-btn-blanco">
                    <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Nuevo usuario
                </a>
                <a href="{{ route('admin.usuarios.importar') }}" class="ui-btn ui-btn-vidrio">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M12 4v12m0-12-4 4m4-4 4 4"/></svg>
                    Carga masiva (Excel)
                </a>
            </x-slot:acciones>
        </x-ui.hero>

        <div class="ui-barra">
            <nav class="ui-filtros" aria-label="Filtrar por rol">
                @foreach ($filtros as $clave => [$texto, $total])
                    <a href="{{ route('admin.usuarios.index', array_filter(['rol' => $clave ?: null, 'buscar' => $buscar ?: null])) }}"
                       class="ui-filtro {{ ($rolFiltro ?: null) === ($clave ?: null) ? 'is-activo' : '' }}">
                        {{ $texto }} <span>{{ $total }}</span>
                    </a>
                @endforeach
            </nav>
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="us-buscar" role="search">
                @if ($rolFiltro) <input type="hidden" name="rol" value="{{ $rolFiltro }}"> @endif
                <input type="search" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre, correo o cédula..." class="ui-input" aria-label="Buscar usuarios">
                <button type="submit" class="ui-btn ui-btn-primario">Buscar</button>
                @if ($buscar !== '')
                    <a href="{{ route('admin.usuarios.index', array_filter(['rol' => $rolFiltro])) }}" class="ui-btn ui-btn-suave">Limpiar</a>
                @endif
            </form>
        </div>

        <section class="ui-panel">
            <div class="ui-tabla-wrap">
                <table class="ui-tabla">
                    <thead>
                        <tr><th>Usuario</th><th>Cédula</th><th>Rol</th><th>Carrera</th><th style="text-align:right">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            @php
                                $ini = mb_strtoupper(collect(preg_split('/\s+/', trim($usuario->name)))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
                                [$rolTexto, $rolColor, $rolFondo] = $roles[$usuario->role] ?? [ucfirst($usuario->role), '#4B5563', '#F3F4F6'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="ui-celda">
                                        @if ($usuario->profile_photo_path)
                                            <img class="ui-avatar" src="{{ asset('storage/' . $usuario->profile_photo_path) }}" alt="" data-sin-zoom>
                                        @else
                                            <span class="ui-avatar">{{ $ini }}</span>
                                        @endif
                                        <div style="min-width:0"><strong>{{ $usuario->name }}</strong><small>{{ $usuario->email }}</small></div>
                                    </div>
                                </td>
                                <td style="white-space:nowrap">{{ $usuario->cedula ?? '—' }}</td>
                                <td><span class="us-rol" style="color:{{ $rolColor }};background:{{ $rolFondo }}">{{ $rolTexto }}</span></td>
                                <td style="font-size:13px;color:var(--ui-texto-2)">{{ $usuario->carrera->nombre ?? '—' }}</td>
                                <td>
                                    <div class="ui-acciones">
                                        @if ($usuario->role === 'estudiante')
                                            <form action="{{ route('admin.usuarios.permitir-actividad', $usuario) }}" method="POST"
                                                  @if ($usuario->permitir_nueva_actividad)
                                                      data-confirm-title="¿Quitar el permiso a {{ $usuario->name }}?" data-confirm-text="Ya no podrá inscribirse en otra actividad." data-confirm-button="Sí, quitar permiso"
                                                  @else
                                                      data-confirm-title="¿Permitir que {{ $usuario->name }} se inscriba en otra actividad?" data-confirm-text="Podrá inscribirse en UNA actividad más. El permiso se desactiva solo después de usarlo." data-confirm-button="Sí, permitir"
                                                  @endif>
                                                @csrf
                                                <button type="submit" class="permiso-btn {{ $usuario->permitir_nueva_actividad ? 'permiso-activo' : '' }}"
                                                        title="{{ $usuario->permitir_nueva_actividad ? 'Haz clic para quitar el permiso' : 'Permitir que se inscriba en otra actividad' }}">
                                                    {{ $usuario->permitir_nueva_actividad ? '✓ Puede inscribirse en otra' : '+ Permitir otra actividad' }}
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="ui-btn ui-btn-suave ui-btn-sm">Editar</a>
                                        @if ($usuario->id !== auth()->id())
                                            <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST"
                                                  data-confirm-title="¿Eliminar a {{ $usuario->name }}?" data-confirm-text="Esta acción no se puede deshacer.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ui-btn ui-btn-peligro ui-btn-sm">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">
                                <div class="ui-vacio">
                                    <strong>{{ $buscar !== '' ? 'No se encontraron usuarios para «' . $buscar . '»' : 'No hay usuarios registrados' }}</strong>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($usuarios->hasPages())
                <div class="ui-pag" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
                    <span style="font-size:13px;color:var(--ui-texto-3)">Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }} de {{ $usuarios->total() }}</span>
                    {{ $usuarios->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
