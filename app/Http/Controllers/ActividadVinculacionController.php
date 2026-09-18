<?php

namespace App\Http\Controllers;

use App\Models\ActividadVinculacion;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ActividadVinculacionController extends Controller
{
    // Docente: ver las 4 tarjetas de carrera
    public function index()
    {
        $carreras = Carrera::where('activo', true)
            ->withCount('actividadesVinculacion')
            ->orderBy('nombre')
            ->get();

        return view('docente.actividades-vinculacion-index', [
            'carreras' => $carreras,
        ]);
    }

    // Docente: ver/agregar actividades de una carrera puntual
    public function show(Carrera $carrera)
    {
        $actividades = $carrera->actividadesVinculacion()
            ->with('creador')
            ->latest()
            ->get();

        return view('docente.actividades-vinculacion-show', [
            'carrera' => $carrera,
            'actividades' => $actividades,
        ]);
    }

    // Docente: exportar el catálogo COMPLETO (todas las carreras) en Excel
    public function exportar()
    {
        $carreras = Carrera::where('activo', true)
            ->with(['actividadesVinculacion' => function ($query) {
                $query->where('activo', true)->with('creador')->orderBy('id');
            }])
            ->orderBy('nombre')
            ->get();

        return $this->generarExcel($carreras, 'catalogo-actividades-vinculacion-' . now()->format('Y-m-d'));
    }

    // Docente: exportar el catálogo de UNA sola carrera en Excel
    public function exportarCarrera(Carrera $carrera)
    {
        $carrera->load(['actividadesVinculacion' => function ($query) {
            $query->where('activo', true)->with('creador')->orderBy('id');
        }]);

        $slug = Str::slug($carrera->nombre);
        return $this->generarExcel(collect([$carrera]), 'actividades-' . $slug . '-' . now()->format('Y-m-d'));
    }

    // Arma el archivo Excel (mismo diseño para el catálogo completo o para una sola carrera)
    private function generarExcel($carreras, string $nombreBase)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Catálogo');

        // ===== Franja verde institucional =====
        // Reservamos A1:B2 solo para el logo, y C1:E2 para el título, para
        // que nunca se superpongan (antes el logo tapaba el inicio del texto).
        $sheet->mergeCells('A1:B2');
        $sheet->mergeCells('C1:E2');
        $sheet->getStyle('A1:E2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('8BC34A');

        $sheet->setCellValue('C1', 'INSTITUTO SUPERIOR TECNOLÓGICO AMAZÓNICO');
        $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('C1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $rutaLogo = public_path('images/logo-istam.png');
        if (file_exists($rutaLogo)) {
            $drawing = new Drawing();
            $drawing->setName('Logo ISTAM');
            $drawing->setPath($rutaLogo);
            $drawing->setHeight(45);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(8);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // ===== Encabezados de columna (fila 3), en negrita =====
        $encabezados = ['N°', 'Carrera', 'Actividad', 'Descripción', 'Subida por'];
        $columnas = ['A', 'B', 'C', 'D', 'E'];
        foreach ($columnas as $i => $col) {
            $sheet->setCellValue($col . '3', $encabezados[$i]);
        }
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        $sheet->getStyle('A3:E3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E8F5E9');
        $sheet->getStyle('A3:E3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // ===== Datos, numerados desde 1 en cada carrera =====
        $fila = 4;
        foreach ($carreras as $carrera) {
            $numero = 1;
            foreach ($carrera->actividadesVinculacion as $actividad) {
                $sheet->setCellValue('A' . $fila, $numero);
                $sheet->setCellValue('B' . $fila, $carrera->nombre);
                $sheet->setCellValue('C' . $fila, $actividad->titulo);
                $sheet->setCellValue('D' . $fila, $actividad->descripcion);
                $sheet->setCellValue('E' . $fila, $actividad->creador->name ?? '—');

                $sheet->getStyle('A' . $fila . ':E' . $fila)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('D' . $fila)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('A' . $fila . ':C' . $fila . ',E' . $fila)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // Altura automática: Excel la recalcula sola al abrir el archivo
                // gracias al "ajustar texto" de la columna Descripción.
                $sheet->getRowDimension($fila)->setRowHeight(-1);

                $numero++;
                $fila++;
            }
        }

        // ===== Anchos de columna =====
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(55);
        $sheet->getColumnDimension('E')->setWidth(18);

        $nombreArchivo = $nombreBase . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // Docente: crear una actividad nueva para una carrera
    public function store(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        ActividadVinculacion::create([
            'carrera_id' => $request->carrera_id,
            'creado_por' => Auth::id(),
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'activo' => true,
        ]);

        return redirect()->route('docente.actividades-vinculacion.show', $request->carrera_id)
            ->with('success', 'Actividad agregada al catálogo.');
    }

    // Docente: desactivar una actividad (ya no se ofrece a nuevos estudiantes)
    public function desactivar(ActividadVinculacion $actividad)
    {
        $actividad->update(['activo' => false]);

        return redirect()->route('docente.actividades-vinculacion.show', $actividad->carrera_id)
            ->with('success', 'Actividad desactivada.');
    }
}