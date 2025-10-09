@echo off
title Instalación de dependencias Laravel - CIDE_SENA
echo ===============================================
echo  🚀 Iniciando instalación de dependencias...
echo ===============================================
echo.

REM ---- DEPENDENCIAS PHP ----
composer require laravel/sanctum spatie/laravel-permission
composer require barryvdh/laravel-debugbar --dev

REM ---- DEPENDENCIAS FRONTEND ----
npm install bootstrap axios sweetalert2
npm install vite laravel-vite-plugin sass postcss autoprefixer --save-dev

REM ---- LIMPIEZA DE CACHÉS ----
php artisan optimize:clear

echo.
echo ===============================================
echo ✅ Instalación finalizada correctamente.
echo ===============================================
pause