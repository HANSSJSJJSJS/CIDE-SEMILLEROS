<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
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

// (Ejemplo) Controladores de tus módulos
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

    // Ajusta los nombres a los que maneje tu RoleMiddleware/base de datos
    switch ($user->rol ?? $user->role ?? null) {
        case 'ADMIN':
            return redirect()->route('admin.dashboard');
        case 'INSTRUCTOR':
            return redirect()->route('instructor.dashboard');
        case 'APRENDIZ':
            return redirect()->route('aprendiz.dashboard');
        case 'LIDER_GENERAL': // si hoy lo tienes con espacio, cámbialo también en BD
        case 'LIDER GENERAL':
            return redirect()->route('lider.dashboard');
        default:
            // Rol no contemplado: envía a un dashboard genérico o error
            return view('dashboard'); // opcional
    }
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PERFIL (área del usuario)
|--------------------------------------------------------------------------
*/
>>>>>>> dfda83aa73b75a9470edde3fb32dc68a154c263b
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

/*
|--------------------------------------------------------------------------
| RUTAS POR ROLES
|--------------------------------------------------------------------------
*/
// ADMIN
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::view('/admin/dashboard', 'dashboard-admin')->name('admin.dashboard');

    // Usuarios (solo admin)
    Route::resource('usuarios', UsuarioController::class);

    // Página “Funciones del administrador” para el botón del menú
    Route::get('/admin/funciones', [AdminController::class, 'index'])->name('admin.functions');
});

// INSTRUCTOR
Route::middleware(['auth', 'role:INSTRUCTOR'])->group(function () {
    Route::view('/instructor/dashboard', 'dashboard-instructor')->name('instructor.dashboard');
});

// LÍDER DE SEMILLERO
Route::middleware(['auth', 'role:LIDER_SEMILLERO'])->group(function () {
    Route::view('/lider_semi/dashboard', 'lider_semi.dashboard-instructor')->name('lider_semi.instructor.dashboard');
});

// APRENDIZ
Route::middleware(['auth', 'role:APRENDIZ'])->group(function () {
    Route::view('/aprendiz/dashboard', 'dashboard-aprendiz')->name('aprendiz.dashboard');
});

// LÍDER GENERAL
Route::middleware(['auth', 'role:LIDER_GENERAL'])->group(function () {
    Route::view('/lider/dashboard', 'dashboard-lider')->name('lider.dashboard');
});







/*
|--------------------------------------------------------------------------
| MÓDULOS DEL PROYECTO (rutas que usa el menú lateral)
|--------------------------------------------------------------------------
| Si aún no tienes controladores, puedes dejar Route::view temporalmente.
| Cuando los tengas, reemplazas por controladores reales como abajo.
*/

// LÍDERES (registro de aprendices líderes)
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR'])->group(function () {
    Route::resource('lideres', LiderController::class)->only(['index','create','store']);
});

// SEMILLEROS
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR,LIDER_GENERAL'])->group(function () {
    Route::resource('semilleros', SemilleroController::class)->only(['index','create','store','show']);
});

// APRENDICES (perfiles)
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR'])->group(function () {
    Route::resource('aprendices', AprendizController::class)->only(['index','create','store','show']);
});

// GRUPOS DE INVESTIGACIÓN
Route::middleware(['auth', 'role:ADMIN,INSTRUCTOR,LIDER_GENERAL'])->group(function () {
    Route::resource('grupos', GrupoInvestigacionController::class)->only(['index','create','store','show']);
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

Route::middleware(['auth'])->group(function () {
    Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class)->only(['index']);
    Route::resource('semilleros', \App\Http\Controllers\SemilleroController::class)->only(['index']);
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class,'edit'])->name('profile.edit');
});

