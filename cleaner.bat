@echo off
title Laravel Optimizer Interactivo
color 0A

:MENU
cls
echo ===========================================
echo           Laravel Optimizer Interactivo
echo ===========================================
echo.
echo 1. Limpiar y optimizar Laravel
echo 2. Composer dump-autoload (opcional)
echo 3. npm run dev (opcional)
echo 4. Ejecutar TODO
echo 5. Salir
echo.
set /p OPTION="Elige una opcion [1-5]: "

if "%OPTION%"=="1" goto LARAVEL
if "%OPTION%"=="2" goto COMPOSER
if "%OPTION%"=="3" goto NPM
if "%OPTION%"=="4" goto TODO
if "%OPTION%"=="5" goto FIN

echo Opcion invalida.
pause
goto MENU

:LARAVEL
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
echo.
echo ✅ Laravel optimizado.
pause
goto MENU

:COMPOSER
cls
color 0B
echo ===========================================
echo        Ejecutando composer dump-autoload...
echo ===========================================
composer dump-autoload
echo.
echo ✅ Composer dump-autoload completado.
pause
goto MENU

:NPM
cls
color 0B
echo ===========================================
echo          Ejecutando npm run dev...
echo ===========================================
npm run dev
echo.
echo ✅ npm run dev completado.
pause
goto MENU

:TODO
call :LARAVEL
call :COMPOSER
call :NPM
goto MENU

:FIN
echo.
echo Gracias por usar Laravel Optimizer.
pause
exit
