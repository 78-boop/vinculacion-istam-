<?php

namespace App\Http\Controllers;

use App\Models\RegistroHora;
use App\Models\Inscripcion;
use App\Models\ActividadEstudiante;
use App\Models\Evidencia;
use App\Models\Certificado;
use App\Models\ObservacionDocente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PDF;

class RegistroHorasController extends Controller
{
    // Estudiante: Ver panel de registro de horas
    public function index()
    {
        $user = Auth::user();

        // Obtener inscripciones del estudiante
        $inscripciones = $user->inscripciones()
            ->with(['registrosHoras' => function($query) {
                $query->orderBy('fecha', 'desc');
            }])
            ->get();

        return view('estudiante.registro-horas', [
            'inscripciones' => $inscripciones
        ]);
    }

    // Estudiante: Crear nuevo registro de horas
    public function store(Request $request)
    {
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripcions,id',
            'fecha' => 'required|date',
            'horas_registradas' => 'required|numeric|between:0.5,8',
            'descripcion' => 'required|string|min:10',
            'evidencias.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $inscripcion = Inscripcion::find($request->inscripcion_id);

        // Verificar que el estudiante sea el propietario de la inscripción
        if ($inscripcion->estudiante_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Crear registro de horas
        try {
            $registroHora = RegistroHora::create([
                'inscripcion_id' => $request->inscripcion_id,
                'fecha' => $request->fecha,
                'horas_registradas' => $request->horas_registradas,
                'descripcion' => $request->descripcion,
                'estado' => 'pendiente'
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return response()->json([
                'error' => 'Ya registraste horas para esa fecha. Elige otro día.'
            ], 422);
        }

        // Procesar evidencias (imágenes)
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $path = $file->store('evidencias/' . $registroHora->id, 'public');

                Evidencia::create([
                    'registro_hora_id' => $registroHora->id,
                    'actividad_estudiante_id' => null,
                    'tipo_archivo' => 'imagen',
                    'nombre_archivo' => $file->getClientOriginalName(),
                    'ruta_archivo' => $path,
                    'tamaño_bytes' => $file->getSize()
                ]);
            }
        }

        // Verificar si se alcanzaron las horas requeridas
        $horasRegistradas = $inscripcion->registrosHoras()
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->sum('horas_registradas');

        return response()->json([
            'success' => true,
            'message' => 'Horas registradas exitosamente',
            'data' => [
                'registro_id' => $registroHora->id,
                'horas_registradas' => $horasRegistradas,
                'horas_requeridas' => $inscripcion->horas_requeridas,
                'completado' => $horasRegistradas >= $inscripcion->horas_requeridas
            ]
        ]);
    }

    // Obtener estado actual del estudiante
    public function getEstado($inscripcionId)
    {
        $inscripcion = Inscripcion::with('registrosHoras')
            ->find($inscripcionId);

        if (!$inscripcion || $inscripcion->estudiante_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $horasRegistradas = $inscripcion->registrosHoras()
            ->sum('horas_registradas');

        $horasAprobadas = $inscripcion->registrosHoras()
            ->where('estado', 'aprobada')
            ->sum('horas_registradas');

        $certificado = Certificado::where('inscripcion_id', $inscripcionId)->first();

        $estado = 'pendiente';
        if ($horasRegistradas >= $inscripcion->horas_requeridas) {
            $estado = 'pendiente_aprobacion';
        }
        if ($horasAprobadas >= $inscripcion->horas_requeridas) {
            $estado = 'aprobado';
        }

        return response()->json([
            'estado' => $estado,
            'horas_registradas' => $horasRegistradas,
            'horas_aprobadas' => $horasAprobadas,
            'horas_requeridas' => $inscripcion->horas_requeridas,
            'puede_solicitar_certificado' => $horasAprobadas >= $inscripcion->horas_requeridas && !$certificado,
            'certificado_aprobado' => $certificado && $certificado->estado === 'aprobado'
        ]);
    }

    // Estudiante: Solicitar certificado
    public function solicitarCertificado(Request $request)
    {
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripcions,id'
        ]);

        $inscripcion = Inscripcion::with('registrosHoras')->find($request->inscripcion_id);

        if ($inscripcion->estudiante_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $horasAprobadas = $inscripcion->registrosHoras()
            ->where('estado', 'aprobada')
            ->sum('horas_registradas');

        if ($horasAprobadas < $inscripcion->horas_requeridas) {
            return response()->json([
                'error' => 'No tienes suficientes horas aprobadas para solicitar el certificado'
            ], 422);
        }

        // Crear certificado
        $certificado = Certificado::create([
            'inscripcion_id' => $inscripcion->id,
            'fecha_generacion' => now()->format('Y-m-d'),
            'numero_certificado' => 'CERT-' . $inscripcion->id . '-' . date('YmdHis'),
            'horas_certificadas' => $horasAprobadas,
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de certificado enviada',
            'certificado_id' => $certificado->id
        ]);
    }

    // Docente: Ver panel de aprobaciones
    public function aprobaciones()
    {
        $user = Auth::user();

        if ($user->role !== 'docente') {
            abort(403);
        }

        // Obtener todos los registros de horas pendientes de aprobación
        $registrosPendientes = RegistroHora::with([
            'inscripcion' => function($query) {
                $query->with('estudiante', 'proyecto', 'registrosHoras');
            },
            'evidencias'
        ])
        ->where('estado', 'pendiente')
        ->orderBy('created_at', 'desc')
        ->get();

        // Agrupar por inscripción
        $agrupados = $registrosPendientes->groupBy('inscripcion_id');

        return view('docente.aprobaciones', [
            'registrosPendientes' => $registrosPendientes,
            'agrupados' => $agrupados
        ]);
    }

    // Docente: Aprobar horas
    public function aprobar(Request $request, $registroHoraId)
    {
        $user = Auth::user();

        if ($user->role !== 'docente') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'observaciones' => 'nullable|string|max:500'
        ]);

        $registroHora = RegistroHora::find($registroHoraId);

        if (!$registroHora) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        // Actualizar estado del registro
        $registroHora->update([
            'estado' => 'aprobada',
            'aprobado_por' => $user->id,
            'observaciones' => $request->observaciones
        ]);

        // Crear observación del docente
        ObservacionDocente::create([
            'registro_hora_id' => $registroHora->id,
            'docente_id' => $user->id,
            'observacion' => $request->observaciones ?? 'Horas aprobadas',
            'aprobacion' => 'aprobado'
        ]);

        // Verificar si ya se completaron todas las horas
        $inscripcion = $registroHora->inscripcion;
        $horasAprobadas = $inscripcion->registrosHoras()
            ->where('estado', 'aprobada')
            ->sum('horas_registradas');

        return response()->json([
            'success' => true,
            'message' => 'Horas aprobadas exitosamente',
            'completado' => $horasAprobadas >= $inscripcion->horas_requeridas
        ]);
    }

    // Docente: Rechazar horas
    public function rechazar(Request $request, $registroHoraId)
    {
        $user = Auth::user();

        if ($user->role !== 'docente') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'observaciones' => 'required|string|min:10'
        ]);

        $registroHora = RegistroHora::find($registroHoraId);

        if (!$registroHora) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        $registroHora->update([
            'estado' => 'rechazada',
            'aprobado_por' => $user->id,
            'observaciones' => $request->observaciones
        ]);

        ObservacionDocente::create([
            'registro_hora_id' => $registroHora->id,
            'docente_id' => $user->id,
            'observacion' => $request->observaciones,
            'aprobacion' => 'rechazado'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de horas rechazado'
        ]);
    }

    // Docente: Aprobar certificado
    public function aprobarCertificado(Request $request, $certificadoId)
    {
        $user = Auth::user();

        if ($user->role !== 'docente') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $certificado = Certificado::find($certificadoId);

        if (!$certificado) {
            return response()->json(['error' => 'Certificado no encontrado'], 404);
        }

        $certificado->update([
            'estado' => 'aprobado',
            'aprobado_por' => $user->id,
            'fecha_aprobacion' => now()->format('Y-m-d')
        ]);

        // Generar PDF del certificado
        // $this->generarPdfCertificado($certificado);

        return response()->json([
            'success' => true,
            'message' => 'Certificado aprobado exitosamente'
        ]);
    }

    // Descargar certificado
    public function descargarCertificado($certificadoId)
    {
        $certificado = Certificado::with('inscripcion.user')
            ->find($certificadoId);

        if (!$certificado || $certificado->estado !== 'aprobado') {
            abort(404);
        }

        $inscripcion = $certificado->inscripcion;
        if ($inscripcion->estudiante_id !== Auth::id() && Auth::user()->role !== 'docente') {
            abort(403);
        }

        // Aquí irá la lógica para generar/descargar el PDF
        return response()->json([
            'message' => 'PDF de certificado lista para descargar',
            'certificado_numero' => $certificado->numero_certificado
        ]);
    }
}
