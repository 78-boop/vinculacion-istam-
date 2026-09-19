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
        $postulacion = $inscripcion->postulacionesActividad()
            ->with('actividad')
            ->where('estado', 'aprobada')
            ->latest('updated_at')
            ->first();
        $actividadNombre = $postulacion?->actividad?->titulo ?? 'Actividad de Vinculación';

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
            'actividadNombre' => $actividadNombre,
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

    // Descargar una versión Word editable con los mismos datos del certificado.
    public function descargarWord(CertificadoAdministrativo $certificado)
    {
        $inscripcion = $certificado->inscripcion()
            ->with(['estudiante', 'proyecto'])
            ->firstOrFail();

        $estudiante = $inscripcion->estudiante;
        $proyecto = $inscripcion->proyecto;
        $fecha = now()->locale('es')->translatedFormat('d \\d\\e F \\d\\e\\l Y');
        $plantilla = storage_path('app/templates/certificado-plantilla.docx');

        if (!is_file($plantilla)) {
            $plantilla = storage_path('app/templates/certificado-plantilla.docx.docx');
        }

        if (!is_file($plantilla)) {
            abort(404, 'No se encontró la plantilla Word del certificado.');
        }

        $rutaWord = storage_path('app/certificado_' . $inscripcion->id . '_' . uniqid() . '.docx');
        copy($plantilla, $rutaWord);

        $zip = new \ZipArchive();
        if ($zip->open($rutaWord) !== true) {
            abort(500, 'No se pudo abrir la plantilla Word.');
        }

        $documentXml = $zip->getFromName('word/document.xml');

        $escaparXml = static fn (string $valor): string => htmlspecialchars($valor, ENT_XML1 | ENT_COMPAT, 'UTF-8');

        // La plantilla conserva cada dato en sus propios fragmentos de Word para no perder estilos.
        $documentXml = preg_replace_callback(
            '~(<w:t\b[^>]*>)ESTUDIANTE(</w:t>)~',
            fn (array $coincidencia): string => $coincidencia[1] . $escaparXml(strtoupper($estudiante->name)) . $coincidencia[2],
            $documentXml,
            1
        );
        $documentXml = preg_replace_callback(
            '~(identidad.*?<w:t\b[^>]*>)[0-9]{6,20}(</w:t>)~s',
            fn (array $coincidencia): string => $coincidencia[1] . $escaparXml((string) $estudiante->cedula) . $coincidencia[2],
            $documentXml,
            1
        );
        $documentXml = preg_replace_callback(
            '~(<w:t\b[^>]*>)Proyecto Test(</w:t>)~',
            fn (array $coincidencia): string => $coincidencia[1] . $escaparXml($proyecto->nombre) . $coincidencia[2],
            $documentXml,
            1
        );

        // La fecha está separada en varios fragmentos; se reemplaza dentro de su párrafo conservando su formato.
        $documentXml = preg_replace_callback(
            '~<w:p\b[^>]*>.*?</w:p>~s',
            function (array $coincidencia) use ($escaparXml, $fecha): string {
                if (strpos($coincidencia[0], 'Yantzaza,') === false) {
                    return $coincidencia[0];
                }

                $primerFragmento = true;

                return preg_replace_callback(
                    '~(<w:t\b[^>]*>)(.*?)(</w:t>)~s',
                    function (array $fragmento) use (&$primerFragmento, $escaparXml, $fecha): string {
                        if ($primerFragmento) {
                            $primerFragmento = false;
                            return $fragmento[1] . $escaparXml('Yantzaza, ' . $fecha) . $fragmento[3];
                        }

                        return $fragmento[1] . $fragmento[3];
                    },
                    $coincidencia[0]
                );
            },
            $documentXml,
            1
        );

        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        return response()->download(
            $rutaWord,
            'Certificado-' . $certificado->numero_certificado . '.docx'
        )->deleteFileAfterSend(true);
    }
}