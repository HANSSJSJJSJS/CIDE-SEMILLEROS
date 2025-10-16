<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UsuarioController;

// ---------------------------------------------
// RUTAS PÚBLICAS
// ---------------------------------------------
Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// ---------------------------------------------
// RUTAS GENERALES (acceso con login)
// ---------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de usuarios (solo si aplica)
    Route::resource('usuarios', UsuarioController::class);
});

// ---------------------------------------------
// RUTAS POR ROLES
// ---------------------------------------------
 
// ADMINISTRADOR
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.admin-dashboard'))->name('admin.dashboard');
    Route::get('/admin/crear', fn() => view('admin.crear'))->name('admin.crear');
});

// INSTRUCTOR o LIDER SEMILLERO (mismo rol)
Route::middleware(['auth', 'role:LIDER SEMILLERO'])->group(function () {
    Route::get('/lider_semi/dashboard', fn() => view('lider_semi.dashboard-instructor'))->name('lider_semi.dashboard');
});

// LÍDER GENERAL
Route::middleware(['auth', 'role:LIDER GENERAL'])->group(function () {
    Route::get('/lider_general/dashboard', fn() => view('lider_general.dashboard-lider'))->name('lider_general.dashboard');
});

// APRENDIZ
Route::middleware(['auth', 'role:APRENDIZ'])->group(function () {
    Route::get('/aprendiz/dashboard', fn() => view('aprendiz.dashboard-aprendiz'))->name('aprendiz.dashboard');
});

// ---------------------------------------------
// AUTENTICACIÓN (Laravel Breeze / Fortify)
// ---------------------------------------------
require __DIR__.'/auth.php';
