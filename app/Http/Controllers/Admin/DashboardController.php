<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use App\Models\ProyectoVinculacion;
use App\Models\Inscripcion;
use App\Models\Actividad;
use App\Models\RegistroHora;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return $this->dashboardAdministrador();
        } elseif ($user->role === 'estudiante') {
                return $this->dashboardEstudiante();
        } elseif ($user->role === 'docente') {
            return $this->dashboardDocente();
        }

        // Rol sin panel asignado todavía (ej. 'coordinador'): mostramos un
        // aviso en vez de redirigir, para no caer en un bucle de redirects.
        abort(403, 'Tu cuenta (rol "' . $user->role . '") todavía no tiene un panel asignado. Contacta a un administrador.');
    }

    private function dashboardAdministrador()
    {
        $totalPeriodos = PeriodoAcademico::count();
        $totalProyectos = ProyectoVinculacion::count();
        $totalInscripciones = Inscripcion::count();
        $totalActividades = Actividad::count();
        $actividadesPendientes = Actividad::where('estado', 'pendiente')->count();
        $horasTotales = Actividad::where('estado', 'aprobada')->sum('horas');

        $ultimasActividades = Actividad::with(['inscripcion.estudiante', 'inscripcion.proyecto'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $estudiantesActivos = Inscripcion::withCount(['actividades' => function ($query) {
            $query->where('estado', 'aprobada');
        }])
            ->with('estudiante')
            ->orderByDesc('actividades_count')
            ->limit(5)
            ->get();

        $proyectosActivos = ProyectoVinculacion::with('inscripciones.actividades')
            ->limit(5)
            ->get()
            ->map(function ($proyecto) {
                $proyecto->actividades_aprobadas = $proyecto->inscripciones->flatMap(function ($insc) {
                    return $insc->actividades->where('estado', 'aprobada');
                })->count();
                return $proyecto;
            })
            ->sortByDesc('actividades_aprobadas')
            ->values();

        return view('admin.dashboard.dashboard', compact(
            'totalPeriodos',
            'totalProyectos',
            'totalInscripciones',
            'totalActividades',
            'actividadesPendientes',
            'horasTotales',
            'ultimasActividades',
            'estudiantesActivos',
            'proyectosActivos'
        ));
    }

    private function dashboardEstudiante()
{
    $user = Auth::user();

    $inscripciones = Inscripcion::where('estudiante_id', $user->id)
        ->with(['proyecto.docente', 'postulacionesActividad.actividad.carrera'])
        ->get();

    $certificadosEstudiante = \App\Models\CertificadoEstudiante::whereIn('inscripcion_id', $inscripciones->pluck('id'))
        ->with(['inscripcion.proyecto', 'tipoCertificado'])
        ->latest('updated_at')
        ->get();

    $inscripcionIds = $inscripciones->pluck('proyecto_vinculacion_id')->toArray();
    $proyectosDisponibles = ProyectoVinculacion::whereNotIn('id', $inscripcionIds)
        ->where('estado', 'aprobado')
        ->with('docente')
        ->get();

    $totalTipos = \App\Models\TipoCertificado::where('activo', true)->count();
    $certificadosAprobados = $certificadosEstudiante->where('estado', 'aprobado')->count();
    $certificadosPendientes = $certificadosEstudiante->where('estado', 'pendiente')->count();
    $certificadosRechazados = $certificadosEstudiante->where('estado', 'rechazado')->count();

    $ultimosCertificados = $certificadosEstudiante->take(5);

    // Actividades de Vinculación: la que está aprobada (la que va a desarrollar)
    // y todas las postulaciones aprobadas por si tiene más de una inscripción.
    $todasPostulaciones = $inscripciones->flatMap->postulacionesActividad;
    $actividadesAprobadas = $todasPostulaciones->where('estado', 'aprobada');
    $actividadPendiente = $todasPostulaciones->where('estado', 'pendiente')->first();

    return view('admin.estudiante.dashboard', compact(
        'inscripciones',
        'totalTipos',
        'certificadosAprobados',
        'certificadosPendientes',
        'certificadosRechazados',
        'ultimosCertificados',
        'proyectosDisponibles',
        'actividadesAprobadas',
        'actividadPendiente'
    ));
}

    private function dashboardDocente()
    {
        $user = Auth::user();

        // Las aprobaciones del docente ahora ocurren sobre CertificadoEstudiante
        // (ver DocenteCertificadoController::index/show/aprobar/rechazar), ya
        // no sobre RegistroHora. Este dashboard tiene que reflejar lo mismo.
        $certificadosPendientes = \App\Models\CertificadoEstudiante::where('estado', 'pendiente')
            ->whereHas('inscripcion.proyecto', function ($query) use ($user) {
                $query->where('docente_id', $user->id);
            })
            ->with(['inscripcion.estudiante', 'inscripcion.proyecto', 'tipoCertificado'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        $certificadosAprobados = \App\Models\CertificadoEstudiante::where('estado', 'aprobado')
            ->whereHas('inscripcion.proyecto', function ($query) use ($user) {
                $query->where('docente_id', $user->id);
            })
            ->count();

        $estudiantesAsignados = Inscripcion::whereHas('proyecto', function ($query) use ($user) {
            $query->where('docente_id', $user->id);
        })
            ->with('estudiante')
            ->distinct('estudiante_id')
            ->get();

        $proyectos = ProyectoVinculacion::where('docente_id', $user->id)
            ->withCount('inscripciones')
            ->get();

        $postulacionesActividadPendientes = \App\Models\PostulacionActividad::where('estado', 'pendiente')
            ->whereHas('inscripcion.proyecto', function ($query) use ($user) {
                $query->where('docente_id', $user->id);
            })
            ->count();

        return view('admin.docente.dashboard', compact(
            'certificadosPendientes',
            'certificadosAprobados',
            'estudiantesAsignados',
            'proyectos',
            'postulacionesActividadPendientes'
        ));
    }
}