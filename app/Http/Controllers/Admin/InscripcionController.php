<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\ProyectoVinculacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function index()
    {
        $inscripciones = Inscripcion::with(['estudiante', 'proyecto'])
            ->orderByDesc('fecha_inscripcion')
            ->get();

        return view('admin.inscripciones.index', compact('inscripciones'));
    }

    public function create()
    {
        $estudiantes = User::where('role', 'estudiante')->get();
        $proyectos = ProyectoVinculacion::orderByDesc('created_at')->get();

        return view('admin.inscripciones.create', compact('estudiantes', 'proyectos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Accept either proyecto_id or proyecto_vinculacion_id (for student self-enrollment)
        $proyectoId = $request->input('proyecto_vinculacion_id') ?? $request->input('proyecto_id');

        // Basic validation
        if (!$proyectoId) {
            return redirect()->route('dashboard')->with('error', 'Proyecto no especificado.');
        }

        // Verify the project exists
        $proyecto = ProyectoVinculacion::findOrFail($proyectoId);

        // If user is admin, allow full form submission
        if ($user->role === 'admin') {
            $validated = $request->validate([
                'estudiante_id' => 'required|exists:users,id',
                'proyecto_vinculacion_id' => 'required|exists:proyecto_vinculacions,id',
                'fecha_inscripcion' => 'required|date',
                'horas_cumplidas' => 'required|integer|min:0',
                'estado' => 'required|in:activo,completado,retirado',
            ]);

            Inscripcion::create($validated);
            return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción registrada correctamente.');
        }

        // Students can only enroll in projects the admin has already approved
        if ($proyecto->estado !== 'aprobado') {
            return redirect()->route('dashboard')->with('error', 'Ese proyecto todavía no ha sido aprobado por un administrador.');
        }

        // For students, check if already enrolled
        $existingInscripcion = Inscripcion::where('estudiante_id', $user->id)
            ->where('proyecto_vinculacion_id', $proyectoId)
            ->first();

        if ($existingInscripcion) {
            return redirect()->route('dashboard')->with('error', 'Ya estás inscrito en este proyecto.');
        }

        // Create inscription with default values for student
        Inscripcion::create([
            'estudiante_id' => $user->id,
            'proyecto_vinculacion_id' => $proyectoId,
            'fecha_inscripcion' => now()->toDateString(),
            'horas_cumplidas' => 0,
            'estado' => 'activo',
        ]);

        return redirect()->route('dashboard')->with('success', 'Inscripción registrada correctamente.');
    }

    public function edit(Inscripcion $inscripcione)
    {
        $estudiantes = User::where('role', 'estudiante')->get();
        $proyectos = ProyectoVinculacion::orderByDesc('created_at')->get();

        return view('admin.inscripciones.edit', compact('inscripcione', 'estudiantes', 'proyectos'));
    }

    public function update(Request $request, Inscripcion $inscripcione)
    {
        $validated = $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'proyecto_vinculacion_id' => 'required|exists:proyecto_vinculacions,id',
            'fecha_inscripcion' => 'required|date',
            'horas_cumplidas' => 'required|integer|min:0',
            'estado' => 'required|in:activo,completado,retirado',
        ]);

        $inscripcione->update($validated);

        return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción actualizada.');
    }

    public function destroy(Inscripcion $inscripcione)
    {
        $inscripcione->delete();
        return redirect()->route('admin.inscripciones.index')->with('success', 'Inscripción eliminada.');
    }
}