<?php

namespace App\Http\Controllers;

use App\Models\ActividadVinculacion;
use App\Models\Inscripcion;
use App\Models\PostulacionActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostulacionActividadController extends Controller
{
    // Estudiante: ver actividades de su carrera y elegir una
    public function index()
    {
        $user = Auth::user();

        $inscripciones = $user->inscripciones()
            ->with(['proyecto', 'postulacionesActividad.actividad'])
            ->get();

        $actividadesDisponibles = collect();
        if ($user->carrera_id) {
            $actividadesDisponibles = ActividadVinculacion::where('carrera_id', $user->carrera_id)
                ->where('activo', true)
                ->with('creador')
                ->latest()
                ->get();
        }

        return view('estudiante.actividades-vinculacion', [
            'inscripciones' => $inscripciones,
            'actividadesDisponibles' => $actividadesDisponibles,
            'tieneCarrera' => (bool) $user->carrera_id,
        ]);
    }

    // Estudiante: seleccionar una actividad del catálogo de su carrera
    public function postular(Request $request)
    {
        $request->validate([
            'actividad_vinculacion_id' => 'required|exists:actividades_vinculacion,id',
            'inscripcion_id' => 'required|exists:inscripcions,id',
        ]);

        $inscripcion = Inscripcion::findOrFail($request->inscripcion_id);

        if ($inscripcion->estudiante_id !== Auth::id()) {
            return back()->with('error', 'No autorizado.');
        }

        // Regla: solo puede tener UNA actividad activa (pendiente o aprobada) a la vez
        $yaTiene = $inscripcion->postulacionesActividad()
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->exists();

        if ($yaTiene) {
            return back()->with('error', 'Ya tenés una actividad seleccionada en espera o aprobada. Esperá la respuesta del docente antes de elegir otra.');
        }

        PostulacionActividad::create([
            'actividad_vinculacion_id' => $request->actividad_vinculacion_id,
            'inscripcion_id' => $inscripcion->id,
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Elegiste la actividad. Queda pendiente de aprobación de tu docente.');
    }
}