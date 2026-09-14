@echo off
title Laravel Optimizer-ALL

cls
color 0B
echo ===========================================
echo        Limpiando y optimizando Laravel...
echo ===========================================
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan clear-compiled
php artisan optimize:clear
npm run dev
echo.
echo ✅ Laravel optimizado.
echo Gracias por usar Laravel Optimizer-ALL.

