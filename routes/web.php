<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;

// Controladores generales
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiderController;
use App\Http\Controllers\GrupoInvestigacionController;

// Admin (alias correctos)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Admin\PerfilController as AdminPerfilController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;

use App\Http\Controllers\Admin\SemilleroController;              // ← sin alias
use App\Http\Controllers\Admin\ProyectoSemilleroController;
use App\Http\Controllers\Admin\ReunionesLideresController;
use App\Http\Controllers\Admin\RecursoController;

// Líder semillero
use App\Http\Controllers\LiderSemillero\SemilleroController as LiderSemilleroUIController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\LiderSemillero\ProyectoController as LiderProyectoController;
use App\Http\Controllers\LiderSemillero\SemilleroAprendizController;
use App\Http\Controllers\LiderSemillero\PerfilController as LiderPerfilController;

// Aprendiz
use App\Http\Controllers\Aprendiz\DashboardController as AprendizDashboardController;
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;
use App\Http\Controllers\Aprendiz\DocumentoController;
use App\Http\Controllers\Aprendiz\CalendarioController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| JSON LÍDER SEMILLERO – PROYECTOS (compatibilidad con modales existentes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Por proyecto (JSON)
    Route::get('/lider_semillero/proyectos/listado', [LiderProyectoController::class, 'listadoJson'])
        ->name('lider_semi.proyectos.listado');
    Route::get('/lider_semillero/proyectos/{id}', [LiderProyectoController::class, 'showJson'])
        ->whereNumber('id')->name('lider_semi.proyectos.show');
    Route::get('/lider_semillero/proyectos/{id}/participantes', [LiderProyectoController::class, 'participantesJson'])
        ->whereNumber('id')->name('lider_semi.proyectos.participantes');
    Route::post('/lider_semillero/proyectos/{id}/participantes', [LiderProyectoController::class, 'assignParticipant'])
        ->whereNumber('id')->name('lider_semi.proyectos.participantes.assign');
    Route::delete('/lider_semillero/proyectos/{id}/participantes/{user}', [LiderProyectoController::class, 'removeParticipant'])
        ->whereNumber('id')->whereNumber('user')->name('lider_semi.proyectos.participantes.remove');
    Route::get('/lider_semillero/proyectos/{id}/candidatos', [LiderProyectoController::class, 'candidatosJson'])
        ->whereNumber('id')->name('lider_semi.proyectos.candidatos');

    // Compatibilidad con modal en views/lider_semi/semilleros.blade.php
    Route::get('/lider_semillero/proyectos/{proyecto}/aprendices/search', [LiderProyectoController::class, 'searchAprendices'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.search');
    Route::post('/lider_semillero/proyectos/{proyecto}/aprendices', [LiderProyectoController::class, 'assignParticipant'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.attach');
    Route::delete('/lider_semillero/proyectos/{proyecto}/aprendices/{aprendiz}', [LiderProyectoController::class, 'removeParticipant'])
        ->whereNumber('proyecto')->whereNumber('aprendiz')->name('lider_semi.proyectos.aprendices.detach');
    Route::put('/lider_semillero/proyectos/{proyecto}/aprendices', [LiderProyectoController::class, 'updateParticipants'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.update');

    // Versión por Semillero → deriva a proyecto activo
    Route::get('/lider_semillero/semilleros/{semillero}/aprendices/search', [SemilleroAprendizController::class, 'search'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.search');
    Route::post('/lider_semillero/semilleros/{semillero}/aprendices', [SemilleroAprendizController::class, 'attach'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.attach');
    Route::delete('/lider_semillero/semilleros/{semillero}/aprendices/{aprendiz}', [SemilleroAprendizController::class, 'detach'])
        ->whereNumber('semillero')->whereNumber('aprendiz')->name('lider_semi.semilleros.aprendices.detach');
    Route::put('/lider_semillero/semilleros/{semillero}/aprendices', [SemilleroAprendizController::class, 'update'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.update');
});

// ======================================================
//                  RUTAS ADMIN
// ======================================================
Route::middleware(['auth', 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats',  [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/charts', [AdminDashboardController::class, 'charts'])->name('dashboard.charts');

        // USUARIOS
        Route::resource('usuarios', AdminUsuarioController::class);
        Route::get('/usuarios/{id}/edit-ajax', [AdminUsuarioController::class, 'editAjax'])
            ->whereNumber('id')->name('usuarios.edit.ajax');
        Route::post('/usuarios/ajax/store', [AdminUsuarioController::class, 'storeAjax'])
            ->name('usuarios.store.ajax');

        // FUNCIONES ADMIN
        Route::get('/funciones', [AdminController::class, 'index'])->name('functions');
        Route::get('/crear', fn () => view('admin.crear'))->name('crear');

        // SEMILLEROS
        Route::get('/semilleros/lideres-disponibles', [SemilleroController::class, 'lideresDisponibles'])
            ->name('semilleros.lideres-disponibles');

        Route::resource('semilleros', SemilleroController::class);
   // PROYECTOS POR SEMILLERO
Route::prefix('semilleros')->name('semilleros.')->group(function () {

    // Listar y crear
    Route::get('{semillero}/proyectos',  [ProyectoSemilleroController::class, 'index'])
        ->name('proyectos.index');
    Route::post('{semillero}/proyectos', [ProyectoSemilleroController::class, 'store'])
        ->name('proyectos.store');

    // Anidadas con pertenencia
    Route::scopeBindings()->group(function () {
        Route::get('{semillero}/proyectos/{proyecto}/json',
            [ProyectoSemilleroController::class,'editAjax']
        )->name('proyectos.edit.json');

        Route::get('{semillero}/proyectos/{proyecto}/detalle',
            [ProyectoSemilleroController::class, 'detalle']
        )->name('proyectos.detalle');

        Route::put('{semillero}/proyectos/{proyecto}',
            [ProyectoSemilleroController::class, 'update']
        )->name('proyectos.update');

        Route::delete('{semillero}/proyectos/{proyecto}',
            [ProyectoSemilleroController::class, 'destroy']
        )->name('proyectos.destroy');
        // Descargar documento
        Route::get('{semillero}/proyectos/{proyecto}/docs/{doc}',
            [ProyectoSemilleroController::class, 'download']
        )->name('proyectos.docs.download');

    });
});



        // REUNIONES DE LÍDERES
        Route::get('/reuniones-lideres', [ReunionesLideresController::class, 'index'])
            ->name('reuniones-lideres.index');

        Route::prefix('reuniones-lideres')->as('reuniones-lideres.')->group(function () {
            Route::get('obtener',    [ReunionesLideresController::class, 'obtener'])->name('obtener');
            Route::get('semilleros', [ReunionesLideresController::class, 'semilleros'])->name('semilleros');
            Route::get('lideres',    [ReunionesLideresController::class, 'lideres'])->name('lideres');
            Route::post('',          [ReunionesLideresController::class, 'store'])->name('store');
            Route::put('{id}',       [ReunionesLideresController::class, 'update'])->whereNumber('id')->name('update');
            Route::delete('{id}',    [ReunionesLideresController::class, 'destroy'])->whereNumber('id')->name('destroy');
            Route::post('{id}/generar-enlace', [ReunionesLideresController::class, 'generarEnlace'])
                ->whereNumber('id')->name('generar-enlace');
        });

        // RECURSOS
        Route::prefix('recursos')->as('recursos.')->group(function () {
            Route::get('/',               [RecursoController::class, 'index'])->name('index');
            Route::get('/listar',         [RecursoController::class, 'listar'])->name('listar');
            Route::post('/',              [RecursoController::class, 'store'])->name('store');
            Route::get('/{recurso}/dl',   [RecursoController::class, 'download'])->name('download');
            Route::delete('/{recurso}',   [RecursoController::class, 'destroy'])->name('destroy');
        });

        // PERFIL (ADMIN)
        Route::prefix('perfil')->as('perfil.')->group(function () {
            Route::get('/',         [AdminPerfilController::class, 'edit'])->name('edit');
            Route::put('/',         [AdminPerfilController::class, 'update'])->name('update');
            Route::put('/password', [AdminPerfilController::class, 'updatePassword'])->name('password.update');
        });

        // NOTIFICACIONES
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.read_all');
    });


/*
|--------------------------------------------------------------------------
| RUTA /dashboard GENÉRICA → redirige por rol
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) return redirect()->route('login');

    $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

    $map = [
        'ADMIN'          => 'admin.dashboard',
        'INSTRUCTOR'     => 'lider_semi.dashboard',
        'APRENDIZ'       => 'aprendiz.dashboard',
        'LIDER_SEMILLERO'=> 'lider_semi.dashboard',
        'LIDER_GENERAL'  => 'lider_general.dashboard',
    ];

    $route = $map[$rol] ?? 'home';
    return redirect()->route($route);
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
| LÍDERES / SEMILLEROS / GRUPOS (generales)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO'])->group(function () {
    Route::resource('lideres', \App\Http\Controllers\AprendizController::class)->only(['index','create','store']); // si es otro controller, cámbialo
});

Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO,LIDER_GENERAL'])->group(function () {
    Route::resource('semilleros', \App\Http\Controllers\LiderSemillero\SemilleroController::class)->only(['index','create','store','show']);
});

Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO'])->group(function () {
    Route::resource('aprendices', \App\Http\Controllers\AprendizController::class)->only(['index','create','store','show']);
});

Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO,LIDER_GENERAL'])->group(function () {
    Route::resource('grupos', GrupoInvestigacionController::class)->only(['index','create','store','show']);
});

/*
|--------------------------------------------------------------------------
| LÍDER_SEMI (UI)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','lider.semillero'])
    ->prefix('lider_semi')
    ->name('lider_semi.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController_semi::class, 'index'])->name('dashboard');

        // Vistas principales
        Route::get('/semilleros', [LiderSemilleroUIController::class, 'semilleros'])->name('semilleros');
        Route::get('/aprendices', [LiderSemilleroUIController::class, 'aprendices'])->name('aprendices');
        Route::view('/recursos', 'lider_semi.recursos')->name('recursos');
        Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
        Route::put('/perfil/contacto', [LiderPerfilController::class, 'updateContacto'])->name('perfil.contacto.update');
        Route::put('/perfil/password', [LiderPerfilController::class, 'updatePassword'])->name('perfil.password.update');

        // Gestión de aprendices por semillero
        Route::get('/semilleros/{semillero}/aprendices', [LiderSemilleroUIController::class, 'editAprendices'])->name('semilleros.aprendices.edit');
        Route::put('/semilleros/{semillero}/aprendices', [LiderSemilleroUIController::class, 'updateAprendices'])->name('semilleros.aprendices.update');
        Route::get('/semilleros/{semillero}/aprendices/search', [LiderSemilleroUIController::class, 'searchAprendices'])->name('semilleros.aprendices.search');
        Route::post('/semilleros/{semillero}/aprendices/attach', [LiderSemilleroUIController::class, 'attachAprendiz'])->name('semilleros.aprendices.attach');
        Route::delete('/semilleros/{semillero}/aprendices/{aprendiz}', [LiderSemilleroUIController::class, 'detachAprendiz'])->name('semilleros.aprendices.detach');
        Route::post('/semilleros/{semillero}/aprendices/create', [LiderSemilleroUIController::class, 'createAndAttachAprendiz'])->name('semilleros.aprendices.create');

        // Gestión de aprendices por proyecto
        Route::get('/proyectos/{proyecto}/aprendices', [LiderSemilleroUIController::class, 'editProyectoAprendices'])->name('proyectos.aprendices.edit');
        Route::put('/proyectos/{proyecto}/aprendices', [LiderSemilleroUIController::class, 'updateProyectoAprendices'])->name('proyectos.aprendices.update');
        Route::get('/proyectos/{proyecto}/aprendices/search', [LiderSemilleroUIController::class, 'searchProyectoAprendices'])->name('proyectos.aprendices.search');
        Route::post('/proyectos/{proyecto}/aprendices/attach', [LiderSemilleroUIController::class, 'attachProyectoAprendiz'])->name('proyectos.aprendices.attach');
        Route::delete('/proyectos/{proyecto}/aprendices/{aprendiz}', [LiderSemilleroUIController::class, 'detachProyectoAprendiz'])->name('proyectos.aprendices.detach');
        Route::post('/proyectos/{proyecto}/aprendices/create', [LiderSemilleroUIController::class, 'createAndAttachProyectoAprendiz'])->name('proyectos.aprendices.create');

        // Documentos / entregas / evidencias
        Route::get('/documentos', [LiderSemilleroUIController::class, 'documentos'])->name('documentos');
        Route::get('/proyectos/list', [LiderSemilleroUIController::class, 'listarProyectos'])->name('proyectos.list');
        Route::get('/proyectos/{proyecto}/aprendices-list', [LiderSemilleroUIController::class, 'obtenerAprendicesProyecto'])->name('proyectos.aprendices.list');
        Route::post('/evidencias/store', [LiderSemilleroUIController::class, 'guardarEvidencia'])->name('evidencias.store');
        Route::get('/proyectos/{proyecto}/entregas', [LiderSemilleroUIController::class, 'obtenerEntregas'])->name('proyectos.entregas');
        Route::put('/entregas/{entrega}/estado', [LiderSemilleroUIController::class, 'cambiarEstadoEntrega'])->name('entregas.estado');
        Route::put('/documentos/{documento}/actualizar', [LiderSemilleroUIController::class, 'actualizarDocumento'])->name('documentos.actualizar');

        // Calendario
        Route::get('/calendario', [LiderSemilleroUIController::class, 'calendario'])->name('calendario');
        Route::get('/eventos', [LiderSemilleroUIController::class, 'obtenerEventos'])->name('eventos.obtener');
        Route::post('/eventos', [LiderSemilleroUIController::class, 'crearEvento'])->name('eventos.crear');
        Route::put('/eventos/{evento}', [LiderSemilleroUIController::class, 'actualizarEvento'])->name('eventos.actualizar');
        Route::delete('/eventos/{evento}', [LiderSemilleroUIController::class, 'eliminarEvento'])->name('eventos.eliminar');
        Route::post('/eventos/{evento}/generar-enlace', [LiderSemilleroUIController::class, 'generarEnlace'])->name('eventos.generar-enlace');
        Route::get('/eventos/{evento}/info', [LiderSemilleroUIController::class, 'getInfoReunion'])->name('eventos.info');
    });

/*
|--------------------------------------------------------------------------
| LÍDER GENERAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:LIDER_GENERAL'])->group(function () {
    Route::get('/lider_general/dashboard', fn () => view('lider_general.dashboard_lider'))
        ->name('lider_general.dashboard');
});

/*
|--------------------------------------------------------------------------
| APRENDIZ (BLOQUE ÚNICO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:APRENDIZ'])
    ->prefix('aprendiz')
    ->name('aprendiz.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AprendizDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AprendizDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/proximas', [CalendarioController::class, 'proximasReuniones'])->name('dashboard.proximas');

        // Perfil
        Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
        Route::get('/perfil/edit', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::post('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');

        // Proyectos (solo ver)
        Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
        Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->whereNumber('id')->name('proyectos.show');

        // Archivos
        Route::resource('archivos', ArchivoController::class);
        Route::get('/archivos/upload', [ArchivoController::class, 'create'])->name('archivos.upload');
        Route::post('/archivos/upload', [ArchivoController::class, 'upload'])->name('archivos.upload.post');
        Route::get('/archivos/list-by-project', [ArchivoController::class, 'listByProject'])->name('archivos.list-by-project');

        // Documentos
        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->whereNumber('id')->name('documentos.download');
        Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])->whereNumber('id')->name('documentos.destroy');

        // Calendario
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/calendario/events', [CalendarioController::class, 'events'])->name('calendario.events');
    });