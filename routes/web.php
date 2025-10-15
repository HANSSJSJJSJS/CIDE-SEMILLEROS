<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UsuarioController;


use App\Http\Middleware\RoleMiddleware;


app('router')->aliasMiddleware('role', RoleMiddleware::class);


Route::resource('usuarios', UsuarioController::class);

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- RUTAS POR ROLES Y DASHBOARDS ---
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.dashboard-admin'))->name('admin.dashboard');
});

Route::middleware(['auth', 'role:INSTRUCTOR'])->group(function () {
    Route::get('/lider_semi/dashboard', fn() => view('lider_semi.dashboard-instructor'))->name('lider_semi.instructor.dashboard');
});

Route::middleware(['auth', 'role:APRENDIZ'])->group(function () {
    Route::get('/aprendiz/dashboard', fn() => view('aprendiz.dashboard-aprendiz'))->name('aprendiz.aprendiz.dashboard');
});

Route::middleware(['auth', 'role:LIDER GENERAL'])->group(function () {
    Route::get('/lider_general/dashboard', fn() => view('lider_general.dashboard-lider'))->name('lider_general.lider.dashboard');
});
// --- FIN RUTAS POR ROLES Y DASHBOARDS ---
Route::get('/admin/crear', function () {
    return view('Admin.crear');
});

Route::get('/admin/crear', function () {
    return view('Admin.crear');
});


require __DIR__.'/auth.php';

