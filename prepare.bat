@echo off
cd /d "C:\Users\odali\Tesis\vinculacion-istam"
php artisan cache:clear
php artisan config:cache
echo.
echo Cache operations completed!
