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
use App\Http\Controllers\LiderSemillero\DashboardController as LiderSemilleroDashboardController; // Alias para evitar conflicto
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Alias para evitar conflicto
use App\Http\Controllers\Aprendiz\DashboardController as AprendizDashboardController; // Alias para evitar conflicto
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;

// ---------------------------------------------
// RUTAS PÚBLICAS
// ---------------------------------------------

Route::get('/', function () {
    // Si el usuario ya está autenticado, lo envía a su dashboard (ruta 'dashboard')
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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestión de usuarios: Usa Route::resource para todas las rutas RESTful
        Route::resource('usuarios', UsuarioController::class);

        // Crear usuario (AJAX) - Si necesitas una ruta especial diferente al store de resource
        Route::post('/usuarios/ajax/store', [UsuarioController::class, 'storeAjax'])->name('usuarios.store.ajax');

        // Página “Funciones del administrador”
        Route::get('/funciones', [AdminController::class, 'index'])->name('functions');

        // Ruta de ejemplo
        Route::get('/crear', fn() => view('admin.crear'))->name('crear');
    });


// ---------------------------------------------
// RUTAS GENERALES AUTENTICADAS
// ---------------------------------------------

/*
|--------------------------------------------------------------------------
| RUTA /dashboard GENÉRICA (fallback que redirige según rol)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    switch ($user->rol ?? $user->role ?? null) {
        case 'ADMIN':
            return view('Admin.dashboard-admin');
        case 'INSTRUCTOR': // Asumiendo que es un rol válido
            return view('Instructor.dashboard-instructor');
        case 'APRENDIZ':
            return view('Aprendiz.dashboard-aprendiz');
        case 'LIDER_GENERAL':
        case 'LIDER GENERAL':
            return view('Lider.dashboard-lider');
        default:
            return view('dashboard');
    }
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

    // Ruta de recursos para usuarios (Si la usan roles NO-ADMIN)
    // Route::resource('usuarios', UsuarioController::class); // Descomentar solo si aplica a NO-ADMIN
});

/*
|--------------------------------------------------------------------------
| PASSWORD CHANGE (para el botón “Cambio contraseña”)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/password/change', function () {
    return view('auth.passwords.change');
})->name('password.change');

// ---------------------------------------------
// MÓDULOS DEL PROYECTO (rutas que usa el menú lateral)
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
// RUTAS ESPECÍFICAS POR ROL (usando los alias de Dashboard)
// ---------------------------------------------

// INSTRUCTOR o LIDER SEMILLERO (mismo rol)
Route::middleware(['auth', 'role:LIDER SEMILLERO'])
    ->prefix('lider_semi')
    ->name('lider_semi.')
    ->group(function () {
        Route::get('/semilleros', [SemilleroController::class, 'semilleros'])->name('semilleros');
        Route::view('/aprendices', 'lider_semi.aprendices')->name('aprendices');
        Route::view('/documentos', 'lider_semi.documentos')->name('documentos');
        Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
        Route::view('/calendario', 'lider_semi.calendario')->name('calendario');
        Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
    });

// LÍDER GENERAL (Asumiendo que 'LIDER GENERAL' es el mismo rol)
Route::middleware(['auth', 'role:LIDER GENERAL'])->group(function () {
    Route::get('/lider_general/dashboard', fn() => view('lider_general.dashboard-lider'))->name('lider_general.dashboard');
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

        // Archivos: Route::resource crea index, store, update, destroy, etc.
        Route::resource('archivos', ArchivoController::class);

        // Rutas específicas de Archivos (si necesitas sobrescribir o añadir)
        Route::get('/archivos/upload', [ArchivoController::class, 'create'])->name('archivos.upload');
        Route::post('/archivos/upload', [ArchivoController::class, 'upload'])->name('archivos.upload.post');
    });
