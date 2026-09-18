<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificadoAdministrativo;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificadoAdministrativoController extends Controller
{
    // Admin: lista de estudiantes que ya completaron sus 8 documentos
    public function index()
    {
        $inscripciones = Inscripcion::with(['estudiante', 'proyecto', 'certificadoAdministrativo'])
            ->get()
            ->filter(function ($inscripcion) {
                return $inscripcion->todosCertificadosAprobados();
            });

        return view('admin.certificados.index', [
            'inscripciones' => $inscripciones,
        ]);
    }

    // Admin: generar el PDF autocompletado
    public function generar(Inscripcion $inscripcion)
    {
        if (!$inscripcion->todosCertificadosAprobados()) {
            return redirect()->route('admin.certificados.index')
                ->with('error', 'Este estudiante todavía no tiene los 8 documentos aprobados.');
        }

        $estudiante = $inscripcion->estudiante;

        if (empty($estudiante->cedula)) {
            return redirect()->route('admin.usuarios.edit', $estudiante->id)
                ->with('error', 'Este estudiante no tiene cédula registrada. Completala para poder generar el certificado.');
        }

        $admin = Auth::user();
        $numeroCertificado = 'ADM-' . $inscripcion->id . '-' . now()->format('YmdHis');

        // El "Gestor de Vinculación" es un cargo institucional fijo, no cambia
        // según qué cuenta admin esté logueada. Si el gestor cambia algún día,
        // se actualiza acá en un solo lugar.
        $gestorNombre = 'Ing. Juan Carlos Guarinda Castillo';

        $pdf = Pdf::loadView('pdf.certificado-administrativo', [
            'estudiante' => $estudiante,
            'inscripcion' => $inscripcion,
            'proyecto' => $inscripcion->proyecto,
            'horas' => $inscripcion->horas_requeridas ?? 0,
            'gestorNombre' => $gestorNombre,
            'numeroCertificado' => $numeroCertificado,
            'fecha' => now(),
        ]);

        $rutaPdf = 'certificados_administrativos/certificado_' . $inscripcion->id . '.pdf';
        Storage::disk('public')->put($rutaPdf, $pdf->output());

        // Si ya existía uno para este estudiante, lo actualizamos en vez de duplicar
        // (por ejemplo, si se corrigió la cédula y hay que regenerarlo).
        CertificadoAdministrativo::updateOrCreate(
            ['inscripcion_id' => $inscripcion->id],
            [
                'numero_certificado' => $numeroCertificado,
                'fecha_generacion' => now()->format('Y-m-d'),
                'generado_por' => $admin->id,
                'ruta_pdf' => $rutaPdf,
            ]
        );

        return redirect()->route('admin.certificados.index')
            ->with('success', 'Certificado generado correctamente para ' . $estudiante->name . '.');
    }

    // Descargar el PDF ya generado
    public function descargar(CertificadoAdministrativo $certificado)
    {
        if (!Storage::disk('public')->exists($certificado->ruta_pdf)) {
            abort(404, 'El archivo ya no está disponible.');
        }

        return Storage::disk('public')->download(
            $certificado->ruta_pdf,
            'Certificado-' . $certificado->numero_certificado . '.pdf'
        );
    }
}