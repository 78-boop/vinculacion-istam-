<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\CertificadoEstudiante;
use App\Models\Inscripcion;
use App\Models\TipoCertificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocenteCertificadoController extends Controller
{
    // Docente: lista de sus estudiantes con el progreso de certificados
    public function index()
    {
        $docente = Auth::user();

        $inscripciones = Inscripcion::whereHas('proyecto', function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })
            ->with(['estudiante.carrera', 'proyecto', 'certificadosEstudiante'])
            ->get();

        $totalTipos = TipoCertificado::where('activo', true)->count();

        return view('docente.certificados-index', [
            'inscripciones' => $inscripciones,
            'totalTipos' => $totalTipos,
        ]);
    }

    // Docente: registrar/actualizar las horas cumplidas del estudiante
    public function actualizarHoras(Request $request, Inscripcion $inscripcion)
    {
        $docente = Auth::user();

        if ($inscripcion->proyecto->docente_id !== $docente->id) {
            abort(403, 'No sos el tutor de este estudiante.');
        }

        $request->validate([
            'horas_cumplidas' => 'required|integer|min:0|max:1000',
        ]);

        $inscripcion->update(['horas_cumplidas' => $request->horas_cumplidas]);

        return back()->with('success', 'Horas actualizadas correctamente.');
    }

    // Docente: ver los 8 documentos de un estudiante puntual
    public function show(Inscripcion $inscripcion)
    {
        $docente = Auth::user();

        if ($inscripcion->proyecto->docente_id !== $docente->id) {
            abort(403, 'No sos el tutor de este estudiante.');
        }

        $inscripcion->load(['estudiante', 'proyecto', 'certificadosEstudiante.tipoCertificado']);

        $tiposCertificado = TipoCertificado::where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('docente.certificados-show', [
            'inscripcion' => $inscripcion,
            'tiposCertificado' => $tiposCertificado,
        ]);
    }

    // Docente: aprobar un documento
    public function aprobar(Request $request, CertificadoEstudiante $certificado)
    {
        $docente = Auth::user();
        $inscripcion = $certificado->inscripcion()->with('proyecto')->first();

        if ($inscripcion->proyecto->docente_id !== $docente->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $certificado->update([
            'estado' => 'aprobado',
            'aprobado_por' => $docente->id,
            'fecha_aprobacion' => now(),
            'observaciones_docente' => null,
        ]);

        // ¿Con este ya se completaron los 8? Si es así, generamos el
        // certificado final automático (una sola vez, no se duplica).
        $certificadoFinalCreado = false;

        if ($inscripcion->todosCertificadosAprobados()) {
            $certificadoFinal = Certificado::firstOrCreate(
                ['inscripcion_id' => $inscripcion->id],
                [
                    'fecha_generacion' => now()->format('Y-m-d'),
                    'numero_certificado' => 'CERT-' . $inscripcion->id . '-' . now()->format('YmdHis'),
                    'horas_certificadas' => $inscripcion->horas_requeridas ?? 0,
                    'estado' => 'aprobado',
                    'aprobado_por' => $docente->id,
                    'fecha_aprobacion' => now()->format('Y-m-d'),
                ]
            );
            $certificadoFinalCreado = $certificadoFinal->wasRecentlyCreated;
        }

        return response()->json([
            'success' => true,
            'message' => 'Documento aprobado.',
            'certificado_final_generado' => $certificadoFinalCreado,
        ]);
    }

    // Docente: rechazar un documento (el estudiante lo vuelve a subir)
    public function rechazar(Request $request, CertificadoEstudiante $certificado)
    {
        $docente = Auth::user();
        $inscripcion = $certificado->inscripcion()->with('proyecto')->first();

        if ($inscripcion->proyecto->docente_id !== $docente->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'observaciones_docente' => 'required|string|min:5',
        ]);

        $certificado->update([
            'estado' => 'rechazado',
            'aprobado_por' => $docente->id,
            'fecha_aprobacion' => now(),
            'observaciones_docente' => $request->observaciones_docente,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento rechazado. El estudiante verá el motivo y podrá volver a subirlo.',
        ]);
    }
}