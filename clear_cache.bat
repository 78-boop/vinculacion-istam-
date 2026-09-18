@echo off
cd /d "C:\Users\odali\Tesis\vinculacion-istam"
echo Running Laravel cache clear commands...
echo.
echo 1. Clearing application cache...
php artisan cache:clear
if %errorlevel% neq 0 (
    echo ERROR: cache:clear failed
    pause
    exit /b 1
)
echo.
echo 2. Clearing configuration cache...
php artisan config:clear
if %errorlevel% neq 0 (
    echo ERROR: config:clear failed
    pause
    exit /b 1
)
echo.
echo 3. Clearing view cache...
php artisan view:clear
if %errorlevel% neq 0 (
    echo ERROR: view:clear failed
    pause
    exit /b 1
)
echo.
echo 4. Clearing optimization cache...
php artisan optimize:clear
if %errorlevel% neq 0 (
    echo ERROR: optimize:clear failed
    pause
    exit /b 1
)
echo.
echo ===== ALL CACHE COMMANDS COMPLETED SUCCESSFULLY =====
pause
