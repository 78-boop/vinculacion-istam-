<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificadoAdministrativo;
use App\Models\Inscripcion;
use App\Models\TipoCertificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificadoAdministrativoController extends Controller
{
    // El "Gestor de Vinculación" es un cargo institucional fijo. Si cambia, se actualiza aquí.
    private const GESTOR = 'Ing. Juan Carlos Guarinda Castillo';

    // Admin: estudiantes que ya tienen todos sus documentos aprobados
    public function index()
    {
        $totalTipos = TipoCertificado::where('activo', true)->count();

        // Una sola consulta: cuenta los documentos aprobados de cada inscripción
        $inscripciones = $totalTipos === 0 ? collect() : Inscripcion::with(['estudiante', 'proyecto', 'certificadoAdministrativo'])
            ->withCount(['certificadosVigentes as aprobados_count' => fn ($q) => $q->where('estado', 'aprobado')])
            ->get()
            ->filter(fn ($inscripcion) => $inscripcion->aprobados_count >= $totalTipos)
            ->values();

        return view('admin.certificados.index', compact('inscripciones'));
    }

    // Admin: registra el certificado (rápido). El PDF y el Word se arman al descargar,
    // siempre con la fecha del día de la descarga.
    public function generar(Inscripcion $inscripcion)
    {
        if (! $inscripcion->todosCertificadosAprobados()) {
            return redirect()->route('admin.certificados.index')
                ->with('error', 'Este estudiante todavía no tiene todos sus documentos aprobados.');
        }

        $estudiante = $inscripcion->estudiante;
        if ((int) $inscripcion->horas_cumplidas <= 0) {
            return redirect()->route('admin.certificados.index')
                ->with('aviso_titulo', 'Faltan las horas')
                ->with('warning', 'El docente aún no registra las horas cumplidas de ' . $estudiante->name
                    . '. El certificado se emite con esas horas, así que deben registrarse primero.');
        }

        if (empty($estudiante->cedula)) {
            return redirect()->route('admin.usuarios.edit', $estudiante->id)
                ->with('error', 'Este estudiante no tiene cédula registrada. Complétala para poder generar el certificado.');
        }

        CertificadoAdministrativo::updateOrCreate(
            ['inscripcion_id' => $inscripcion->id],
            [
                'numero_certificado' => 'ADM-' . $inscripcion->id . '-' . now()->format('YmdHis'),
                'fecha_generacion' => now()->format('Y-m-d'),
                'generado_por' => Auth::id(),
                'ruta_pdf' => 'certificados_administrativos/certificado_' . $inscripcion->id . '.pdf',
            ]
        );

        return redirect()->route('admin.certificados.index')
            ->with('success', 'Certificado de ' . $estudiante->name . ' listo. Ya puedes descargarlo en PDF o Word.');
    }

    // Estudiante: descarga su propio certificado de vinculación (solo si el administrador ya lo emitió)
    public function descargarEstudiante(CertificadoAdministrativo $certificado)
    {
        $this->autorizarEstudiante($certificado);

        return $this->descargar($certificado);
    }

    public function descargarWordEstudiante(CertificadoAdministrativo $certificado)
    {
        $this->autorizarEstudiante($certificado);

        return $this->descargarWord($certificado);
    }

    private function autorizarEstudiante(CertificadoAdministrativo $certificado): void
    {
        abort_unless($certificado->inscripcion?->estudiante_id === Auth::id(), 403, 'Este certificado no te pertenece.');
    }

    // Descargar PDF: se genera en el momento con la fecha de hoy
    public function descargar(CertificadoAdministrativo $certificado)
    {
        $datos = $this->datos($certificado);

        $pdf = Pdf::loadView('pdf.certificado-administrativo', $datos)
            ->setPaper('a4')
            ->setOption('isFontSubsettingEnabled', true); // solo las letras usadas: archivo mucho más liviano

        // Se guarda una copia actualizada como respaldo
        Storage::disk('public')->put($certificado->ruta_pdf, $pdf->output());

        return $pdf->download('Certificado-' . $certificado->numero_certificado . '.pdf');
    }

    // Descargar Word editable: misma información y fecha de hoy
    public function descargarWord(CertificadoAdministrativo $certificado)
    {
        $datos = $this->datos($certificado);

        $plantilla = storage_path('app/templates/certificado-plantilla.docx');
        if (! is_file($plantilla)) {
            $plantilla = storage_path('app/templates/certificado-plantilla.docx.docx');
        }
        if (! is_file($plantilla)) {
            abort(404, 'No se encontró la plantilla Word del certificado.');
        }

        $rutaWord = storage_path('app/certificado_' . $certificado->inscripcion_id . '_' . uniqid() . '.docx');
        copy($plantilla, $rutaWord);

        $zip = new \ZipArchive();
        if ($zip->open($rutaWord) !== true) {
            abort(500, 'No se pudo abrir la plantilla Word.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $e = static fn (string $v): string => htmlspecialchars($v, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $t = '<w:t\b[^>]*>';   // apertura de un fragmento de texto de Word

        // Reemplaza el texto capturado en el grupo indicado, conservando el resto (estilos de Word)
        $cambiar = function (string $patron, int $grupo, string $valor) use (&$xml, $e) {
            $xml = preg_replace_callback($patron, function ($m) use ($grupo, $valor, $e) {
                $salida = '';
                for ($i = 1; $i < count($m); $i++) {
                    $salida .= $i === $grupo ? $e($valor) : $m[$i];
                }
                return $salida;
            }, $xml, 1);
        };

        // Cada dato está en su propio fragmento de Word: se reemplaza sin perder estilos (negritas, etc.)
        $cambiar("~($t)(ESTUDIANTE)(</w:t>)~", 2, mb_strtoupper($datos['estudiante']->name));
        $cambiar("~(identidad.*?$t)([0-9]{6,20})(</w:t>)~s", 2, (string) $datos['estudiante']->cedula);

        // "Proyecto" + " " + "Test" están en 3 fragmentos: el nombre va en el primero y se vacían los otros
        $cambiar("~(denominado.*?$t)(Proyecto)(</w:t>.*?$t) (</w:t>.*?$t)Test(</w:t>)~s", 2, $datos['proyecto']->nombre);

        // Horas
        $cambiar("~($t)(90)(</w:t>)(?=.*? horas)~s", 2, (string) $datos['horas']);

        // Número del certificado: "ADM-1-" + "20260919113638"
        $cambiar("~($t)(ADM-[0-9]+-)(</w:t>.*?$t)[0-9]{8,20}(</w:t>)~s", 2, $certificado->numero_certificado);

        // Fecha: está repartida en varios fragmentos dentro del párrafo "Yantzaza, ..."
        $fecha = 'Yantzaza, ' . $datos['fecha']->translatedFormat('d \d\e F \d\e\l Y');
        $xml = preg_replace_callback('~<w:p\b[^>]*>.*?</w:p>~s', function ($parrafo) use ($e, $fecha) {
            if (! str_contains($parrafo[0], 'Yantzaza,')) {
                return $parrafo[0];
            }
            $primero = true;

            return preg_replace_callback('~(<w:t\b[^>]*>)(.*?)(</w:t>)~s', function ($f) use (&$primero, $e, $fecha) {
                if ($primero) {
                    $primero = false;
                    return $f[1] . $e($fecha) . $f[3];
                }
                return $f[1] . $f[3];
            }, $parrafo[0]);
        }, $xml);

        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        return response()->download($rutaWord, 'Certificado-' . $certificado->numero_certificado . '.docx')
            ->deleteFileAfterSend(true);
    }

    // Datos del certificado, siempre con la fecha de hoy
    private function datos(CertificadoAdministrativo $certificado): array
    {
        $inscripcion = $certificado->inscripcion()->with(['estudiante', 'proyecto'])->firstOrFail();

        // Actividad real del estudiante (creada por el administrador); si no hay, la del catálogo antiguo
        $actividad = $inscripcion->actividadesAsignadas()->latest('fecha_inicio')->first();
        $postulacion = $actividad ? null : $inscripcion->postulacionesActividad()
            ->with('actividad')
            ->where('estado', 'aprobada')
            ->latest('updated_at')
            ->first();
        $nombreActividad = $actividad
            ? \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $actividad->descripcion ?: $actividad->lugar ?: '')), 90)
            : $postulacion?->actividad?->titulo;

        return [
            'estudiante' => $inscripcion->estudiante,
            'inscripcion' => $inscripcion,
            'proyecto' => $inscripcion->proyecto,
            'actividadNombre' => $nombreActividad ?: 'Actividad de Vinculación',
            'horas' => (int) $inscripcion->horas_cumplidas, // horas que registra el docente
            'gestorNombre' => self::GESTOR,
            'numeroCertificado' => $certificado->numero_certificado,
            'fecha' => now()->locale('es'),
        ];
    }
}
