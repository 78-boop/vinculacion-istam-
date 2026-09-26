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

        abort(403, 'Tu cuenta (rol "' . $user->role . '") todavía no tiene un panel asignado. Contacta a un administrador.');
    }

    private function dashboardAdministrador()
    {
        $totalPeriodos = PeriodoAcademico::count();
        $totalProyectos = ProyectoVinculacion::count();
        $totalInscripciones = Inscripcion::count();
        $totalActividades = Actividad::count();
        $actividadesPendientes = Actividad::where('estado', 'pendiente')->count();

        $ultimasActividades = Actividad::with(['inscripcion.estudiante', 'inscripcion.proyecto', 'proyecto', 'inscripciones.estudiante'])
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

        $carreras = \App\Models\Carrera::withCount(['estudiantes', 'actividadesVinculacion'])
            ->where('activo', true)
            ->orderByDesc('estudiantes_count')
            ->get()
            ->each(fn ($carrera) => $carrera->actividades_count = $carrera->actividades()->count());

        $periodoActivo = PeriodoAcademico::where('activo', true)->latest('fecha_inicio')->first();
        $totalUsuarios = \App\Models\User::count();

        return view('admin.dashboard.dashboard', compact(
            'carreras',
            'periodoActivo',
            'totalUsuarios',
            'totalPeriodos',
            'totalProyectos',
            'totalInscripciones',
            'totalActividades',
            'actividadesPendientes',
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

        $certificadosEstudiante = \App\Models\CertificadoEstudiante::vigentes()->whereIn('inscripcion_id', $inscripciones->pluck('id'))
            ->with(['inscripcion.proyecto', 'tipoCertificado'])
            ->latest('updated_at')
            ->get();

        // Proyectos abiertos con cuántas actividades aceptan estudiantes todavía
        $proyectosDisponibles = ProyectoVinculacion::where('estado', 'aprobado')
            ->with('docente')
            ->withCount(['actividades as actividades_disponibles_count' => fn ($q) => $q
                ->where('estado', 'aprobada')
                ->where(fn ($f) => $f->whereNull('fecha_finalizacion')->orWhereDate('fecha_finalizacion', '>=', today()))])
            ->orderBy('nombre')
            ->get();
        $proyectosInscritos = $inscripciones->pluck('proyecto_vinculacion_id')->all();

        // La única actividad en la que participa el estudiante
        $miActividad = \App\Http\Controllers\ProyectoEstudianteController::actividadDe($user);

        $totalTipos = \App\Models\TipoCertificado::where('activo', true)->count();
        $certificadosAprobados = $certificadosEstudiante->where('estado', 'aprobado')->count();
        $certificadosPendientes = $certificadosEstudiante->where('estado', 'pendiente')->count();
        $certificadosRechazados = $certificadosEstudiante->where('estado', 'rechazado')->count();

        $ultimosCertificados = $certificadosEstudiante->take(5);

        // Actividades que el administrador registró con este estudiante como participante
        $inscripcionIdsEstudiante = $inscripciones->pluck('id');
        $actividadesAsignadas = Actividad::with(['proyecto', 'docente'])
            ->where(function ($query) use ($inscripcionIdsEstudiante) {
                $query->whereHas('inscripciones', fn ($q) => $q->whereIn('inscripcions.id', $inscripcionIdsEstudiante))
                    ->orWhereIn('inscripcion_id', $inscripcionIdsEstudiante);
            })
            ->orderByDesc('fecha_inicio')
            ->get();

        $todasPostulaciones = $inscripciones->flatMap->postulacionesActividad;
        $actividadesAprobadas = $todasPostulaciones->where('estado', 'aprobada');
        $actividadPendiente = $todasPostulaciones->where('estado', 'pendiente')->first();

        return view('admin.estudiante.dashboard', compact(
            'inscripciones',
            'actividadesAsignadas',
            'miActividad',
            'proyectosInscritos',
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

        // Todo sale de lo que registra el administrador: proyectos donde es docente
        // y actividades de las que lo hizo responsable.
        $inscripcionesDocente = Inscripcion::delDocente($user->id);

        $certificadosPendientes = \App\Models\CertificadoEstudiante::vigentes()->where('estado', 'pendiente')
            ->whereIn('inscripcion_id', (clone $inscripcionesDocente)->select('id'))
            ->with(['inscripcion.estudiante', 'inscripcion.proyecto', 'tipoCertificado'])
            ->latest('created_at')
            ->paginate(10);

        $certificadosAprobados = \App\Models\CertificadoEstudiante::vigentes()->where('estado', 'aprobado')
            ->whereIn('inscripcion_id', (clone $inscripcionesDocente)->select('id'))
            ->count();

        $estudiantesAsignados = (clone $inscripcionesDocente)
            ->with('estudiante')
            ->get()
            ->unique('estudiante_id')
            ->values();

        $proyectos = ProyectoVinculacion::where(function ($q) use ($user) {
                $q->where('docente_id', $user->id)
                    ->orWhereHas('actividades', fn ($a) => $a->where('docente_id', $user->id));
            })
            ->withCount('inscripciones')
            ->get();

        $postulacionesActividadPendientes = 0; // el catálogo por carrera ya no se usa

        $actividadesDocente = Actividad::where(function ($q) use ($user) {
            $q->where('docente_id', $user->id)
                ->orWhereHas('proyecto', fn ($p) => $p->where('docente_id', $user->id));
        });

        $actividadesEnCurso = (clone $actividadesDocente)
            ->where(fn ($q) => $q->whereNull('fecha_inicio')->orWhereDate('fecha_inicio', '<=', today()))
            ->where(fn ($q) => $q->whereNull('fecha_finalizacion')->orWhereDate('fecha_finalizacion', '>=', today()))
            ->count();

        $misActividades = (clone $actividadesDocente)
            ->with('proyecto')
            ->withCount('inscripciones')
            ->orderByDesc('fecha_inicio')
            ->limit(6)
            ->get();

        return view('admin.docente.dashboard', compact(
            'misActividades',
            'actividadesEnCurso',
            'certificadosPendientes',
            'certificadosAprobados',
            'estudiantesAsignados',
            'proyectos',
            'postulacionesActividadPendientes'
        ));
    }
}