$projectPath = "C:\Users\odali\Tesis\vinculacion-istam"

# Navigate to project directory and run artisan commands
Set-Location $projectPath
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

Write-Host "Laravel cache, views, and optimization cleared successfully!"
Read-Host "Press Enter to exit"
