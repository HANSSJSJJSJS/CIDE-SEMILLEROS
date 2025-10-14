<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UsuarioController;




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
    Route::get('/admin/dashboard', fn() => view('dashboard-admin'))->name('admin.dashboard');
});

Route::middleware(['auth', 'role:INSTRUCTOR'])->group(function () {
    Route::get('/instructor/dashboard', fn() => view('dashboard-instructor'))->name('instructor.dashboard');
});

Route::middleware(['auth', 'role:APRENDIZ'])->group(function () {
    Route::get('/aprendiz/dashboard', fn() => view('dashboard-aprendiz'))->name('aprendiz.dashboard');
});

Route::middleware(['auth', 'role:LIDER GENERAL'])->group(function () {
    Route::get('/lider/dashboard', fn() => view('dashboard-lider'))->name('lider.dashboard');
});
// --- FIN RUTAS POR ROLES Y DASHBOARDS ---
Route::get('/admin/crear', function () {
    return view('Admin.crear');
});

Route::get('/admin/crear', function () {
    return view('Admin.crear');
});


require __DIR__.'/auth.php';

