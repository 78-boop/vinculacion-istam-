<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Docente: ve las actividades que el administrador creó y en las que él participa
class DocenteActividadController extends Controller
{
    public function index(Request $request)
    {
        $docenteId = Auth::id();
        $filtro = $request->query('ver', 'todas');

        $actividades = Actividad::with(['proyecto.periodoAcademico', 'docente', 'inscripciones.estudiante.carrera'])
            ->where(function ($q) use ($docenteId) {
                $q->where('docente_id', $docenteId)
                    ->orWhereHas('proyecto', fn ($p) => $p->where('docente_id', $docenteId));
            })
            ->orderByDesc('fecha_inicio')
            ->get();

        $hoy = today();
        $estadoDe = function (Actividad $a) use ($hoy) {
            $inicio = $a->fecha_inicio ?? $a->fecha;
            $fin = $a->fecha_finalizacion ?? $inicio;
            if ($inicio && $hoy->lt($inicio)) return 'proximas';
            if ($fin && $hoy->gt($fin)) return 'finalizadas';
            return 'en_curso';
        };

        $conteos = [
            'todas' => $actividades->count(),
            'en_curso' => $actividades->filter(fn ($a) => $estadoDe($a) === 'en_curso')->count(),
            'proximas' => $actividades->filter(fn ($a) => $estadoDe($a) === 'proximas')->count(),
            'finalizadas' => $actividades->filter(fn ($a) => $estadoDe($a) === 'finalizadas')->count(),
        ];

        if ($filtro !== 'todas') {
            $actividades = $actividades->filter(fn ($a) => $estadoDe($a) === $filtro)->values();
        }

        return view('docente.actividades.index', compact('actividades', 'conteos', 'filtro', 'docenteId'));
    }
}
