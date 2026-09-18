@echo off
cd /d C:\Users\odali\Tesis\vinculacion-istam
echo Running Laravel cache and view clearing commands...
echo.
php artisan cache:clear
echo.
php artisan view:clear
echo.
php artisan optimize:clear
echo.
echo All commands completed successfully!
pause
