<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroHorasController;
use App\Http\Controllers\CertificadoEstudianteController;
use App\Http\Controllers\ActividadVinculacionController;
use App\Http\Controllers\PostulacionActividadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PeriodoAcademicoController;
use App\Http\Controllers\Admin\ProyectoVinculacionController;
use App\Http\Controllers\Admin\ActividadController;
use App\Http\Controllers\Admin\InscripcionController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CertificadoAdministrativoController;
use App\Http\Controllers\Admin\TipoCertificadoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student enrollment route (allows students to enroll in projects)
    Route::post('/inscripciones', [\App\Http\Controllers\Admin\InscripcionController::class, 'store'])
        ->name('inscripciones.store');

    // CERTIFICADOS - ESTUDIANTE
    Route::get('/mis-certificados', [CertificadoEstudianteController::class, 'index'])
        ->name('certificados-estudiante.index');
    Route::get('/mis-certificados/{certificado}/descargar', [CertificadoEstudianteController::class, 'descargar'])
        ->name('certificados-estudiante.descargar');
    // Certificado de vinculación: el estudiante lo descarga una vez que el administrador lo emite
    Route::get('/mis-certificados/vinculacion/{certificado}/pdf', [CertificadoAdministrativoController::class, 'descargarEstudiante'])
        ->name('certificados-estudiante.vinculacion.pdf');
    Route::get('/mis-certificados/vinculacion/{certificado}/word', [CertificadoAdministrativoController::class, 'descargarWordEstudiante'])
        ->name('certificados-estudiante.vinculacion.word');

    // ACTIVIDADES DE VINCULACIÓN - ESTUDIANTE
    Route::get('/mis-actividades-vinculacion', [PostulacionActividadController::class, 'index'])
        ->name('actividades-vinculacion.index');
    Route::post('/mis-actividades-vinculacion/postular', [PostulacionActividadController::class, 'postular'])
        ->name('actividades-vinculacion.postular');
    Route::delete('/mis-actividades-vinculacion/{postulacion}/cancelar', [PostulacionActividadController::class, 'cancelar'])
        ->name('actividades-vinculacion.cancelar');

    // PROYECTOS Y ACTIVIDADES - ESTUDIANTE (escoge una sola actividad)
    Route::middleware('role:estudiante')->group(function () {
        Route::get('/proyectos-disponibles', [\App\Http\Controllers\ProyectoEstudianteController::class, 'index'])
            ->name('estudiante.proyectos.index');
        Route::get('/proyectos-disponibles/{proyecto}', [\App\Http\Controllers\ProyectoEstudianteController::class, 'show'])
            ->name('estudiante.proyectos.show');
        Route::post('/actividades/{actividad}/inscribirme', [\App\Http\Controllers\ProyectoEstudianteController::class, 'inscribirse'])
            ->name('estudiante.actividades.inscribirse');
    });

    Route::middleware('role:docente')->group(function () {
        // DOCENTE: actividades creadas por el administrador en sus proyectos / a su cargo
        Route::get('/docente/mis-actividades', [\App\Http\Controllers\DocenteActividadController::class, 'index'])
            ->name('docente.actividades.index');

        Route::get('/docente/proyectos/crear', [\App\Http\Controllers\Admin\ProyectoVinculacionController::class, 'crearPropuesta'])
            ->name('docente.proyectos.create');
        Route::post('/docente/proyectos', [\App\Http\Controllers\Admin\ProyectoVinculacionController::class, 'storePropuesta'])
            ->name('docente.proyectos.store');

        // CERTIFICADOS - DOCENTE
        Route::get('/docente/certificados', [\App\Http\Controllers\DocenteCertificadoController::class, 'index'])
            ->name('docente.certificados.index');
        Route::get('/docente/certificados/{inscripcion}', [\App\Http\Controllers\DocenteCertificadoController::class, 'show'])
            ->name('docente.certificados.show');
        Route::post('/docente/certificados/{inscripcion}/horas', [\App\Http\Controllers\DocenteCertificadoController::class, 'actualizarHoras'])
            ->name('docente.certificados.actualizar-horas');

        // ACTIVIDADES DE VINCULACIÓN - DOCENTE
        Route::get('/docente/actividades-vinculacion', [ActividadVinculacionController::class, 'index'])
            ->name('docente.actividades-vinculacion.index');
        Route::get('/docente/actividades-vinculacion/exportar', [ActividadVinculacionController::class, 'exportar'])
            ->name('docente.actividades-vinculacion.exportar');
        Route::get('/docente/actividades-vinculacion/{carrera}', [ActividadVinculacionController::class, 'show'])
            ->name('docente.actividades-vinculacion.show');
        Route::get('/docente/actividades-vinculacion/{carrera}/exportar', [ActividadVinculacionController::class, 'exportarCarrera'])
            ->name('docente.actividades-vinculacion.exportar-carrera');
        Route::post('/docente/actividades-vinculacion', [ActividadVinculacionController::class, 'store'])
            ->name('docente.actividades-vinculacion.store');
        Route::post('/docente/actividades-vinculacion/{actividad}/desactivar', [ActividadVinculacionController::class, 'desactivar'])
            ->name('docente.actividades-vinculacion.desactivar');

        // POSTULACIONES DE ACTIVIDAD - DOCENTE
        Route::get('/docente/postulaciones-actividad', [\App\Http\Controllers\DocentePostulacionController::class, 'index'])
            ->name('docente.postulaciones-actividad.index');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('periodos', PeriodoAcademicoController::class)->except(['show']);
    Route::resource('carreras', \App\Http\Controllers\Admin\CarreraController::class)->except(['show']);
    Route::resource('proyectos', ProyectoVinculacionController::class)->except(['show']);
    Route::resource('inscripciones', \App\Http\Controllers\Admin\InscripcionController::class)->parameters(['inscripciones' => 'inscripcione'])->except(['show']);
    Route::resource('actividades', ActividadController::class)->parameters(['actividades' => 'actividad'])->except(['show']);
    // Carga masiva de usuarios (Excel / CSV)
    Route::get('/usuarios/importar', [\App\Http\Controllers\Admin\UsuarioImportController::class, 'create'])->name('usuarios.importar');
    Route::post('/usuarios/importar', [\App\Http\Controllers\Admin\UsuarioImportController::class, 'store'])->name('usuarios.importar.store');
    Route::get('/usuarios/importar/plantilla', [\App\Http\Controllers\Admin\UsuarioImportController::class, 'plantilla'])->name('usuarios.importar.plantilla');
    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario'])->except(['show']);
    Route::resource('tipos-certificado', TipoCertificadoController::class)
        ->parameters(['tipos-certificado' => 'tiposCertificado'])->except(['show']);

    // ADMIN: dar / quitar permiso a un estudiante para inscribirse en otra actividad
    Route::post('/usuarios/{usuario}/permitir-actividad', [UsuarioController::class, 'permitirActividad'])
        ->name('usuarios.permitir-actividad');

    // ADMIN: aprobar/rechazar proyectos propuestos por un docente
    Route::post('/proyectos/{proyecto}/aprobar', [ProyectoVinculacionController::class, 'aprobar'])
        ->name('proyectos.aprobar');
    Route::post('/proyectos/{proyecto}/rechazar', [ProyectoVinculacionController::class, 'rechazar'])
        ->name('proyectos.rechazar');

    // CERTIFICADO ADMINISTRATIVO (gestor de vinculación)
    Route::get('/certificados', [CertificadoAdministrativoController::class, 'index'])
        ->name('certificados.index');
    Route::post('/certificados/{inscripcion}/generar', [CertificadoAdministrativoController::class, 'generar'])
        ->name('certificados.generar');
    Route::get('/certificados-administrativos/{certificado}/descargar', [CertificadoAdministrativoController::class, 'descargar'])
        ->name('certificados.descargar');
    Route::get('/certificados-administrativos/{certificado}/descargar-word', [CertificadoAdministrativoController::class, 'descargarWord'])
        ->name('certificados.descargar-word');
});

// API ROUTES - CERTIFICADOS
Route::middleware(['auth'])->prefix('api')->group(function () {
    // ESTUDIANTE: Subir/reemplazar un certificado (FPVS)
    Route::post('/certificados-estudiante', [CertificadoEstudianteController::class, 'store'])
        ->name('api.certificados-estudiante.store');

    // DOCENTE: Aprobar/rechazar un certificado subido por el estudiante
    Route::post('/certificados-estudiante/{certificado}/aprobar', [\App\Http\Controllers\DocenteCertificadoController::class, 'aprobar'])
        ->middleware('role:docente')
        ->name('api.certificados-estudiante.aprobar');
    Route::post('/certificados-estudiante/{certificado}/rechazar', [\App\Http\Controllers\DocenteCertificadoController::class, 'rechazar'])
        ->middleware('role:docente')
        ->name('api.certificados-estudiante.rechazar');

    // DOCENTE: Aprobar/rechazar una postulación de actividad de vinculación
    Route::post('/postulaciones-actividad/{postulacion}/aprobar', [\App\Http\Controllers\DocentePostulacionController::class, 'aprobar'])
        ->middleware('role:docente')
        ->name('api.postulaciones-actividad.aprobar');
    Route::post('/postulaciones-actividad/{postulacion}/rechazar', [\App\Http\Controllers\DocentePostulacionController::class, 'rechazar'])
        ->middleware('role:docente')
        ->name('api.postulaciones-actividad.rechazar');

    // Descargar certificado final (horas cumplidas, se genera automático)
    Route::get('/certificados/{certificado}/descargar', [RegistroHorasController::class, 'descargarCertificado'])
        ->name('api.certificados.descargar');
});

require __DIR__.'/auth.php';