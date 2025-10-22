<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// Controladores generales
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
// Controladores de Módulos Específicos
use App\Http\Controllers\LiderSemillero\SemilleroController;
use App\Http\Controllers\LiderSemillero\DashboardController as LiderSemilleroDashboardController; // ALIAS para Lider Semillero
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // ALIAS para Admin
use App\Http\Controllers\Aprendiz\DashboardController as AprendizDashboardController; // ALIAS para Aprendiz
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;

// ---------------------------------------------
// RUTAS PÚBLICAS
// ---------------------------------------------

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

// ====== ADMIN (BLOQUE CONSOLIDADO) ======
Route::middleware(['auth', 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); // admin.dashboard

        // Gestión de usuarios
        Route::resource('usuarios', UsuarioController::class);

        // Crear usuario (AJAX)
        Route::post('/usuarios/ajax/store', [UsuarioController::class, 'storeAjax'])->name('usuarios.store.ajax');

        // Página “Funciones del administrador”
        Route::get('/funciones', [AdminController::class, 'index'])->name('functions');

        Route::get('/crear', fn() => view('admin.crear'))->name('crear');
    });


// ---------------------------------------------
// RUTAS GENERALES AUTENTICADAS
// ---------------------------------------------

/*
|--------------------------------------------------------------------------
| RUTA /dashboard GENÉRICA (Redirige al dashboard específico de cada rol)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

    $map = [
        'ADMIN' => 'admin.dashboard',
        'INSTRUCTOR' => 'lider_semi.dashboard',
        'APRENDIZ' => 'aprendiz.dashboard',
        'LIDER_SEMILLERO' => 'lider_semi.dashboard',
        'LIDER_GENERAL' => 'lider_general.dashboard',
    ];

    $route = $map[$rol] ?? 'home';

    return redirect()->route($route); // Redirige al dashboard del rol específico
})->middleware(['auth', 'verified'])->name('dashboard'); // Nombre de la ruta genérica: 'dashboard'

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
| PASSWORD CHANGE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/password/change', function () {
    return view('auth.passwords.change');
})->name('password.change');

// ---------------------------------------------
// MÓDULOS DEL PROYECTO
// ---------------------------------------------

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


// ---------------------------------------------
// RUTAS ESPECÍFICAS POR ROL (usando los nombres de vista correctos)
// ---------------------------------------------

// INSTRUCTOR o LIDER SEMILLERO (mismo rol)
Route::middleware(['auth', 'role:LIDER SEMILLERO'])
    ->prefix('lider_semi')
    ->name('lider_semi.')
    ->group(function () {
        // CORREGIDO: Usando el alias de controlador y el nombre de vista que proporcionaste
        Route::get('/dashboard', [DashboardController_semi::class, 'index'])
             ->name('dashboard'); // lider_semi.dashboard

        Route::get('/semilleros', [SemilleroController::class, 'semilleros'])->name('semilleros');
        Route::view('/aprendices', 'lider_semi.aprendices')->name('aprendices');
        Route::view('/documentos', 'lider_semi.documentos')->name('documentos');
        Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
        Route::view('/calendario', 'lider_semi.calendario')->name('calendario');
        Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
    });

// LÍDER GENERAL (Asumiendo que 'LIDER GENERAL' es el mismo rol)
Route::middleware(['auth', 'role:LIDER GENERAL'])->group(function () {
    // CORREGIDO: Usando el nombre de vista que proporcionaste
    Route::get('/lider_general/dashboard', fn() => view('lider_general.dashboard_lider'))
        ->name('lider_general.dashboard'); // lider_general.dashboard
});


// ---------------------------------------------
// RUTAS DEL MÓDULO APRENDIZ (BLOQUE CONSOLIDADO)
// ---------------------------------------------
Route::middleware(['auth', 'role:APRENDIZ'])
    ->prefix('aprendiz')
    ->name('aprendiz.')
    ->group(function () {

        // Dashboard Aprendiz
        Route::get('/dashboard', [AprendizDashboardController::class, 'index'])->name('dashboard');

        // Perfil (ver datos personales)
        Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
        Route::get('/perfil/edit', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::post('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');

        // Proyectos (solo ver)
        Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');

        // Archivos
        Route::resource('archivos', ArchivoController::class);

        // Rutas específicas de Archivos (si necesitas sobrescribir o añadir)
        Route::get('/archivos/upload', [ArchivoController::class, 'create'])->name('archivos.upload');
        Route::post('/archivos/upload', [ArchivoController::class, 'upload'])->name('archivos.upload.post');
    });
