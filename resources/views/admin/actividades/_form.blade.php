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

<div class="mb-4">
    <label class="block font-medium text-sm text-gray-700">Estudiantes del proyecto</label>
    <div id="lista-estudiantes" class="mt-1 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-2 space-y-1">
        @foreach ($inscripciones as $inscripcion)
            <label class="estudiante-opcion flex items-center gap-2 p-2 rounded hover:bg-gray-50" data-proyecto="{{ $inscripcion->proyecto_vinculacion_id }}" data-nombre="{{ strtolower($inscripcion->estudiante->name ?? '') }}" data-cedula="{{ strtolower($inscripcion->estudiante->cedula ?? '') }}">
                <input type="checkbox" name="inscripcion_ids[]" value="{{ $inscripcion->id }}" data-proyecto="{{ $inscripcion->proyecto_vinculacion_id }}" @checked(in_array($inscripcion->id, old('inscripcion_ids', $actividad?->inscripciones?->pluck('id')->all() ?? []))) class="estudiante-checkbox rounded border-gray-300 text-green-800">
                <span>{{ $inscripcion->estudiante->name ?? 'Sin estudiante' }} <span class="text-xs text-gray-500">— {{ $inscripcion->proyecto->nombre ?? 'Sin proyecto' }}</span></span>
            </label>
        @endforeach
        <p id="sin-estudiantes" class="p-2 text-sm text-gray-500">Todavía no hay estudiantes seleccionados.</p>
    </div>
    <p class="mt-1 text-xs text-gray-500">Selecciona los estudiantes inscritos que participarán. La actividad pertenece al proyecto.</p>
    @error('inscripcion_ids') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    @error('inscripcion_ids.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
    <input id="horas" type="number" step="0.5" min="0.5" max="24" name="horas" value="{{ old('horas', $actividad?->horas) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
    const opciones = document.querySelectorAll('.estudiante-opcion');
    const docentes = document.querySelectorAll('.docente-dato');
    let activoDocente = -1;
    let activoEstudiante = -1;

    function normalizar(texto) {
        return texto.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function actualizarEstudiantes() {
        const proyectoId = proyecto.value;
        let visibles = 0;

        opciones.forEach(function (opcion) {
            const coincideProyecto = proyectoId && opcion.dataset.proyecto === proyectoId;
            const seleccionado = opcion.querySelector('input').checked;
            opcion.classList.toggle('hidden', !coincideProyecto || !seleccionado);
            if (!coincideProyecto) {
                opcion.querySelector('input').checked = false;
            }
            if (coincideProyecto && seleccionado) {
                visibles++;
            }
        });

        document.getElementById('sin-estudiantes').classList.toggle('hidden', visibles > 0);
        filtrarEstudiantes();
    }

    function filtrarDocentes() {
        const termino = normalizar(buscarDocente.value.trim());
        activoDocente = -1;
        resultadosDocente.innerHTML = '';

        if (!termino) {
            resultadosDocente.classList.add('hidden');
            return;
        }

        let coincidencias = 0;
        docentes.forEach(function (docente) {
            const nombre = normalizar(docente.dataset.nombre);
            const cedula = normalizar(docente.dataset.cedula);
            if (!nombre.includes(termino) && !cedula.includes(termino)) return;

            coincidencias++;
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'block w-full text-left px-3 py-3 text-sm text-gray-700 hover:bg-green-50 focus:bg-green-50 focus:outline-none';
            boton.setAttribute('role', 'option');
            boton.style.borderLeft = '3px solid transparent';
            boton.textContent = docente.textContent.trim() + (docente.dataset.cedula ? ' - ' + docente.dataset.cedula : '');
            boton.addEventListener('click', function () {
                docenteId.value = docente.dataset.id;
                docenteSeleccionado.textContent = docente.textContent.trim();
                buscarDocente.value = docente.textContent.trim();
                boton.style.backgroundColor = '#ECFDF5';
                boton.style.color = '#065F46';
                boton.style.borderLeftColor = '#006B47';
                resultadosDocente.classList.add('hidden');
            });
            resultadosDocente.appendChild(boton);
        });

        if (!coincidencias) {
            resultadosDocente.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500">No se encontró ningún docente.</p>';
        }
        resultadosDocente.classList.remove('hidden');
    }

    function filtrarEstudiantes() {
        const proyectoId = proyecto.value;
        const termino = normalizar(buscador.value.trim());
        activoEstudiante = -1;
        resultadosEstudiante.innerHTML = '';

        if (!termino) {
            resultadosEstudiante.classList.add('hidden');
            return;
        }

        let coincidencias = 0;
        opciones.forEach(function (opcion) {
            const nombre = normalizar(opcion.dataset.nombre);
            const cedula = normalizar(opcion.dataset.cedula);
            if ((proyectoId && opcion.dataset.proyecto !== proyectoId) || (!nombre.includes(termino) && !cedula.includes(termino))) return;

            coincidencias++;
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'block w-full text-left px-3 py-3 text-sm text-gray-700 hover:bg-green-50 focus:bg-green-50 focus:outline-none';
            boton.setAttribute('role', 'option');
            boton.style.borderLeft = '3px solid transparent';
            boton.textContent = opcion.querySelector('span').textContent.trim() + (opcion.dataset.cedula ? ' - ' + opcion.dataset.cedula : '');
            boton.addEventListener('click', function () {
                if (proyecto.value !== opcion.dataset.proyecto) {
                    proyecto.value = opcion.dataset.proyecto;
                    actualizarEstudiantes();
                }
                opcion.querySelector('input').checked = true;
                actualizarEstudiantes();
                buscador.value = opcion.querySelector('span').textContent.trim();
                opcion.classList.add('bg-green-50');
                boton.style.backgroundColor = '#ECFDF5';
                boton.style.color = '#065F46';
                boton.style.borderLeftColor = '#006B47';
                resultadosEstudiante.classList.add('hidden');
            });
            resultadosEstudiante.appendChild(boton);
        });

        if (!coincidencias) {
            resultadosEstudiante.innerHTML = '<p class="px-3 py-2 text-sm text-gray-500">No se encontró ningún estudiante.</p>';
        }
        resultadosEstudiante.classList.remove('hidden');
    }

    function pintarResultadoActivo(resultados, indice) {
        const botones = Array.from(resultados.querySelectorAll('button'));

        botones.forEach(function (boton, posicion) {
            const seleccionado = posicion === indice;
            boton.style.backgroundColor = seleccionado ? '#ECFDF5' : '#FFFFFF';
            boton.style.color = seleccionado ? '#065F46' : '#374151';
            boton.style.borderLeftColor = seleccionado ? '#006B47' : 'transparent';
            boton.setAttribute('aria-selected', seleccionado ? 'true' : 'false');
        });

        if (indice >= 0 && botones[indice]) {
            botones[indice].scrollIntoView({ block: 'nearest' });
        }
    }

    function navegarResultados(evento, resultados, buscadorActual, tipo) {
        if (resultados.classList.contains('hidden')) return;

        const botones = Array.from(resultados.querySelectorAll('button'));
        if (!botones.length) return;

        let activo = tipo === 'docente' ? activoDocente : activoEstudiante;

        if (evento.key === 'ArrowDown') {
            evento.preventDefault();
            activo = Math.min(activo + 1, botones.length - 1);
        } else if (evento.key === 'ArrowUp') {
            evento.preventDefault();
            activo = activo <= 0 ? -1 : activo - 1;
            if (activo === -1) {
                pintarResultadoActivo(resultados, -1);
                buscadorActual.focus();
                if (tipo === 'docente') activoDocente = -1;
                else activoEstudiante = -1;
                return;
            }
        } else if (evento.key === 'Enter' && activo < 0) {
            evento.preventDefault();
            botones[0].click();
        } else if (evento.key === 'Enter' && activo >= 0) {
            evento.preventDefault();
            botones[activo].click();
        } else if (evento.key === 'Escape') {
            evento.preventDefault();
            resultados.classList.add('hidden');
        }

        if (tipo === 'docente') activoDocente = activo;
        else activoEstudiante = activo;
        pintarResultadoActivo(resultados, activo);
    }

    proyecto.addEventListener('change', actualizarEstudiantes);
    buscador.addEventListener('input', filtrarEstudiantes);
    buscarDocente.addEventListener('input', filtrarDocentes);
    buscarDocente.addEventListener('keydown', function (evento) {
        navegarResultados(evento, resultadosDocente, buscarDocente, 'docente');
    });
    buscador.addEventListener('keydown', function (evento) {
        navegarResultados(evento, resultadosEstudiante, buscador, 'estudiante');
    });

    document.addEventListener('mousedown', function (evento) {
        const dentroDeDocente = buscarDocente.contains(evento.target) || resultadosDocente.contains(evento.target);
        const dentroDeEstudiante = buscador.contains(evento.target) || resultadosEstudiante.contains(evento.target);

        if (!dentroDeDocente) resultadosDocente.classList.add('hidden');
        if (!dentroDeEstudiante) resultadosEstudiante.classList.add('hidden');
    });

    opciones.forEach(function (opcion) {
        opcion.querySelector('input').addEventListener('change', actualizarEstudiantes);
    });

    actualizarEstudiantes();
});
</script>
