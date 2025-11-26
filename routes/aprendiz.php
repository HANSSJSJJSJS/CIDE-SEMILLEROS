<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController as AprendizProyectoController;
use App\Http\Controllers\ArchivoController;

// Rutas para el módulo Aprendiz
Route::middleware(['auth'])->group(function () {
    Route::get('/aprendiz/dashboard', [AprendizController::class, 'dashboard'])->name('aprendiz.dashboard');
    Route::get('/aprendiz/perfil', [PerfilController::class, 'show'])->name('aprendiz.perfil');

    Route::prefix('aprendiz/proyectos')->group(function () {
        Route::get('/', [AprendizProyectoController::class, 'index'])->name('aprendiz.proyectos.index');
        Route::get('/{proyecto}', [AprendizProyectoController::class, 'show'])->name('aprendiz.proyectos.show');
    });

    Route::prefix('aprendiz/archivos')->group(function () {
        Route::get('/', [ArchivoController::class, 'index'])->name('aprendiz.archivos.index');
        Route::get('/{archivo}', [ArchivoController::class, 'show'])->name('aprendiz.archivos.show');
    });
});
