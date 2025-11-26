<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;


use App\Http\Controllers\Admin\SemilleroController;              // ← sin alias
use App\Http\Controllers\Admin\ProyectoSemilleroController;
use App\Http\Controllers\Admin\ReunionesLideresController;
use App\Http\Controllers\Admin\RecursoController;

// Líder semillero
use App\Http\Controllers\LiderSemillero\SemilleroController as LiderSemilleroUIController;
use App\Http\Controllers\LiderSemillero\CalendarioLiderController;
use App\Http\Controllers\LiderSemillero\AprendicesController as LiderAprendicesController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\LiderSemillero\ProyectoController as LiderProyectoController;
use App\Http\Controllers\LiderSemillero\SemilleroAprendizController;
use App\Http\Controllers\LiderSemillero\PerfilController as LiderPerfilController;
use App\Http\Controllers\LiderSemillero\RecursosController;
use App\Http\Controllers\LiderSemillero\DocumentosController;

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
// ======================================================
// RUTAS DE LOGIN / LOGOUT  (mantén tal cual)
// ======================================================
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Vista de login personalizada
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Envío del formulario de login
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

// Cierre de sesión (logout)
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ======================================================
// RUTAS PROTEGIDAS (paneles, dashboard, etc.)
// ======================================================
// Solo accesibles si el usuario está autenticado
// y con prevención del uso del botón "Atrás" tras logout
Route::middleware(['auth', 'prevent-back-history'])->group(function () {

    // Dashboard principal del administrador
    Route::get('/dashboard', fn() => view('admin.dashboard.index'))->name('dashboard');

    // 🔽 Aquí puedes agregar todas tus rutas internas protegidas
    // Ejemplo:
    // Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    // Route::get('/recursos', [RecursoController::class, 'index'])->name('recursos.index');
});


// ======================================================
// RUTAS ADICIONALES (auth.php, restablecer contraseña, verificación, etc.)
// ======================================================
// Importa las rutas autogeneradas por Laravel Breeze / Jetstream
// ⚠️ Asegúrate de que en routes/auth.php estén COMENTADAS las rutas /login y /logout
require __DIR__ . '/auth.php';

// ---------------------------------------------------------------------
// Sobrescribir GET /login sin middleware 'guest' para evitar bucles
// /login ↔ /dashboard en casos de estados de sesión inconsistentes.
// ---------------------------------------------------------------------
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
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
    // Usar el método searchProyectoAprendices del controlador de UI (LiderSemilleroUIController)
    // para aprovechar la lógica tolerante al esquema y el fallback cuando no hay pivote clara.
    Route::get('/lider_semillero/proyectos/{proyecto}/aprendices/search', [LiderSemilleroUIController::class, 'searchProyectoAprendices'])
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
//                  RUTAS ADMIN / LÍDER INVESTIGACIÓN
// ======================================================
Route::middleware(['auth', 'role:ADMIN,LIDER_INVESTIGACION'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats',  [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/charts', [DashboardController::class, 'charts'])->name('dashboard.charts');

        // USUARIOS
        Route::resource('usuarios', AdminUsuarioController::class);
        Route::get('/usuarios/{id}/edit-ajax', [AdminUsuarioController::class, 'editAjax'])
            ->whereNumber('id')->name('usuarios.edit.ajax');
        Route::post('/usuarios/ajax/store', [AdminUsuarioController::class, 'storeAjax'])
            ->name('usuarios.store.ajax');

          Route::post('usuarios/{usuario}/toggle-permisos-investigacion',
            [AdminUsuarioController::class, 'togglePermisosInvestigacion'])
                ->name('usuarios.togglePermisosInvestigacion');

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
Route::get('/dashboard', function (Request $request) {
    $user = Auth::user();
    if (!$user) return redirect()->route('login');

    $rol = strtoupper(str_replace([' ', '-'], '_', trim($user->role ?? $user->rol ?? '')));

    $map = [
        'ADMIN'           => 'admin.dashboard',
        'INSTRUCTOR'      => 'lider_semi.dashboard',
        'APRENDIZ'        => 'aprendiz.dashboard',
        'LIDER_SEMILLERO' => 'lider_semi.dashboard',
        'LIDER_GENERAL'   => 'lider_general.dashboard',
    ];

    if (! isset($map[$rol])) {
        // Rol desconocido: cerrar sesión para evitar bucles login ↔ dashboard
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    return redirect()->route($map[$rol]);
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

// Rutas para la gestión de semilleros
Route::middleware(['auth', 'role:ADMIN,LIDER_SEMILLERO,LIDER_GENERAL'])->group(function () {
    Route::resource('semilleros', \App\Http\Controllers\Admin\SemilleroController::class);

    // Rutas adicionales para la gestión de aprendices en semilleros
    Route::post('semilleros/{semillero}/aprendices', [\App\Http\Controllers\Admin\SemilleroController::class, 'asignarAprendiz'])
        ->name('semilleros.aprendices.store');

    Route::delete('semilleros/{semillero}/aprendices/{aprendiz}', [\App\Http\Controllers\Admin\SemilleroController::class, 'quitarAprendiz'])
        ->name('semilleros.aprendices.destroy');
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
Route::middleware(['auth', 'role:LIDER_SEMILLERO'])
    ->prefix('lider_semi')
    ->name('lider_semi.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController_semi::class, 'index'])->name('dashboard');

        // Vistas principales
        Route::get('/semilleros', [LiderSemilleroUIController::class, 'semilleros'])->name('semilleros');
        Route::get('/aprendices', [LiderAprendicesController::class, 'index'])->name('aprendices');
        // Recursos (solo Líder Semillero)
        Route::prefix('recursos')->name('recursos.')->group(function () {
            Route::get('/',              [RecursosController::class, 'index'])->name('index');
            Route::get('/crear',         [RecursosController::class, 'create'])->name('create');
            Route::post('/',             [RecursosController::class, 'store'])->name('store');
            Route::get('/{id}/download', [RecursosController::class, 'download'])->whereNumber('id')->name('download');
            Route::delete('/{id}',       [RecursosController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });
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

        // Documentos / entregas / evidencias (controlador dedicado)
        Route::get('/documentos', [DocumentosController::class, 'documentos'])->name('documentos');
        Route::get('/proyectos/list', [DocumentosController::class, 'listarProyectos'])->name('proyectos.list');
        Route::get('/proyectos/{id}/aprendices-list', [DocumentosController::class, 'obtenerAprendicesProyecto'])->name('proyectos.aprendices.list');
        Route::post('/evidencias/store', [DocumentosController::class, 'guardarEvidencia'])->name('evidencias.store');
        Route::get('/proyectos/{id}/entregas', [DocumentosController::class, 'obtenerEntregas'])->name('proyectos.entregas');
        Route::put('/entregas/{id}/estado', [DocumentosController::class, 'cambiarEstadoEntrega'])->name('entregas.estado');
        // Ruta original para actualizar documento (compatibilidad)
        Route::put('/documentos/{id}', [DocumentosController::class, 'actualizarDocumento'])->name('documentos.actualizar');
        // Nueva ruta con sufijo /actualizar para coincidir con el JS del modal de edición
        Route::put('/documentos/{id}/actualizar', [DocumentosController::class, 'actualizarDocumento'])->name('documentos.actualizar.sufijo');
        Route::get('/documentos/{id}/ver', [DocumentosController::class, 'verDocumento'])->name('documentos.ver');
        Route::get('/documentos/{id}/descargar', [DocumentosController::class, 'descargarDocumento'])->name('documentos.descargar');

        // Calendario (controlador dedicado)
        Route::get('/calendario', [CalendarioLiderController::class, 'calendario'])->name('calendario');
        Route::get('/eventos', [CalendarioLiderController::class, 'obtenerEventos'])->name('eventos.obtener');
        Route::post('/eventos', [CalendarioLiderController::class, 'crearEvento'])->name('eventos.crear');
        Route::put('/eventos/{evento}', [CalendarioLiderController::class, 'actualizarEvento'])->name('eventos.actualizar');
        Route::delete('/eventos/{evento}', [CalendarioLiderController::class, 'eliminarEvento'])->name('eventos.eliminar');
        Route::post('/eventos/{evento}/generar-enlace', [CalendarioLiderController::class, 'generarEnlace'])->name('eventos.generar-enlace');
        Route::get('/eventos/{evento}/info', [CalendarioLiderController::class, 'getInfoReunion'])->name('eventos.info');
        Route::put('/eventos/{evento}/participantes/{aprendiz}/asistencia', [CalendarioLiderController::class, 'actualizarAsistencia'])->name('eventos.participantes.asistencia');
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
        Route::post('/documentos/{id}/upload-assigned', [DocumentoController::class, 'uploadAssigned'])->whereNumber('id')->name('documentos.uploadAssigned');
        Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->whereNumber('id')->name('documentos.download');
        Route::put('/documentos/{id}', [DocumentoController::class, 'update'])->whereNumber('id')->name('documentos.update');
        Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])->whereNumber('id')->name('documentos.destroy');


        // Calendario
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/calendario/events', [CalendarioController::class, 'events'])->name('calendario.events');
    });
