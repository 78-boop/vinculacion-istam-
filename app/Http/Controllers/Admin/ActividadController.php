<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Inscripcion;
use App\Models\ProyectoVinculacion;
use App\Models\User;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::with(['proyecto', 'docente', 'inscripciones.estudiante', 'inscripcion.proyecto'])
            ->latest('fecha_inicio')
            ->get();

        return view('admin.actividades.index', compact('actividades'));
    }

    public function create()
    {
        [$proyectos, $docentes, $inscripciones] = $this->formData();

        return view('admin.actividades.create', compact('proyectos', 'docentes', 'inscripciones'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateActivity($request);
        $inscripcionIds = $this->validInscripcionIds($request);
        $validated['inscripcion_id'] = null;
        $validated['fecha'] = $validated['fecha_inicio'];

        $actividad = Actividad::create($validated);
        $actividad->inscripciones()->sync($inscripcionIds);

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(Actividad $actividad)
    {
        $actividad->load('inscripciones');
        [$proyectos, $docentes, $inscripciones] = $this->formData();

        return view('admin.actividades.edit', compact('actividad', 'proyectos', 'docentes', 'inscripciones'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $validated = $this->validateActivity($request);
        $inscripcionIds = $this->validInscripcionIds($request);
        $validated['inscripcion_id'] = $actividad->inscripcion_id;
        $validated['fecha'] = $validated['fecha_inicio'];

        $actividad->update($validated);
        $actividad->inscripciones()->sync($inscripcionIds);

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    private function formData(): array
    {
        return [
            ProyectoVinculacion::orderBy('nombre')->get(),
            User::where('role', 'docente')->orderBy('name')->get(),
            Inscripcion::with(['estudiante', 'proyecto'])->get(),
        ];
    }

    private function validateActivity(Request $request): array
    {
        return $request->validate([
            'proyecto_vinculacion_id' => ['required', 'exists:proyecto_vinculacions,id'],
            'docente_id'             => ['required', 'exists:users,id'],
            'inscripcion_ids'        => ['nullable', 'array'],
            'inscripcion_ids.*'      => ['integer', 'exists:inscripcions,id'],
            'fecha_inicio'           => ['required', 'date'],
            'fecha_finalizacion'     => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'lugar'               => ['nullable', 'string', 'max:255'],
            'descripcion'         => ['required', 'string'],
            'horas'               => ['required', 'numeric', 'min:0.1', 'max:24'],
            'estado'              => ['required', 'in:pendiente,aprobada'],
            'comentario_docente'  => ['nullable', 'string'],
        ]);
    }

    private function validInscripcionIds(Request $request): array
    {
        $ids = $request->input('inscripcion_ids', []);

        return Inscripcion::whereIn('id', $ids)
            ->where('proyecto_vinculacion_id', $request->input('proyecto_vinculacion_id'))
            ->pluck('id')
            ->all();
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}