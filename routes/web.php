<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controladores base
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;

// Controladores de módulos
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\SemilleroController;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\GrupoInvestigacionController;

// Middleware de rol
use App\Http\Middleware\RoleMiddleware;
app('router')->aliasMiddleware('role', RoleMiddleware::class);

/*
|--------------------------------------------------------------------------
| PÚBLICO
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('welcome');

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD (autoredirección por rol)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) return redirect()->route('login');

    switch ($user->rol ?? $user->role ?? null) {
        case 'ADMIN':
            return redirect()->route('admin.dashboard');
        case 'INSTRUCTOR':
            return redirect()->route('instructor.dashboard');
        case 'APRENDIZ':
            return redirect()->route('aprendiz.dashboard');
        case 'LIDER_SEMILLERO':
            return redirect()->route('lidersemillero.dashboard');
        case 'LIDER_GENERAL':
        case 'LIDER GENERAL':
            return redirect()->route('lider.dashboard');
        default:
            return view('dashboard'); // opcional: genérico
    }
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PERFIL (área del usuario)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| RUTAS POR ROLES
|--------------------------------------------------------------------------
*/

// ADMIN
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::view('/admin/dashboard', 'dashboard-admin')->name('admin.dashboard');
    Route::get('/admin/funciones', [AdminController::class, 'index'])->name('admin.functions');
    Route::resource('usuarios', UsuarioController::class);
});

// INSTRUCTOR
Route::middleware(['auth', 'role:INSTRUCTOR'])->group(function () {
    Route::view('/instructor/dashboard', 'dashboard-instructor')->name('instructor.dashboard');
});

// LÍDER DE SEMILLERO
Route::middleware(['auth', 'role:LIDER_SEMILLERO'])->group(function () {
    Route::view('/lidersemillero/dashboard', 'dashboard-lider-semillero')->name('lidersemillero.dashboard');
    Route::view('/lidersemillero/proyectos', 'lidersemillero.proyectos')->name('lidersemillero.proyectos');
    Route::view('/lidersemillero/cuenta', 'lidersemillero.cuenta')->name('lidersemillero.cuenta');
});

// APRENDIZ
Route::middleware(['auth', 'role:APRENDIZ'])->group(function () {
    Route::view('/aprendiz/dashboard', 'dashboard-aprendiz')->name('aprendiz.dashboard');
    Route::view('/aprendiz/proyectos', 'aprendiz.proyectos')->name('aprendiz.proyectos');
    Route::view('/aprendiz/cuenta', 'aprendiz.cuenta')->name('aprendiz.cuenta');
});

// LÍDER GENERAL
Route::middleware(['auth', 'role:LIDER_GENERAL,LIDER GENERAL'])->group(function () {
    Route::view('/lider/dashboard', 'dashboard-lider')->name('lider.dashboard');
});

/*
|--------------------------------------------------------------------------
| MÓDULOS DEL PROYECTO (rutas que usa el menú lateral)
|--------------------------------------------------------------------------
*/

// LÍDERES (registro de aprendices líderes)
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR'])->group(function () {
    Route::resource('lideres', LiderController::class)->only(['index', 'create', 'store']);
});

// SEMILLEROS
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR,LIDER_GENERAL'])->group(function () {
    Route::resource('semilleros', SemilleroController::class)->only(['index', 'create', 'store', 'show']);
});

// APRENDICES (perfiles)
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR'])->group(function () {
    Route::resource('aprendices', AprendizController::class)->only(['index', 'create', 'store', 'show']);
});

// GRUPOS DE INVESTIGACIÓN
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR,LIDER_GENERAL'])->group(function () {
    Route::resource('grupos', GrupoInvestigacionController::class)->only(['index', 'create', 'store', 'show']);
});

/*
|--------------------------------------------------------------------------
| PASSWORD CHANGE (para el botón “Cambio contraseña”)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/password/change', function () {
    return view('auth.passwords.change');
})->name('password.change');
