<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Inscripcion;
use App\Models\ProyectoVinculacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

// Estudiante: ver proyectos abiertos, sus actividades y escoger UNA actividad
class ProyectoEstudianteController extends Controller
{
    public function index()
    {
        $estudiante = Auth::user();

        $proyectos = ProyectoVinculacion::where('estado', 'aprobado')
            ->with(['docente', 'periodoAcademico'])
            ->withCount(['actividades as actividades_disponibles_count' => fn ($q) => $this->soloDisponibles($q)])
            ->orderBy('nombre')
            ->get();

        $inscritoEn = $estudiante->inscripciones()->pluck('proyecto_vinculacion_id')->all();
        $miActividad = self::actividadDe($estudiante);
        $puedeOtra = (bool) $estudiante->permitir_nueva_actividad;

        return view('estudiante.proyectos.index', compact('proyectos', 'inscritoEn', 'miActividad', 'puedeOtra'));
    }

    public function show(ProyectoVinculacion $proyecto)
    {
        abort_unless($proyecto->estado === 'aprobado', 404);

        $estudiante = Auth::user();
        $proyecto->load(['docente', 'periodoAcademico']);

        $actividades = $proyecto->actividades()
            ->with('docente')
            ->withCount('inscripciones')
            ->tap(fn ($q) => $this->soloDisponibles($q))
            ->orderBy('fecha_inicio')
            ->get();

        $miActividad = self::actividadDe($estudiante);
        $misActividadIds = self::actividadesDe($estudiante)->pluck('id')->all();
        $puedeOtra = (bool) $estudiante->permitir_nueva_actividad;

        return view('estudiante.proyectos.show', compact('proyecto', 'actividades', 'miActividad', 'misActividadIds', 'puedeOtra'));
    }

    public function inscribirse(Actividad $actividad)
    {
        $estudiante = Auth::user();

        // Ya participa en esta misma actividad
        if (self::actividadesDe($estudiante)->contains('id', $actividad->id)) {
            return back()
                ->with('aviso_titulo', 'Ya estás inscrito')
                ->with('warning', 'Ya participas en esta actividad.');
        }

        // Regla: solo UNA actividad, salvo que el administrador le haya dado permiso para otra
        $actual = self::actividadDe($estudiante);
        if ($actual && ! $estudiante->permitir_nueva_actividad) {
            return back()
                ->with('aviso_titulo', 'Inscripción denegada')
                ->with('error', 'Ya estás inscrito en la actividad de "' . ($actual->proyecto->nombre ?? 'un proyecto')
                    . '". Para inscribirte en otra, el administrador debe darte permiso.');
        }

        $proyecto = $actividad->proyecto;
        $disponible = $proyecto && $proyecto->estado === 'aprobado'
            && $actividad->estado === 'aprobada'
            && (! $actividad->fecha_finalizacion || $actividad->fecha_finalizacion->gte(today()));

        if (! $disponible) {
            return back()
                ->with('aviso_titulo', 'Inscripción denegada')
                ->with('error', 'Esta actividad ya no está disponible para inscripciones.');
        }

        // Si aún no está inscrito en el proyecto, se lo inscribe automáticamente
        $inscripcion = Inscripcion::firstOrCreate(
            ['estudiante_id' => $estudiante->id, 'proyecto_vinculacion_id' => $proyecto->id],
            [
                'fecha_inscripcion' => now()->toDateString(),
                'horas_cumplidas' => 0,
                'estado' => 'activo',
                'horas_requeridas' => $estudiante->carrera->horas_requeridas ?? 90,
            ]
        );

        $actividad->inscripciones()->syncWithoutDetaching([$inscripcion->id]);

        // El permiso para otra actividad se usa una sola vez
        if ($actual && $estudiante->permitir_nueva_actividad) {
            $estudiante->forceFill(['permitir_nueva_actividad' => false])->save();
        }

        return redirect()->route('dashboard')
            ->with('success', '¡Listo! Quedaste inscrito en la actividad de "' . $proyecto->nombre . '".');
    }

    // Actividades que todavía aceptan estudiantes: aprobadas y sin terminar
    private function soloDisponibles(Builder $query): Builder
    {
        return $query->where('estado', 'aprobada')
            ->where(fn ($q) => $q->whereNull('fecha_finalizacion')->orWhereDate('fecha_finalizacion', '>=', today()));
    }

    // Todas las actividades en las que participa el estudiante (la más reciente primero)
    public static function actividadesDe(User $estudiante)
    {
        $inscripcionIds = $estudiante->inscripciones()->pluck('id');

        return Actividad::with(['proyecto', 'docente'])
            ->where(function ($q) use ($inscripcionIds) {
                $q->whereHas('inscripciones', fn ($i) => $i->whereIn('inscripcions.id', $inscripcionIds))
                    ->orWhereIn('inscripcion_id', $inscripcionIds);
            })
            ->latest('fecha_inicio')
            ->get();
    }

    // La actividad más reciente del estudiante (o null)
    public static function actividadDe(User $estudiante): ?Actividad
    {
        return self::actividadesDe($estudiante)->first();
    }
}
