<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\LiderSemillero\SemilleroController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;



/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS / AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'))->name('welcome');

Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| DASHBOARD POR ROL (vistas según archivos que tienes)
|--------------------------------------------------------------------------
*/

//admin
Route::middleware(['auth','role:ADMIN'])->group(function () {
    // resources/views/admin/admin-dashboard.blade.php
    Route::get('/admin/dashboard', fn() => view('admin.dashboard-admin'))->name('admin.dashboard');
    Route::get('/admin/crear', fn() => view('admin.crear'))->name('admin.crear');
    Route::get('/admin/funciones', [AdminController::class, 'index'])->name('admin.functions');
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('users.index');

});

Route::middleware(['auth','verified'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });





//fin admin 



Route::middleware(['auth','role:APRENDIZ'])->group(function () {
    // [dashboard-aprendiz.blade.php](http://_vscodecontentref_/2)
    Route::get('/aprendiz/dashboard', fn() => view('aprendiz.dashboard-aprendiz'))->name('aprendiz.dashboard');
});

Route::middleware(['auth','role:LIDER_GENERAL'])->group(function () {
    // [dashboard-lider.blade.php](http://_vscodecontentref_/3)
    Route::get('/lider/dashboard', fn() => view('lider_general.dashboard-lider'))->name('lider.dashboard');
});

/*
|--------------------------------------------------------------------------
| RUTA /dashboard GENÉRICA (fallback que redirige según rol si quieres)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

    return match ($rol) {
        'ADMIN' => redirect()->route('admin.dashboard'),
        'INSTRUCTOR' => redirect()->route('lider_semi.dashboard'),
        'APRENDIZ' => redirect()->route('aprendiz.dashboard'),
        'LIDER_SEMILLERO' => redirect()->route('lider_semi.dashboard'),
        'LIDER_GENERAL' => redirect()->route('lider.dashboard'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PERFIL (área del usuario)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de usuarios (solo si aplica)
    Route::resource('usuarios', UsuarioController::class);
});


// ---------------------------------------------
// AUTENTICACIÓN (Laravel Breeze / Fortify)
// ---------------------------------------------
 



/*
|--------------------------------------------------------------------------
| MÓDULOS DEL PROYECTO (rutas que usa el menú lateral)
|--------------------------------------------------------------------------
| Si aún no tienes controladores, puedes dejar Route::view temporalmente.
| Cuando los tengas, reemplazas por controladores reales como abajo.
*/

// LÍDERES (registro de aprendices líderes)
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO'])->group(function () {
    Route::resource('lideres', LiderController::class)->only(['index','create','store']);
});

// SEMILLEROS
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO,LIDER_GENERAL'])->group(function () {
    Route::resource('semilleros', SemilleroController::class)->only(['index','create','store','show']);
});

// APRENDICES (perfiles)
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO'])->group(function () {
    Route::resource('aprendices', AprendizController::class)->only(['index','create','store','show']);
});

// GRUPOS DE INVESTIGACIÓN
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO,LIDER_GENERAL'])->group(function () {
    Route::resource('grupos', GrupoInvestigacionController::class)->only(['index','create','store','show']);
});

// Rutas para líder-semillero
Route::middleware(['auth','lider.semillero'])->prefix('lider_semi')->name('lider_semi.')->group(function () {
    Route::get('/dashboard', [DashboardController_semi::class, 'index'])->name('dashboard');
    Route::get('/semilleros', [SemilleroController::class, 'semilleros'])->name('semilleros');
    Route::get('/aprendices', [SemilleroController::class, 'aprendices'])->name('aprendices');
    // Gestión de aprendices por semillero
    Route::get('/semilleros/{semillero}/aprendices', [SemilleroController::class, 'editAprendices'])->name('semilleros.aprendices.edit');
    Route::put('/semilleros/{semillero}/aprendices', [SemilleroController::class, 'updateAprendices'])->name('semilleros.aprendices.update');
    Route::get('/semilleros/{semillero}/aprendices/search', [SemilleroController::class, 'searchAprendices'])->name('semilleros.aprendices.search');
    Route::post('/semilleros/{semillero}/aprendices/attach', [SemilleroController::class, 'attachAprendiz'])->name('semilleros.aprendices.attach');
    Route::delete('/semilleros/{semillero}/aprendices/{aprendiz}', [SemilleroController::class, 'detachAprendiz'])->name('semilleros.aprendices.detach');
    Route::post('/semilleros/{semillero}/aprendices/create', [SemilleroController::class, 'createAndAttachAprendiz'])->name('semilleros.aprendices.create');

    // Gestión de aprendices por proyecto
    Route::get('/proyectos/{proyecto}/aprendices', [SemilleroController::class, 'editProyectoAprendices'])->name('proyectos.aprendices.edit');
    Route::put('/proyectos/{proyecto}/aprendices', [SemilleroController::class, 'updateProyectoAprendices'])->name('proyectos.aprendices.update');
    Route::get('/proyectos/{proyecto}/aprendices/search', [SemilleroController::class, 'searchProyectoAprendices'])->name('proyectos.aprendices.search');
    Route::post('/proyectos/{proyecto}/aprendices/attach', [SemilleroController::class, 'attachProyectoAprendiz'])->name('proyectos.aprendices.attach');
    Route::delete('/proyectos/{proyecto}/aprendices/{aprendiz}', [SemilleroController::class, 'detachProyectoAprendiz'])->name('proyectos.aprendices.detach');
    Route::post('/proyectos/{proyecto}/aprendices/create', [SemilleroController::class, 'createAndAttachProyectoAprendiz'])->name('proyectos.aprendices.create');
    Route::view('/documentos', 'lider_semi.documentos')->name('documentos');
    Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
    Route::view('/calendario', 'lider_semi.calendario')->name('calendario');
    Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
});

/*
|--------------------------------------------------------------------------
| PASSWORD CHANGE (para el botón “Cambio contraseña”)
|--------------------------------------------------------------------------
| Si usas Fortify o Jetstream, ajusta esta ruta a tu flujo real.
*/
Route::middleware('auth')->get('/password/change', function () {
    return view('auth.passwords.change'); // crea esta vista o apunta al form real
})->name('password.change');

