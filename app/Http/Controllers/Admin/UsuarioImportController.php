<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Carga masiva de usuarios desde un archivo Excel o CSV
class UsuarioImportController extends Controller
{
    private const MAX_FILAS = 5000;

    // Nombres de columna aceptados (sin tildes, en minúsculas)
    private const ALIAS = [
        'nombre'   => ['nombre', 'nombres', 'name', 'nombres y apellidos', 'nombre completo', 'apellidos y nombres', 'estudiante'],
        'cedula'   => ['cedula', 'ci', 'identificacion', 'numero de cedula', 'documento', 'dni'],
        'correo'   => ['correo', 'email', 'correo electronico', 'e-mail', 'mail', 'correo institucional'],
        'rol'      => ['rol', 'role', 'tipo', 'tipo de usuario'],
        'carrera'  => ['carrera', 'carrera profesional', 'programa'],
        'password' => ['contrasena', 'password', 'clave'],
    ];

    public function create()
    {
        $carreras = Carrera::where('activo', true)->orderBy('nombre')->get();

        return view('admin.usuarios.importar', compact('carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:10240'],
            'rol_defecto' => ['required', 'in:estudiante,docente'],
        ], [
            'archivo.required' => 'Selecciona el archivo Excel o CSV con los usuarios.',
            'archivo.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV.',
            'archivo.max' => 'El archivo no puede pesar más de 10 MB.',
        ]);

        @set_time_limit(0);

        try {
            $hoja = IOFactory::load($request->file('archivo')->getRealPath())->getActiveSheet();
            $filas = $hoja->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo leer el archivo. Verifica que sea un Excel o CSV válido.');
        }

        // Encabezados -> campo
        $encabezados = array_map(fn ($h) => $this->normalizar((string) $h), array_shift($filas) ?? []);
        $columnas = [];
        foreach ($encabezados as $i => $h) {
            foreach (self::ALIAS as $campo => $alias) {
                if (in_array($h, $alias, true) && ! isset($columnas[$campo])) {
                    $columnas[$campo] = $i;
                }
            }
        }

        $faltan = array_diff(['nombre', 'cedula', 'correo'], array_keys($columnas));
        if ($faltan) {
            return back()->with('error', 'Al archivo le faltan las columnas: ' . implode(', ', $faltan)
                . '. Descarga la plantilla para ver el formato correcto.');
        }

        $filas = array_values(array_filter($filas, fn ($f) => trim(implode('', array_map('strval', $f))) !== ''));
        if (count($filas) === 0) {
            return back()->with('error', 'El archivo no tiene usuarios (solo encabezados).');
        }
        if (count($filas) > self::MAX_FILAS) {
            return back()->with('error', 'El archivo tiene ' . count($filas) . ' filas. Divídelo en archivos de máximo ' . self::MAX_FILAS . '.');
        }

        // Datos existentes para detectar duplicados sin consultar fila por fila
        $correosExistentes = User::pluck('email')->map(fn ($c) => mb_strtolower($c))->flip();
        $cedulasExistentes = User::whereNotNull('cedula')->pluck('cedula')->flip();
        $carreras = Carrera::all()->mapWithKeys(fn ($c) => [$this->normalizar($c->nombre) => $c]);

        $nuevos = [];
        $errores = [];
        $avisos = [];
        $vistosCorreo = [];
        $vistosCedula = [];
        $ahora = now();

        foreach ($filas as $n => $fila) {
            $linea = $n + 2; // +1 encabezado, +1 porque Excel empieza en 1
            $valor = fn ($campo) => isset($columnas[$campo]) ? trim((string) ($fila[$columnas[$campo]] ?? '')) : '';

            $nombre = preg_replace('/\s+/', ' ', $valor('nombre'));
            $correo = mb_strtolower($valor('correo'));
            $cedula = preg_replace('/\D/', '', $valor('cedula'));
            if (strlen($cedula) === 9) {
                $cedula = '0' . $cedula; // Excel quita el 0 inicial de las cédulas
            }

            $rolTexto = $this->normalizar($valor('rol'));
            $rol = match (true) {
                $rolTexto === '' => $request->rol_defecto,
                str_starts_with($rolTexto, 'est') => 'estudiante',
                str_starts_with($rolTexto, 'doc') || str_starts_with($rolTexto, 'prof') => 'docente',
                default => null,
            };

            $problemas = [];
            if ($nombre === '') $problemas[] = 'falta el nombre';
            if (! filter_var($correo, FILTER_VALIDATE_EMAIL)) $problemas[] = 'correo inválido';
            if (strlen($cedula) < 10 || strlen($cedula) > 13) $problemas[] = 'cédula inválida';
            if (! $rol) $problemas[] = 'rol "' . $valor('rol') . '" no válido (usa estudiante o docente)';
            if ($correo && (isset($correosExistentes[$correo]) || isset($vistosCorreo[$correo]))) $problemas[] = 'el correo ya existe';
            if ($cedula && (isset($cedulasExistentes[$cedula]) || isset($vistosCedula[$cedula]))) $problemas[] = 'la cédula ya existe';

            if ($problemas) {
                $errores[] = ['fila' => $linea, 'nombre' => $nombre ?: '—', 'motivo' => implode(', ', $problemas)];
                continue;
            }

            $carreraId = null;
            $carreraTexto = $valor('carrera');
            if ($carreraTexto !== '') {
                $carrera = $this->buscarCarrera($carreraTexto, $carreras);
                if ($carrera) {
                    $carreraId = $carrera->id;
                } else {
                    $avisos[] = ['fila' => $linea, 'nombre' => $nombre, 'motivo' => 'carrera "' . $carreraTexto . '" no encontrada; se creó sin carrera'];
                }
            }

            $password = $valor('password') !== '' ? $valor('password') : $cedula;

            $vistosCorreo[$correo] = true;
            $vistosCedula[$cedula] = true;
            $nuevos[] = [
                'name' => Str::title(mb_strtolower($nombre)),
                'cedula' => $cedula,
                'email' => $correo,
                'role' => $rol,
                'carrera_id' => $rol === 'estudiante' ? $carreraId : null,
                // Cifrado liviano para que la carga masiva sea rápida (~5 ms por usuario en vez de ~250 ms).
                // Laravel lo vuelve a cifrar con la seguridad completa en el primer inicio de sesión (rehash_on_login).
                'password' => Hash::driver('bcrypt')->make($password, ['rounds' => 6]),
                'permitir_nueva_actividad' => false,
                'email_verified_at' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        DB::transaction(function () use ($nuevos) {
            foreach (array_chunk($nuevos, 500) as $bloque) {
                DB::table('users')->insert($bloque);
            }
        });

        $resumen = [
            'creados' => count($nuevos),
            'total' => count($filas),
            'errores' => $errores,
            'avisos' => $avisos,
            'estudiantes' => count(array_filter($nuevos, fn ($u) => $u['role'] === 'estudiante')),
            'docentes' => count(array_filter($nuevos, fn ($u) => $u['role'] === 'docente')),
        ];

        $mensaje = count($nuevos)
            ? 'Se crearon ' . count($nuevos) . ' de ' . count($filas) . ' usuarios.'
            : 'No se creó ningún usuario. Revisa los errores del archivo.';

        return redirect()->route('admin.usuarios.importar')
            ->with(count($nuevos) ? 'success' : 'warning', $mensaje)
            ->with('resumen_importacion', $resumen);
    }

    // Plantilla Excel con encabezados, ejemplo y listas desplegables
    public function plantilla()
    {
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet()->setTitle('Usuarios');

        $encabezados = ['Nombre', 'Cedula', 'Correo', 'Rol', 'Carrera', 'Contrasena'];
        $hoja->fromArray($encabezados, null, 'A1');
        $hoja->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '006B47']],
        ]);

        // La cédula como TEXTO para que Excel no borre el 0 inicial
        $hoja->getStyle('B2:B' . (self::MAX_FILAS + 1))->getNumberFormat()->setFormatCode('@');

        $carreras = Carrera::where('activo', true)->orderBy('nombre')->pluck('nombre')->all();
        $hoja->setCellValueExplicit('A2', 'Ana María Pérez López', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $hoja->setCellValueExplicit('B2', '0102030405', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $hoja->setCellValue('C2', 'ana.perez@istam.edu.ec');
        $hoja->setCellValue('D2', 'estudiante');
        $hoja->setCellValue('E2', $carreras[0] ?? '');
        $hoja->setCellValue('F2', '');

        foreach (['A' => 34, 'B' => 16, 'C' => 34, 'D' => 14, 'E' => 38, 'F' => 18] as $col => $ancho) {
            $hoja->getColumnDimension($col)->setWidth($ancho);
        }
        $hoja->freezePane('A2');

        // Hoja auxiliar con las carreras y los roles (para las listas desplegables)
        $listas = $libro->createSheet()->setTitle('Listas');
        $listas->setCellValue('A1', 'Roles');
        $listas->fromArray([['estudiante'], ['docente']], null, 'A2');
        $listas->setCellValue('B1', 'Carreras');
        foreach ($carreras as $i => $nombre) {
            $listas->setCellValue('B' . ($i + 2), $nombre);
        }
        $listas->getColumnDimension('B')->setWidth(40);

        $validar = function (string $rango, string $formula) use ($hoja) {
            $v = new DataValidation();
            $v->setType(DataValidation::TYPE_LIST)->setAllowBlank(true)->setShowDropDown(true)
                ->setShowErrorMessage(true)->setErrorTitle('Valor no válido')->setError('Elige un valor de la lista.')
                ->setFormula1($formula);
            $hoja->setDataValidation($rango, $v);
        };
        $validar('D2:D' . (self::MAX_FILAS + 1), "'Listas'!\$A\$2:\$A\$3");
        if ($carreras) {
            $validar('E2:E' . (self::MAX_FILAS + 1), "'Listas'!\$B\$2:\$B\$" . (count($carreras) + 1));
        }

        // Instrucciones
        $ayuda = $libro->createSheet()->setTitle('Instrucciones');
        $ayuda->fromArray([
            ['Cómo llenar la plantilla'],
            ['1. Una fila por usuario, empezando en la fila 2 de la hoja "Usuarios".'],
            ['2. Nombre, Cédula y Correo son obligatorios.'],
            ['3. Rol: estudiante o docente. Si se deja vacío se usa el rol elegido al importar.'],
            ['4. Carrera: elige de la lista (solo para estudiantes).'],
            ['5. Contraseña: opcional. Si se deja vacía, la contraseña será la cédula.'],
            ['6. Se omiten los usuarios cuyo correo o cédula ya existan en el sistema.'],
        ], null, 'A1');
        $ayuda->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $ayuda->getColumnDimension('A')->setWidth(90);

        $libro->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
        }, 'plantilla-usuarios-istam.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);

        return preg_replace('/\s+/', ' ', $texto);
    }

    // Busca la carrera por nombre exacto o parcial ("Software" -> "Tecnología en Desarrollo de Software")
    private function buscarCarrera(string $texto, $carreras): ?Carrera
    {
        $clave = $this->normalizar($texto);
        if (isset($carreras[$clave])) {
            return $carreras[$clave];
        }

        $coincidencias = $carreras->filter(fn ($c, $nombre) => str_contains($nombre, $clave) || str_contains($clave, $nombre));

        return $coincidencias->count() === 1 ? $coincidencias->first() : null;
    }
}
