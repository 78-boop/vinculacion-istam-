@php($actividad = $actividad ?? null)

<div class="mb-4">
    <label for="proyecto_vinculacion_id" class="block font-medium text-sm text-gray-700">Proyecto</label>
    <select id="proyecto_vinculacion_id" name="proyecto_vinculacion_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">Seleccione un proyecto</option>
        @foreach ($proyectos as $proyecto)
            <option value="{{ $proyecto->id }}" {{ old('proyecto_vinculacion_id', $actividad?->proyecto_vinculacion_id) == $proyecto->id ? 'selected' : '' }}>
                {{ $proyecto->nombre }}
            </option>
        @endforeach
    </select>
    @error('proyecto_vinculacion_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="docente_id" class="block font-medium text-sm text-gray-700">Docente responsable</label>
    <input type="hidden" id="docente_id" name="docente_id" value="{{ old('docente_id', $actividad?->docente_id) }}">
    <div id="docente-seleccionado" class="mt-1 min-h-10 flex items-center border border-green-200 bg-green-50 rounded-lg px-3 text-sm font-medium text-green-900">
        {{ $docentes->firstWhere('id', old('docente_id', $actividad?->docente_id))?->name ?? 'Ningún docente seleccionado' }}
    </div>
    <div id="docentes-catalogo" class="hidden">
        @foreach ($docentes as $docente)
            <span class="docente-dato" data-id="{{ $docente->id }}" data-nombre="{{ strtolower($docente->name) }}" data-cedula="{{ strtolower($docente->cedula ?? '') }}">{{ $docente->name }}</span>
        @endforeach
    </div>
    @error('docente_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

@php($estudiantesMarcados = array_map('intval', (array) old('estudiante_ids', $actividad?->inscripciones?->pluck('estudiante_id')->all() ?? [])))
<div class="mb-4">
    <label class="block font-medium text-sm text-gray-700">Estudiantes que participan</label>
    <div id="lista-estudiantes" class="mt-1 border border-gray-200 rounded-md p-2" style="display:flex;flex-wrap:wrap;gap:8px;min-height:48px;align-items:center;">
        @foreach ($estudiantes as $estudiante)
            <label class="estudiante-opcion"
                   data-id="{{ $estudiante->id }}"
                   data-nombre="{{ $estudiante->name }}"
                   data-cedula="{{ $estudiante->cedula ?? '' }}"
                   data-carrera="{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}"
                   data-proyectos="{{ $estudiante->inscripciones->pluck('proyecto_vinculacion_id')->implode(',') }}"
                   style="display:none;align-items:center;gap:8px;padding:6px 8px 6px 12px;border-radius:99px;background:#E7F5EE;color:#065F46;font-size:13px;font-weight:600;">
                <input type="checkbox" name="estudiante_ids[]" value="{{ $estudiante->id }}" class="estudiante-checkbox" style="display:none" @checked(in_array($estudiante->id, $estudiantesMarcados))>
                <span>{{ $estudiante->name }}</span>
                <button type="button" class="quitar-estudiante" aria-label="Quitar a {{ $estudiante->name }}" style="width:22px;height:22px;border-radius:50%;border:0;background:#fff;color:#B91C1C;font-weight:800;line-height:1;cursor:pointer;">×</button>
            </label>
        @endforeach
        <p id="sin-estudiantes" class="text-sm text-gray-500" style="margin:0 4px;">Todavía no hay estudiantes seleccionados. Búscalos en el panel "Buscar estudiante".</p>
    </div>
    <p class="mt-1 text-xs text-gray-500">Aparecen todos los usuarios con rol estudiante. Si alguno no está inscrito en el proyecto, se inscribe automáticamente al guardar.</p>
    @error('estudiante_ids') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    @error('estudiante_ids.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="fecha_inicio" class="block font-medium text-sm text-gray-700">Fecha de inicio</label>
        <input id="fecha_inicio" type="text" name="fecha_inicio" value="{{ old('fecha_inicio', $actividad?->fecha_inicio?->format('Y-m-d')) }}" placeholder="dd/mm/aaaa" autocomplete="off" required class="actividad-datepicker mt-1 block w-full border-gray-300 rounded-md shadow-sm text-base">
        @error('fecha_inicio') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="fecha_finalizacion" class="block font-medium text-sm text-gray-700">Fecha de finalización</label>
        <input id="fecha_finalizacion" type="text" name="fecha_finalizacion" value="{{ old('fecha_finalizacion', $actividad?->fecha_finalizacion?->format('Y-m-d')) }}" placeholder="dd/mm/aaaa" autocomplete="off" required class="actividad-datepicker mt-1 block w-full border-gray-300 rounded-md shadow-sm text-base">
        @error('fecha_finalizacion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-4">
    <label for="lugar" class="block font-medium text-sm text-gray-700">Lugar</label>
    <input id="lugar" type="text" name="lugar" value="{{ old('lugar', $actividad?->lugar) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    @error('lugar') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="descripcion" class="block font-medium text-sm text-gray-700">Descripción de la actividad</label>
    <textarea id="descripcion" name="descripcion" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $actividad?->descripcion) }}</textarea>
    @error('descripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="horas" class="block font-medium text-sm text-gray-700">Horas</label>
    <input id="horas" type="number" step="any" min="0.5" max="999.99" name="horas" value="{{ old('horas', $actividad?->horas) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    @error('horas') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="estado" class="block font-medium text-sm text-gray-700">Estado</label>
    <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="pendiente" {{ old('estado', $actividad?->estado ?? 'pendiente') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
        <option value="aprobada" {{ old('estado', $actividad?->estado) === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
    </select>
    @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label for="comentario_docente" class="block font-medium text-sm text-gray-700">Comentario del docente (opcional)</label>
    <textarea id="comentario_docente" name="comentario_docente" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('comentario_docente', $actividad?->comentario_docente) }}</textarea>
    @error('comentario_docente') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const proyecto = document.getElementById('proyecto_vinculacion_id');
    const docenteId = document.getElementById('docente_id');
    const docenteSeleccionado = document.getElementById('docente-seleccionado');
    const buscarDocente = document.getElementById('buscar_docente');
    const resultadosDocente = document.getElementById('resultados-docente');
    const buscador = document.getElementById('buscar_estudiante');
    const resultadosEstudiante = document.getElementById('resultados-estudiante');
    const opciones = Array.from(document.querySelectorAll('.estudiante-opcion'));
    const docentes = Array.from(document.querySelectorAll('.docente-dato'));
    const activos = { docente: -1, estudiante: -1 };

    function normalizar(texto) {
        return (texto || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    function escapar(texto) {
        const div = document.createElement('div');
        div.textContent = texto || '';
        return div.innerHTML;
    }

    // Muestra como "chips" los estudiantes marcados
    function actualizarSeleccionados() {
        let visibles = 0;
        opciones.forEach(function (opcion) {
            const marcado = opcion.querySelector('input').checked;
            opcion.style.display = marcado ? 'inline-flex' : 'none';
            if (marcado) visibles++;
        });
        document.getElementById('sin-estudiantes').style.display = visibles ? 'none' : '';
    }

    function crearBoton(html, marcado) {
        const boton = document.createElement('button');
        boton.type = 'button';
        boton.setAttribute('role', 'option');
        boton.className = 'block w-full text-left px-3 py-2 text-sm hover:bg-green-50 focus:outline-none';
        boton.style.borderLeft = '3px solid ' + (marcado ? '#006B47' : 'transparent');
        boton.style.background = marcado ? '#F0FAF5' : '#FFFFFF';
        boton.innerHTML = html;
        return boton;
    }

    // ----- Docentes: lista completa al hacer clic, filtra al escribir -----
    function filtrarDocentes() {
        const termino = normalizar(buscarDocente.value.trim());
        activos.docente = -1;
        resultadosDocente.innerHTML = '';

        const lista = docentes.filter(function (d) {
            return !termino || normalizar(d.dataset.nombre).includes(termino) || normalizar(d.dataset.cedula).includes(termino);
        });

        lista.forEach(function (docente) {
            const marcado = docenteId.value === docente.dataset.id;
            const boton = crearBoton(
                '<strong style="display:block;color:#0F2A20">' + escapar(docente.textContent.trim()) + (marcado ? ' ✓' : '') + '</strong>' +
                (docente.dataset.cedula ? '<small style="color:#6B7280">Cédula: ' + escapar(docente.dataset.cedula) + '</small>' : ''),
                marcado
            );
            boton.addEventListener('click', function () {
                docenteId.value = docente.dataset.id;
                docenteSeleccionado.textContent = docente.textContent.trim();
                buscarDocente.value = '';
                resultadosDocente.classList.add('hidden');
            });
            resultadosDocente.appendChild(boton);
        });

        if (!lista.length) {
            resultadosDocente.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500">' +
                (docentes.length ? 'No se encontró ningún docente.' : 'Aún no hay usuarios con rol docente.') + '</p>';
        }
        resultadosDocente.classList.remove('hidden');
    }

    // ----- Estudiantes: todos los de rol estudiante, sin necesidad de escribir -----
    function filtrarEstudiantes() {
        const termino = normalizar(buscador.value.trim());
        const proyectoId = proyecto.value;
        activos.estudiante = -1;
        resultadosEstudiante.innerHTML = '';

        const lista = opciones.filter(function (o) {
            return !termino
                || normalizar(o.dataset.nombre).includes(termino)
                || normalizar(o.dataset.cedula).includes(termino)
                || normalizar(o.dataset.carrera).includes(termino);
        });

        lista.forEach(function (opcion) {
            const input = opcion.querySelector('input');
            const inscrito = proyectoId && opcion.dataset.proyectos.split(',').includes(proyectoId);
            const etiqueta = !proyectoId ? ''
                : inscrito
                    ? '<span style="font-size:11px;font-weight:700;color:#15803D;background:#DCFCE7;padding:2px 8px;border-radius:99px;white-space:nowrap">Inscrito</span>'
                    : '<span style="font-size:11px;font-weight:700;color:#B45309;background:#FEF3C7;padding:2px 8px;border-radius:99px;white-space:nowrap">Se inscribirá</span>';

            const boton = crearBoton(
                '<span style="display:flex;justify-content:space-between;gap:8px;align-items:center">' +
                    '<strong style="color:#0F2A20">' + escapar(opcion.dataset.nombre) + (input.checked ? ' ✓' : '') + '</strong>' + etiqueta +
                '</span>' +
                '<small style="color:#6B7280">' + escapar(opcion.dataset.carrera) + (opcion.dataset.cedula ? ' · ' + escapar(opcion.dataset.cedula) : '') + '</small>',
                input.checked
            );
            boton.addEventListener('click', function () {
                input.checked = !input.checked;   // otro clic lo quita
                actualizarSeleccionados();
                filtrarEstudiantes();
                buscador.focus();
            });
            resultadosEstudiante.appendChild(boton);
        });

        if (!lista.length) {
            resultadosEstudiante.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500">' +
                (opciones.length ? 'No se encontró ningún estudiante.' : 'Aún no hay usuarios con rol estudiante.') + '</p>';
        }
        resultadosEstudiante.classList.remove('hidden');
    }

    function pintarActivo(resultados, indice) {
        Array.from(resultados.querySelectorAll('button')).forEach(function (boton, i) {
            boton.style.outline = i === indice ? '2px solid #006B47' : 'none';
            boton.style.outlineOffset = '-2px';
            boton.setAttribute('aria-selected', i === indice ? 'true' : 'false');
            if (i === indice) boton.scrollIntoView({ block: 'nearest' });
        });
    }

    function navegar(evento, resultados, tipo) {
        if (resultados.classList.contains('hidden')) return;
        const botones = resultados.querySelectorAll('button');
        if (!botones.length) return;
        let activo = activos[tipo];

        if (evento.key === 'ArrowDown') { evento.preventDefault(); activo = Math.min(activo + 1, botones.length - 1); }
        else if (evento.key === 'ArrowUp') { evento.preventDefault(); activo = Math.max(activo - 1, -1); }
        else if (evento.key === 'Enter') { evento.preventDefault(); botones[Math.max(activo, 0)].click(); return; }
        else if (evento.key === 'Escape') { evento.preventDefault(); resultados.classList.add('hidden'); return; }
        else return;

        activos[tipo] = activo;
        pintarActivo(resultados, activo);
    }

    buscarDocente.addEventListener('focus', filtrarDocentes);
    buscarDocente.addEventListener('click', filtrarDocentes);
    buscarDocente.addEventListener('input', filtrarDocentes);
    buscarDocente.addEventListener('keydown', function (e) { navegar(e, resultadosDocente, 'docente'); });

    buscador.addEventListener('focus', filtrarEstudiantes);
    buscador.addEventListener('click', filtrarEstudiantes);
    buscador.addEventListener('input', filtrarEstudiantes);
    buscador.addEventListener('keydown', function (e) { navegar(e, resultadosEstudiante, 'estudiante'); });

    // Al cambiar de proyecto se actualizan las etiquetas "Inscrito / Se inscribirá"
    proyecto.addEventListener('change', function () {
        if (!resultadosEstudiante.classList.contains('hidden')) filtrarEstudiantes();
    });

    // Quitar un estudiante desde su chip
    document.getElementById('lista-estudiantes').addEventListener('click', function (e) {
        const quitar = e.target.closest('.quitar-estudiante');
        if (!quitar) return;
        e.preventDefault();
        quitar.closest('.estudiante-opcion').querySelector('input').checked = false;
        actualizarSeleccionados();
        if (!resultadosEstudiante.classList.contains('hidden')) filtrarEstudiantes();
    });

    document.addEventListener('mousedown', function (e) {
        if (!buscarDocente.contains(e.target) && !resultadosDocente.contains(e.target)) resultadosDocente.classList.add('hidden');
        if (!buscador.contains(e.target) && !resultadosEstudiante.contains(e.target)) resultadosEstudiante.classList.add('hidden');
    });

    actualizarSeleccionados();
});
</script>
