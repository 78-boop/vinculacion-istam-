<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Inscripcion;
use App\Models\ProyectoVinculacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        [$proyectos, $docentes, $estudiantes] = $this->formData();

        return view('admin.actividades.create', compact('proyectos', 'docentes', 'estudiantes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateActivity($request);
        $inscripcionIds = $this->inscripcionesDeEstudiantes($request);
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
        [$proyectos, $docentes, $estudiantes] = $this->formData();

        return view('admin.actividades.edit', compact('actividad', 'proyectos', 'docentes', 'estudiantes'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $validated = $this->validateActivity($request);
        $inscripcionIds = $this->inscripcionesDeEstudiantes($request);
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
            // Todos los usuarios con rol estudiante (aparecen solos al crearlos),
            // con los proyectos en los que ya están inscritos
            User::where('role', 'estudiante')
                ->with(['carrera', 'inscripciones:id,estudiante_id,proyecto_vinculacion_id'])
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validateActivity(Request $request): array
    {
        return $request->validate([
            'proyecto_vinculacion_id' => ['required', 'exists:proyecto_vinculacions,id'],
            'docente_id'             => ['required', 'exists:users,id'],
            'estudiante_ids'         => ['nullable', 'array'],
            'estudiante_ids.*'       => ['integer', Rule::exists('users', 'id')->where('role', 'estudiante')],
            'fecha_inicio'           => ['required', 'date'],
            'fecha_finalizacion'     => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'lugar'               => ['nullable', 'string', 'max:255'],
            'descripcion'         => ['required', 'string'],
            'horas'               => ['required', 'numeric', 'min:0.1', 'max:999.99'],
            'estado'              => ['required', 'in:pendiente,aprobada'],
            'comentario_docente'  => ['nullable', 'string'],
        ]);
    }

    // Devuelve la inscripción de cada estudiante en el proyecto elegido.
    // Si el estudiante aún no está inscrito, se lo inscribe automáticamente.
    private function inscripcionesDeEstudiantes(Request $request): array
    {
        $proyectoId = $request->input('proyecto_vinculacion_id');
        $estudiantes = User::where('role', 'estudiante')
            ->whereIn('id', $request->input('estudiante_ids', []))
            ->with('carrera')
            ->get();

        return $estudiantes->map(function (User $estudiante) use ($proyectoId) {
            return Inscripcion::firstOrCreate(
                ['estudiante_id' => $estudiante->id, 'proyecto_vinculacion_id' => $proyectoId],
                [
                    'fecha_inscripcion' => now()->toDateString(),
                    'horas_cumplidas' => 0,
                    'estado' => 'activo',
                    'horas_requeridas' => $estudiante->carrera->horas_requeridas ?? 90,
                ]
            )->id;
        })->all();
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}