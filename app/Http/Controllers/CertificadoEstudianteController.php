<?php

namespace App\Http\Controllers;

use App\Models\CertificadoEstudiante;
use App\Models\Inscripcion;
use App\Models\TipoCertificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificadoEstudianteController extends Controller
{
    // Estudiante: ver el panel con los 8 certificados y su estado
    public function index()
    {
        $user = Auth::user();

        // Traemos las inscripciones del estudiante junto con lo que ya subió
        $inscripciones = $user->inscripciones()
            ->with(['proyecto', 'certificadosEstudiante.tipoCertificado'])
            ->get();

        // Catálogo completo y ordenado (para pintar las 8 filas aunque no haya subido nada aún)
        $tiposCertificado = TipoCertificado::where('activo', true)
            ->orderBy('orden')
            ->get();

        return view('estudiante.certificados', [
            'inscripciones' => $inscripciones,
            'tiposCertificado' => $tiposCertificado,
        ]);
    }

    // Estudiante: subir o reemplazar el archivo de un tipo de certificado
    public function store(Request $request)
    {
        $request->validate([
            'inscripcion_id' => 'required|exists:inscripcions,id',
            'tipo_certificado_id' => 'required|exists:tipos_certificado,id',
            'archivo' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:10240', // 10 MB
            ],
        ]);

        $inscripcion = Inscripcion::find($request->inscripcion_id);

        // Verificar que el estudiante sea el dueño de la inscripción
        if ($inscripcion->estudiante_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $file = $request->file('archivo');
        $path = $file->store('certificados_estudiante/' . $inscripcion->id, 'public');

        // updateOrCreate: si ya había subido este documento antes (por ejemplo
        // porque el docente lo rechazó), se reemplaza la misma fila y vuelve
        // a quedar "pendiente" para que el docente lo revise de nuevo.
        $certificado = CertificadoEstudiante::updateOrCreate(
            [
                'inscripcion_id' => $inscripcion->id,
                'tipo_certificado_id' => $request->tipo_certificado_id,
            ],
            [
                'ruta_archivo' => $path,
                'nombre_archivo_original' => $file->getClientOriginalName(),
                'estado' => 'pendiente',
                'observaciones_docente' => null,
                'aprobado_por' => null,
                'fecha_aprobacion' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Documento subido correctamente, queda pendiente de revisión.',
            'certificado' => $certificado->load('tipoCertificado'),
        ]);
    }

    // Estudiante: descargar/ver el archivo que subió
    public function descargar(CertificadoEstudiante $certificado)
    {
        if ($certificado->inscripcion->estudiante_id !== Auth::id() && Auth::user()->role !== 'docente') {
            abort(403);
        }

        if (!Storage::disk('public')->exists($certificado->ruta_archivo)) {
            abort(404, 'El archivo ya no está disponible.');
        }

        return Storage::disk('public')->response(
            $certificado->ruta_archivo,
            $certificado->nombre_archivo_original
        );
    }
}