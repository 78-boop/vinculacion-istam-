@echo off
REM Script para ejecutar migraciones FASE 2
REM Este script crea todas las tablas nuevas

setlocal enabledelayedexpansion

cd /d "%~dp0"

echo.
echo ====================================
echo Ejecutando Migraciones FASE 2
echo ====================================
echo.

REM Verificar que estamos en el directorio correcto
if not exist "artisan" (
    echo ERROR: No se encontro artisan. Asegurate de estar en la carpeta del proyecto.
    pause
    exit /b 1
)

echo.
echo 1. Ejecutando migraciones...
echo.

php artisan migrate

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Hubo un problema al ejecutar las migraciones.
    echo Verifica que tu base de datos este disponible.
    pause
    exit /b 1
)

echo.
echo 2. Limpiando cache...
echo.

php artisan optimize:clear

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Hubo un problema al limpiar el cache.
    pause
    exit /b 1
)

echo.
echo ====================================
echo SUCCESS! Migraciones completadas
echo ====================================
echo.
echo Se crearon las siguientes tablas:
echo   - registro_horas
echo   - actividades_estudiante
echo   - evidencias
echo   - certificados
echo   - observaciones_docente
echo.
echo Campos agregados a 'inscripcions':
echo   - horas_requeridas
echo   - fecha_inicio
echo   - fecha_finalizacion_estimada
echo   - certificado_aprobado
echo.
pause
