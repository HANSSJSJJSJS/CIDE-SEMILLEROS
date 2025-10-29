<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
// Generales
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;
// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\SemilleroController as AdminSemilleros;
// Lider semillero
use App\Http\Controllers\LiderSemillero\SemilleroController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
// Aprendiz
use App\Http\Controllers\Aprendiz\DashboardController as AprendizDashboardController;
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;
use App\Http\Controllers\Aprendiz\DocumentoController;
// Otros opcionales
use App\Http\Controllers\AdminController;

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

// ====== ADMIN (UN SOLO BLOQUE) ======
Route::middleware(['auth','role:ADMIN'])
    ->prefix('admin')->as('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ===== Usuarios =====
        Route::get('/usuarios',                [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create',         [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',               [UsuarioController::class, 'store'])->name('usuarios.store');

        // OJO: aquí llamas a editForm, entonces el controlador DEBE tener ese método
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'editForm'])->name('usuarios.edit');

        Route::put('/usuarios/{usuario}',      [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}',   [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // AJAX (firma edit($id) → por eso dejamos {id})
        Route::get('/usuarios/{id}/edit-ajax', [UsuarioController::class, 'editAjax'])->name('usuarios.edit.ajax');

        Route::post('/usuarios/ajax/store',    [UsuarioController::class, 'storeAjax'])->name('usuarios.store.ajax');

        // ===== Semilleros (ADMIN) =====
        Route::get('/semilleros/lideres-disponibles', [AdminSemilleros::class, 'lideresDisponibles'])
            ->name('semilleros.lideres-disponibles');

        Route::resource('semilleros', AdminSemilleros::class)
            ->only(['index','edit','update','destroy'])
            ->names('semilleros'); // genera admin.semilleros.index|edit|update|destroy

        // Página “Funciones del administrador” (opcional si se usa)
        Route::get('/funciones', [AdminController::class, 'index'])->name('functions');
    });

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
    Route::resource('aprendices', \App\Http\Controllers\AprendizController::class)->only(['index','create','store','show']);
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
    Route::get('/documentos', [SemilleroController::class, 'documentos'])->name('documentos');
    Route::get('/proyectos/list', [SemilleroController::class, 'listarProyectos'])->name('proyectos.list');
    Route::get('/proyectos/{proyecto}/aprendices-list', [SemilleroController::class, 'obtenerAprendicesProyecto'])->name('proyectos.aprendices.list');
    Route::post('/evidencias/store', [SemilleroController::class, 'guardarEvidencia'])->name('evidencias.store');
    Route::get('/proyectos/{proyecto}/entregas', [SemilleroController::class, 'obtenerEntregas'])->name('proyectos.entregas');
    Route::put('/entregas/{entrega}/estado', [SemilleroController::class, 'cambiarEstadoEntrega'])->name('entregas.estado');
    Route::put('/documentos/{documento}/actualizar', [SemilleroController::class, 'actualizarDocumento'])->name('documentos.actualizar');
    
    // Rutas del calendario
    Route::get('/calendario', [SemilleroController::class, 'calendario'])->name('calendario');
    Route::get('/eventos', [SemilleroController::class, 'obtenerEventos'])->name('eventos.obtener');
    Route::post('/eventos', [SemilleroController::class, 'crearEvento'])->name('eventos.crear');
    Route::put('/eventos/{evento}', [SemilleroController::class, 'actualizarEvento'])->name('eventos.actualizar');
    Route::delete('/eventos/{evento}', [SemilleroController::class, 'eliminarEvento'])->name('eventos.eliminar');
    Route::post('/eventos/{evento}/generar-enlace', [SemilleroController::class, 'generarEnlace'])->name('eventos.generar-enlace');
    Route::get('/eventos/{evento}/info', [SemilleroController::class, 'getInfoReunion'])->name('eventos.info');
    
    Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
    Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
});

Route::middleware(['auth'])->prefix('aprendiz')->name('aprendiz.')->group(function () {
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
});

// ---------------------------------------------
// RUTAS DEL MÓDULO APRENDIZ (BLOQUE CONSOLIDADO)
// ---------------------------------------------
Route::middleware(['auth', 'role:APRENDIZ'])
    ->prefix('aprendiz')
    ->name('aprendiz.')
    ->group(function () {

        // Dashboard Aprendiz
        Route::get('/dashboard', [AprendizDashboardController::class, 'index'])
            ->name('dashboard');

        // Endpoint de estadísticas del dashboard
        Route::get('/dashboard/stats', [AprendizDashboardController::class, 'stats'])
            ->name('dashboard.stats');

        // Perfil (ver y editar datos personales)
        Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
        Route::get('/perfil/edit', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::post('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');

        // Proyectos (solo ver)
        Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');

        // Archivos
        Route::resource('archivos', ArchivoController::class);

        // Subida de archivos (manual)
        Route::get('/archivos/upload', [ArchivoController::class, 'create'])->name('archivos.upload');
        Route::post('/archivos/upload', [ArchivoController::class, 'upload'])->name('archivos.upload.post');
    });

