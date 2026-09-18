<?php

namespace App\Http\Controllers;

use App\Models\PostulacionActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocentePostulacionController extends Controller
{
    // Docente: ver las postulaciones pendientes de sus propios estudiantes
    public function index()
    {
        $docenteId = Auth::id();

        $postulaciones = PostulacionActividad::whereHas('inscripcion.proyecto', function ($query) use ($docenteId) {
                $query->where('docente_id', $docenteId);
            })
            ->with(['inscripcion.estudiante', 'inscripcion.proyecto', 'actividad.carrera'])
            ->orderByRaw("FIELD(estado, 'pendiente', 'aprobada', 'rechazada')")
            ->latest('created_at')
            ->get();

        return view('docente.postulaciones-actividad', [
            'postulaciones' => $postulaciones,
        ]);
    }

    // Docente: aprobar
    public function aprobar(PostulacionActividad $postulacion)
    {
        $docenteId = Auth::id();

        if ($postulacion->inscripcion->proyecto->docente_id !== $docenteId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $postulacion->update([
            'estado' => 'aprobada',
            'aprobado_por' => $docenteId,
            'fecha_aprobacion' => now(),
            'observaciones_docente' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Actividad aprobada.']);
    }

    // Docente: rechazar
    public function rechazar(Request $request, PostulacionActividad $postulacion)
    {
        $docenteId = Auth::id();

        if ($postulacion->inscripcion->proyecto->docente_id !== $docenteId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'observaciones_docente' => 'required|string|min:5',
        ]);

        $postulacion->update([
            'estado' => 'rechazada',
            'aprobado_por' => $docenteId,
            'fecha_aprobacion' => now(),
            'observaciones_docente' => $request->observaciones_docente,
        ]);

        return response()->json(['success' => true, 'message' => 'Actividad rechazada. El estudiante podrá elegir otra.']);
    }
}