<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\AprendizController;
use App\Http\Controllers\LiderSemillero\SemilleroController;
use App\Http\Controllers\LiderSemillero\PerfilController as LiderSemiPerfilController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;
// Controladores Admin
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController; // 
use App\Http\Controllers\Admin\SemilleroController as AdminSemilleroController; 
use App\Http\Controllers\Admin\SemilleroController as AdminSemilleros;




// ====== PÚBLICAS / AUTH ======
Route::get('/', fn() => view('welcome'))->name('welcome');

Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

require __DIR__.'/auth.php';

// ====== ADMIN (UN SOLO BLOQUE) ======
Route::middleware(['auth','role:ADMIN'])
    ->prefix('admin')->as('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ===== Usuarios =====
        Route::get('/usuarios',                [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create',         [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',               [UsuarioController::class, 'store'])->name('usuarios.store');

        // OJO: aquí llamas a editForm, entonces el controlador DEBE tener ese método
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'editForm'])->name('usuarios.edit');

        Route::put('/usuarios/{usuario}',      [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}',   [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // AJAX (firma edit($id) → por eso dejamos {id})
        Route::get('/usuarios/{id}/edit-ajax', [UsuarioController::class, 'edit'])->name('usuarios.edit.ajax');

        Route::post('/usuarios/ajax/store',    [UsuarioController::class, 'storeAjax'])->name('usuarios.store.ajax');

        // ===== Semilleros (ADMIN) =====
        Route::get('/semilleros/lideres-disponibles', [AdminSemilleros::class, 'lideresDisponibles'])
            ->name('semilleros.lideres-disponibles');

        Route::resource('semilleros', AdminSemilleros::class)
            ->only(['index','store','edit','update','destroy'])
            ->names('semilleros'); // genera admin.semilleros.index|edit|update|destroy

                // routes/web.php (solo para probar)
                Route::get('/semilleros/{id}', [AdminSemilleros::class, 'show'])
    ->name('semilleros.show');




    });


// fin admin 



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
Route::get('/admin/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('admin.usuarios.edit');
Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');



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
    Route::put('/perfil/contacto', [LiderSemiPerfilController::class, 'updateContacto'])->name('perfil.contacto.update');
    Route::put('/perfil/password', [LiderSemiPerfilController::class, 'updatePassword'])->name('perfil.password.update');
});

// Rutas para aprendices
use App\Http\Controllers\Aprendiz\DocumentoController;

Route::middleware(['auth'])->prefix('aprendiz')->name('aprendiz.')->group(function () {
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
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


// Crear usuario (AJAX)
Route::post('/usuarios/ajax/store', [UsuarioController::class, 'storeAjax'])
    ->name('usuarios.store.ajax');