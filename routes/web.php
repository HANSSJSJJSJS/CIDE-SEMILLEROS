<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;

// ---------------------------------------------
// RUTAS PÚBLICAS
// ---------------------------------------------

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// ---------------------------------------------
// RUTAS GENERALES (acceso con login)
// ---------------------------------------------

// Controladores de tus módulos
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\LiderSemillero\SemilleroController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;
// Controladores Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;     // <-- el del store del modal
use App\Http\Controllers\UsuarioController;        // <-- tu listado (si NO está en Admin) controlador de gestión de usuarios en el panel
use App\Http\Controllers\Auth\AuthenticatedSessionController;


/*
|--------------------------------------------------------------------------
| PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

// ====== ADMIN (UN SOLO BLOQUE) ======
Route::middleware(['auth', 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin (una sola vez)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Gestión de usuarios (listado)
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

        // Crear usuario (form tradicional si lo usas)
        Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');

        // Crear usuario (AJAX)
        Route::post('/usuarios/ajax/store', [UsuarioController::class, 'storeAjax'])->name('usuarios.store.ajax');
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

    switch ($user->rol ?? $user->role ?? null) {
        case 'ADMIN':
            return view('Admin.dashboard-admin'); // Vista en carpeta Admin
        case 'INSTRUCTOR':
            return view('Instructor.dashboard-instructor'); // Vista en carpeta Instructor
        case 'APRENDIZ':
            return view('Aprendiz.dashboard-aprendiz'); // Vista en carpeta Aprendiz
        case 'LIDER_GENERAL':
        case 'LIDER GENERAL':
            return view('Lider.dashboard-lider'); // Vista en carpeta Lider
        default:
            return view('dashboard'); // Vista genérica o error
    }
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PERFIL (área del usuario)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Ya tienes el dashboard directo arriba, así que no repitas esta ruta
    // Eliminar: Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

Route::middleware('auth')->group(function () {
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
// Ya no necesitas rutas separadas para dashboards, lo manejas desde /dashboard

// Resto de rutas por rol para otros módulos

// ADMIN
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/crear', fn() => view('admin.crear'))->name('admin.crear');

    // Usuarios (solo admin)
    Route::resource('usuarios', UsuarioController::class);

    // Página “Funciones del administrador” para el botón del menú
    Route::get('/admin/funciones', [AdminController::class, 'index'])->name('admin.functions');
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

// Rutas para líder-semillero
Route::middleware(['auth', 'lider.semillero'])->prefix('lider_semi')->name('lider_semi.')->group(function () {
    Route::get('/dashboard', [DashboardController_semi::class, 'index'])->name('dashboard');
    Route::get('/semilleros', [SemilleroController::class, 'semilleros'])->name('semilleros');
    Route::view('/aprendices', 'lider_semi.aprendices')->name('aprendices');
    Route::view('/documentos', 'lider_semi.documentos')->name('documentos');
    Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
    Route::view('/calendario', 'lider_semi.calendario')->name('calendario');
    Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
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
// RUTAS DEL MÓDULO APRENDIZ
// ---------------------------------------------

use App\Http\Controllers\Aprendiz\DashboardController;
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;

Route::middleware(['auth', 'role:APRENDIZ'])->prefix('aprendiz')->name('aprendiz.')->group(function () {
    // Dashboard Aprendiz
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (ver datos personales)
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
    Route::get('/perfil/edit', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::post('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');

    // Proyectos (solo ver)
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');

    // Archivos: ver lista, descargar y subir PDFs
    Route::get('/archivos', [ArchivoController::class, 'index'])->name('archivos.index');
    Route::get('/archivos/upload', [ArchivoController::class, 'create'])->name('archivos.upload');
    Route::post('/archivos/upload', [ArchivoController::class, 'upload'])->name('archivos.upload.post');

    Route::resource('archivos', ArchivoController::class);
});
