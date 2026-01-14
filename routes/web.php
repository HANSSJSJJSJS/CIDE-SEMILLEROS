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
use App\Http\Controllers\Admin\RecursoController as AdminRecursoController;


// Líder semillero
use App\Http\Controllers\LiderSemillero\SemilleroController as LiderSemilleroUIController;
use App\Http\Controllers\LiderSemillero\CalendarioLiderController;
use App\Http\Controllers\LiderSemillero\AprendicesController as LiderAprendicesController;
use App\Http\Controllers\LiderSemillero\DashboardController_semi;
use App\Http\Controllers\LiderSemillero\ProyectoController as LiderProyectoController;
use App\Http\Controllers\LiderSemillero\SemilleroAprendizController;
use App\Http\Controllers\LiderSemillero\PerfilController as LiderPerfilController;
use App\Http\Controllers\LiderSemillero\RecursoController as LiderRecursoController;
use App\Http\Controllers\LiderSemillero\DocumentosController;


// Aprendiz
use App\Http\Controllers\Aprendiz\DashboardController as AprendizDashboardController;
use App\Http\Controllers\Aprendiz\PerfilController;
use App\Http\Controllers\Aprendiz\ProyectoController;
use App\Http\Controllers\Aprendiz\ArchivoController;
use App\Http\Controllers\Aprendiz\DocumentoController;
use App\Http\Controllers\Aprendiz\CalendarioController;
use App\Http\Controllers\HolidayController;
// cambio contraseña
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Mail;






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

// API: Feriados por año (proxy con caché)
Route::get('/api/holidays/{year?}', [HolidayController::class, 'index'])
    ->whereNumber('year')
    ->name('api.holidays');

// Vista de login personalizada
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Envío del formulario de login
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

// Cierre de sesión (logout)
Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');



// ---------------------------------------------------------------------
// RUTAS DE CAMBIO FORZOSO DE CONTRASEÑA
// ---------------------------------------------------------------------

Route::middleware(['auth'])->group(function () {

    // Mostrar formulario
    Route::get('/password/change', [PasswordController::class, 'showChangeForm'])
        ->name('password.change.form');

    // Guardar nueva contraseña
    Route::post('/password/change', [PasswordController::class, 'updatePassword'])
        ->name('password.change.update');

});
// ======================================================
// RUTAS DE LOGIN / olvido CONTRASEÑA
// ======================================================
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
            ->middleware('guest')
            ->name('password.request');

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('guest')
            ->name('password.email');

        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
            ->middleware('guest')
            ->name('password.reset');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware('guest')
             ->name('password.reset.update');

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
    Route::match(['post','put','patch'], '/lider_semillero/proyectos/{id}/participantes', [LiderProyectoController::class, 'assignParticipant'])
        ->whereNumber('id')->name('lider_semi.proyectos.participantes.assign');
    Route::delete('/lider_semillero/proyectos/{id}/participantes/{user}', [LiderProyectoController::class, 'removeParticipant'])
        ->whereNumber('id')->whereNumber('user')->name('lider_semi.proyectos.participantes.remove');
    Route::get('/lider_semillero/proyectos/{id}/candidatos', [LiderProyectoController::class, 'candidatosJson'])
        ->whereNumber('id')->name('lider_semi.proyectos.candidatos');

    // Compatibilidad con modal en views/lider_semi/semilleros.blade.php
    // Usar el método searchProyectoAprendices del controlador de UI (LiderSemilleroUIController)
    // para aprovechar la lógica tolerante al esquema y el fallback cuando no hay pivote clara.
    Route::get('/lider_semillero/proyectos/{proyecto}/aprendices/search', [LiderSemilleroUIController::class, 'searchProyectoAprendices'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.search.compat');
    Route::post('/lider_semillero/proyectos/{proyecto}/aprendices', [LiderProyectoController::class, 'assignParticipant'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.attach.compat');
    Route::delete('/lider_semillero/proyectos/{proyecto}/aprendices/{aprendiz}', [LiderProyectoController::class, 'removeParticipant'])
        ->whereNumber('proyecto')->whereNumber('aprendiz')->name('lider_semi.proyectos.aprendices.detach.compat');
    Route::match(['put','patch'], '/lider_semillero/proyectos/{proyecto}/aprendices', [LiderProyectoController::class, 'updateParticipants'])
        ->whereNumber('proyecto')->name('lider_semi.proyectos.aprendices.update.compat');

    // Versión por Semillero → deriva a proyecto activo
    Route::get('/lider_semillero/semilleros/{semillero}/aprendices/search', [SemilleroAprendizController::class, 'search'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.search.compat');
    Route::post('/lider_semillero/semilleros/{semillero}/aprendices', [SemilleroAprendizController::class, 'attach'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.attach.compat');
    Route::delete('/lider_semillero/semilleros/{semillero}/aprendices/{aprendiz}', [SemilleroAprendizController::class, 'detach'])
        ->whereNumber('semillero')->whereNumber('aprendiz')->name('lider_semi.semilleros.aprendices.detach.compat');
    Route::put('/lider_semillero/semilleros/{semillero}/aprendices', [SemilleroAprendizController::class, 'update'])
        ->whereNumber('semillero')->name('lider_semi.semilleros.aprendices.update.compat');
});

// ======================================================
//          RUTAS ADMIN/ LIDER GENERAL / LÍDER INVESTIGACIÓN
// ======================================================
Route::middleware([
        'auth',
        'force.password.change',
        'role:ADMIN,LIDER_INVESTIGACION'
    ])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/test-multimedia', function () {
            return route('admin.recursos.multimedia.store');
        });
        // ==========================
        //         DASHBOARD
        // ==========================
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats',  [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/dashboard/charts', [DashboardController::class, 'charts'])->name('dashboard.charts');

       // ==========================
        //         USUARIOS
        // ==========================
        Route::resource('usuarios', AdminUsuarioController::class);

        // Editar por AJAX
        Route::get('usuarios/{id}/edit-ajax', [AdminUsuarioController::class, 'editAjax'])
            ->whereNumber('id')
            ->name('usuarios.edit.ajax');

        // Crear por AJAX
        Route::post('usuarios/ajax/store', [AdminUsuarioController::class, 'storeAjax'])
            ->name('usuarios.store.ajax');

        // Toggle permisos líder investigación
        Route::post('usuarios/{usuario}/toggle-permisos-investigacion',
            [AdminUsuarioController::class, 'togglePermisosInvestigacion']
        )->name('usuarios.togglePermisosInvestigacion');

        // Detalle por AJAX
        Route::get('usuarios/{usuario}/detalle-ajax',
            [AdminUsuarioController::class, 'showAjax']
        )->name('usuarios.detalle.ajax');
        


        // ==========================
        //         FUNCIONES ADMIN
        // ==========================
        Route::get('/funciones', [AdminController::class, 'index'])->name('functions');
        Route::get('/crear', fn () => view('admin.crear'))->name('crear');


            // ==========================
            //         SEMILLEROS
            // ==========================
            // 🔹 Ruta AJAX para líderes disponibles
            Route::get('semilleros/lideres-disponibles', [SemilleroController::class, 'lideresDisponibles'])
                ->name('semilleros.lideresDisponibles');

            // 🔹 Resource semilleros (sin show)
            Route::resource('semilleros', SemilleroController::class)->except(['show']);

            // 🔹 Editar vía AJAX
            Route::get('semilleros/{id}/edit-ajax', [SemilleroController::class, 'editAjax'])
                ->whereNumber('id')
                ->name('semilleros.edit.ajax');

            // 🔹 NUEVA: obtener líder de un semillero en JSON
            Route::get('semilleros/{semillero}/lider', [SemilleroController::class, 'liderJson'])
                ->name('semilleros.liderJson');

             Route::get('semilleros/{semillero}/recursos', [RecursoController::class, 'porSemillero'])
            ->whereNumber('semillero')
            ->name('semilleros.recursos');


        // ==========================
        //   PROYECTOS POR SEMILLERO
        // ==========================
        Route::prefix('semilleros')->name('semilleros.')->group(function () {

            // Listar + crear
            Route::get('{semillero}/proyectos',  [ProyectoSemilleroController::class, 'index'])
                ->name('proyectos.index');

            Route::post('{semillero}/proyectos', [ProyectoSemilleroController::class, 'store'])
                ->name('proyectos.store');

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
        // cometnarios de los proyectos
                Route::post(
            'semilleros/{semillero}/proyectos/{proyecto}/observaciones',
            [ProyectoSemilleroController::class, 'guardarObservaciones']
        )->name('semilleros.proyectos.observaciones');


        // ==========================
        //        REUNIONES LÍDERES
        // ==========================
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

                // ==========================
                //          RECURSOS (ADMIN)
                // ==========================
                Route::prefix('recursos')->as('recursos.')->group(function () {

                    // === CRUD BÁSICO DE RECURSOS ===
                    Route::get('/', [AdminRecursoController::class, 'index'])->name('index');
                    Route::get('/listar', [AdminRecursoController::class, 'listar'])->name('listar');
                    Route::post('/', [AdminRecursoController::class, 'store'])->name('store');
                    Route::get('/{recurso}/dl', [AdminRecursoController::class, 'download'])->name('download');
                    Route::delete('/{recurso}', [AdminRecursoController::class, 'destroy'])->name('destroy');

                    // === RECURSOS ASIGNADOS A SEMILLEROS ===
                    Route::get('/semillero/{semillero}', [AdminRecursoController::class, 'porSemillero'])->name('porSemillero');
                    Route::post('/semillero', [AdminRecursoController::class, 'storeActividad'])->name('semillero.store');
                    Route::put('/semillero/{recurso}/estado', [AdminRecursoController::class, 'actualizarEstadoActividad'])->name('semillero.estado');
                    Route::put('/semillero/{recurso}', [AdminRecursoController::class, 'actualizarRecurso'])->name('semillero.update');

                    // Obtener líder del semillero
                    Route::get('/semillero/{id}/lider', [AdminRecursoController::class, 'liderDeSemillero'])->name('semillero.lider');

                    // === PROYECTOS POR SEMILLERO ===
                    Route::get('/proyectos/{id}', [AdminRecursoController::class, 'getProyectos'])->name('proyectos');

                    // === MULTIMEDIA ===
                    Route::get('/multimedia', [AdminRecursoController::class, 'multimedia'])->name('multimedia');
                    Route::post('/multimedia/store', [AdminRecursoController::class, 'storeMultimedia'])->name('multimedia.store');
                    Route::get('/multimedia/list', [AdminRecursoController::class, 'obtenerMultimedia'])->name('multimedia.list');
                    Route::delete('/multimedia/{id}', [AdminRecursoController::class, 'deleteMultimedia'])->name('multimedia.delete');
                });



        // ==========================
        //            PERFIL ADMIN
        // ==========================
        Route::prefix('perfil')->as('perfil.')->group(function () {
            Route::get('/',         [AdminPerfilController::class, 'edit'])->name('edit');
            Route::put('/',         [AdminPerfilController::class, 'update'])->name('update');
            Route::put('/password', [AdminPerfilController::class, 'updatePassword'])->name('password.update');
        });


        // ==========================
        //          NOTIFICACIONES
        // ==========================
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
Route::middleware(['auth',  'role:LIDER_SEMILLERO'])
    ->prefix('lider_semi')
    ->name('lider_semi.')
    ->group(function () {

        // ------------------------
        // DASHBOARD
        // ------------------------
        Route::get('/dashboard', [DashboardController_semi::class, 'index'])
            ->name('dashboard');

        // ------------------------
        // SEMILLEROS
        // ------------------------
        Route::get('/semilleros', [LiderSemilleroUIController::class, 'semilleros'])
            ->name('semilleros');

        // ------------------------
        // APRENDICES
        // ------------------------
        Route::get('/aprendices', [LiderAprendicesController::class, 'index'])
            ->name('aprendices');

        // ------------------------
        // RECURSOS (LÍDER DE SEMILLERO)
        // ------------------------
                Route::get('/recursos', [LiderRecursoController::class, 'index'])
                ->name('recursos.index');

            Route::get('/recursos/proyecto/{id}', [LiderRecursoController::class, 'recursosPorProyecto'])
                ->name('recursos.proyecto');

            Route::post('/recursos/responder', [LiderRecursoController::class, 'responder'])
                ->name('recursos.responder');

            Route::get('/recursos/multimedia', [LiderRecursoController::class, 'multimedia'])
                ->name('recursos.multimedia');
            Route::get('/recursos/multimedia/list', [LiderRecursoController::class, 'obtenerMultimedia'])
            ->name('recursos.multimedia.list');
            // Guardar recurso nuevo
            Route::post('/recursos/store', [LiderRecursoController::class, 'store'])->name('recursos.store');
            
             // ------------------------
            //Multimedia 
            // ------------------------












        // ------------------------
        // PERFIL
        // ------------------------
        Route::view('/perfil', 'lider_semi.perfil')->name('perfil');
        Route::put('/perfil/contacto', [LiderPerfilController::class, 'updateContacto'])->name('perfil.contacto.update');
        Route::put('/perfil/password', [LiderPerfilController::class, 'updatePassword'])->name('perfil.password.update');

        // ------------------------
        // APRENDICES POR SEMILLERO
        // ------------------------
        Route::get('/semilleros/{semillero}/aprendices', [LiderSemilleroUIController::class, 'editAprendices'])->name('semilleros.aprendices.edit');
        Route::put('/semilleros/{semillero}/aprendices', [LiderSemilleroUIController::class, 'updateAprendices'])->name('semilleros.aprendices.update');
        Route::get('/semilleros/{semillero}/aprendices/search', [LiderSemilleroUIController::class, 'searchAprendices'])->name('semilleros.aprendices.search');
        Route::post('/semilleros/{semillero}/aprendices/attach', [LiderSemilleroUIController::class, 'attachAprendiz'])->name('semilleros.aprendices.attach');
        Route::delete('/semilleros/{semillero}/aprendices/{aprendiz}', [LiderSemilleroUIController::class, 'detachAprendiz'])->name('semilleros.aprendices.detach');
        Route::post('/semilleros/{semillero}/aprendices/create', [LiderSemilleroUIController::class, 'createAndAttachAprendiz'])->name('semilleros.aprendices.create');

        // ------------------------
        // APRENDICES POR PROYECTO
        // ------------------------
        Route::get('/proyectos/{proyecto}/aprendices', [LiderSemilleroUIController::class, 'editProyectoAprendices'])->name('proyectos.aprendices.edit');
        Route::put('/proyectos/{proyecto}/aprendices', [LiderSemilleroUIController::class, 'updateProyectoAprendices'])->name('proyectos.aprendices.update');
        Route::get('/proyectos/{proyecto}/aprendices/search', [LiderSemilleroUIController::class, 'searchProyectoAprendices'])->name('proyectos.aprendices.search');
        Route::post('/proyectos/{proyecto}/aprendices/attach', [LiderSemilleroUIController::class, 'attachProyectoAprendiz'])->name('proyectos.aprendices.attach');
        Route::delete('/proyectos/{proyecto}/aprendices/{aprendiz}', [LiderSemilleroUIController::class, 'detachProyectoAprendiz'])->name('proyectos.aprendices.detach');
        Route::post('/proyectos/{proyecto}/aprendices/create', [LiderSemilleroUIController::class, 'createAndAttachProyectoAprendiz'])->name('proyectos.aprendices.create');

        // ------------------------
        // DOCUMENTOS / EVIDENCIAS
        // ------------------------
        Route::get('/documentos', [DocumentosController::class, 'documentos'])->name('documentos');
        Route::get('/proyectos/list', [DocumentosController::class, 'listarProyectos'])->name('proyectos.list');
        Route::get('/proyectos/{id}/aprendices-list', [DocumentosController::class, 'obtenerAprendicesProyecto'])->name('proyectos.aprendices.list');
        Route::get('/proyectos/{id}/siguiente-numero', [DocumentosController::class, 'siguienteNumeroEvidencia'])->name('proyectos.siguiente_numero');
        Route::post('/evidencias/store', [DocumentosController::class, 'guardarEvidencia'])->name('evidencias.store');
        Route::get('/proyectos/{id}/entregas', [DocumentosController::class, 'obtenerEntregas'])->name('proyectos.entregas');
        Route::put('/entregas/{id}/estado', [DocumentosController::class, 'cambiarEstadoEntrega'])->name('entregas.estado');
        Route::put('/entregas/{id}/respuesta', [DocumentosController::class, 'responderPregunta'])->name('entregas.responder');
        Route::put('/documentos/{id}', [DocumentosController::class, 'actualizarDocumento'])->name('documentos.actualizar');
        Route::put('/documentos/{id}/actualizar', [DocumentosController::class, 'actualizarDocumento'])->name('documentos.actualizar.sufijo');
        Route::get('/documentos/{id}/ver', [DocumentosController::class, 'verDocumento'])->name('documentos.ver');
        Route::get('/documentos/{id}/descargar', [DocumentosController::class, 'descargarDocumento'])->name('documentos.descargar');

        // ------------------------
        // CALENDARIO
        // ------------------------
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
| APRENDIZ (BLOQUE ÚNICO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','force.password.change', 'role:APRENDIZ'])
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
        Route::post('/documentos/{id}/preguntar', [DocumentoController::class, 'preguntar'])->whereNumber('id')->name('documentos.preguntar');
        Route::put('/documentos/{id}/respuesta-leida', [DocumentoController::class, 'marcarRespuestaLeida'])->whereNumber('id')->name('documentos.respuesta_leida');
        Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->whereNumber('id')->name('documentos.download');
        Route::get('/documentos/{id}/view', [DocumentoController::class, 'view'])->whereNumber('id')->name('documentos.view');
        Route::put('/documentos/{id}', [DocumentoController::class, 'update'])->whereNumber('id')->name('documentos.update');
        Route::delete('/documentos/{id}', [DocumentoController::class, 'destroy'])->whereNumber('id')->name('documentos.destroy');

        // Notificaciones: resumen para la campana (evidencias nuevas asignadas y reuniones próximas)
        Route::get('/notifications/summary', function () {
            $user = Auth::user();
            if (!$user) return response()->json(['ok'=>false,'evidencias_nuevas'=>0,'reuniones_nuevas'=>0,'total'=>0]);

            $evidencias = 0; $reuniones = 0; $respuestas = 0;

            // Evidencias nuevas asignadas (documentos sin archivo para este aprendiz/usuario)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('documentos')) {
                    $docAprCol = \Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz') ? 'id_aprendiz'
                                : (\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario') ? 'id_usuario' : null);

                    $aprId = null; $aprUserCol = null; $aprPkCol = null;
                    if (\Illuminate\Support\Facades\Schema::hasTable('aprendices')) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_usuario')) { $aprUserCol = 'id_usuario'; }
                        elseif (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','user_id')) { $aprUserCol = 'user_id'; }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCol = 'id_aprendiz'; }
                        elseif (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id')) { $aprPkCol = 'id'; }
                        if ($aprUserCol && $aprPkCol) {
                            $aprId = \Illuminate\Support\Facades\DB::table('aprendices')->where($aprUserCol, $user->id)->value($aprPkCol);
                        }
                    }

                    $q = \Illuminate\Support\Facades\DB::table('documentos')
                        ->whereRaw("documentos.documento NOT LIKE 'PLACEHOLDER%'")
                        ->where(function($w){ $w->whereNull('ruta_archivo')->orWhere('ruta_archivo',''); });

                    if ($docAprCol === 'id_aprendiz' && $aprId) {
                        $q->where('id_aprendiz', $aprId);
                    } elseif ($docAprCol === 'id_usuario') {
                        $q->where('id_usuario', (int)$user->id);
                    } else {
                        // Sin columna clara: intentar ambas
                        $q->where(function($w) use ($aprId, $user){
                            $w->orWhere('id_aprendiz', $aprId ?? -1);
                            $w->orWhere('id_usuario', (int)$user->id);
                        });
                    }
                    $evidencias = (int) $q->count();
                }
            } catch (\Throwable $e) { $evidencias = 0; }

            // Reuniones próximas (usar fecha_hora y pivote evento_participantes)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('eventos')) {
                    $hoy = now()->startOfDay();
                    $rq = \Illuminate\Support\Facades\DB::table('eventos');
                    // Fecha columna principal
                    if (\Illuminate\Support\Facades\Schema::hasColumn('eventos','fecha_hora')) {
                        $rq->where('fecha_hora', '>=', $hoy);
                    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('eventos','fecha')) {
                        $rq->whereDate('fecha', '>=', $hoy->toDateString());
                    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('eventos','fecha_inicio')) {
                        $rq->whereDate('fecha_inicio', '>=', $hoy->toDateString());
                    } else {
                        throw new \Exception('Sin columna de fecha en eventos');
                    }

                    // Excluir canceladas si existe estado
                    if (\Illuminate\Support\Facades\Schema::hasColumn('eventos','estado')) {
                        $rq->where(function($q){ $q->whereNull('estado')->orWhere('estado','<>','cancelado'); });
                    }

                    // Resolver id_aprendiz real para pivote
                    $aprIdCount = null;
                    if (\Illuminate\Support\Facades\Schema::hasTable('aprendices')) {
                        $aprPk = \Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_aprendiz') ? 'id_aprendiz' : (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id') ? 'id' : null);
                        $aprUserCol = \Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_usuario') ? 'id_usuario' : (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','user_id') ? 'user_id' : null);
                        if ($aprPk && $aprUserCol) {
                            $aprIdCount = \Illuminate\Support\Facades\DB::table('aprendices')->where($aprUserCol, $user->id)->value($aprPk);
                        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','email')) {
                            $email = (string)($user->email ?? '');
                            if ($email !== '') {
                                $aprPk = $aprPk ?: (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_aprendiz') ? 'id_aprendiz' : 'id');
                                $aprIdCount = \Illuminate\Support\Facades\DB::table('aprendices')->where('email', $email)->value($aprPk);
                            }
                        }
                    }

                    if (\Illuminate\Support\Facades\Schema::hasTable('evento_participantes') && !is_null($aprIdCount)) {
                        $rq->join('evento_participantes as ep','ep.id_evento','=','eventos.id_evento')
                           ->where('ep.id_aprendiz', (int)$aprIdCount);
                    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('eventos','id_usuario')) {
                        $rq->where('id_usuario', (int)$user->id);
                    } else {
                        // Sin forma de relacionar: no contar
                        $rq->whereRaw('1=0');
                    }

                    $reuniones = (int) $rq->count();
                }
            } catch (\Throwable $e) { $reuniones = 0; }

            // Respuestas del líder a preguntas del aprendiz (no leídas) — filtrar SIEMPRE por el usuario actual
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('documentos')) {
                    $aprIdLocal = null; $aprUserCol = null; $aprPkCol = null;
                    if (\Illuminate\Support\Facades\Schema::hasTable('aprendices')) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_usuario')) { $aprUserCol = 'id_usuario'; }
                        elseif (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','user_id')) { $aprUserCol = 'user_id'; }
                        if (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id_aprendiz')) { $aprPkCol = 'id_aprendiz'; }
                        elseif (\Illuminate\Support\Facades\Schema::hasColumn('aprendices','id')) { $aprPkCol = 'id'; }
                        if ($aprUserCol && $aprPkCol) {
                            $aprIdLocal = \Illuminate\Support\Facades\DB::table('aprendices')->where($aprUserCol, $user->id)->value($aprPkCol);
                        }
                    }

                    $q = \Illuminate\Support\Facades\DB::table('documentos')
                        ->whereNotNull('respuesta_lider')
                        ->where(function($w) use ($aprIdLocal, $user){
                            $w->when(\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_aprendiz'), function($qq) use ($aprIdLocal){ $qq->orWhere('id_aprendiz', $aprIdLocal ?? -1); })
                              ->when(\Illuminate\Support\Facades\Schema::hasColumn('documentos','id_usuario'), function($qq) use ($user){ $qq->orWhere('id_usuario', (int)$user->id); });
                        });

                    if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','respuesta_leida')) {
                        $q->where(function($w){ $w->whereNull('respuesta_leida')->orWhere('respuesta_leida',''); });
                    } else {
                        // Sin marca de lectura: considerar respondidas en últimos 2 días
                        if (\Illuminate\Support\Facades\Schema::hasColumn('documentos','respondido_en')) {
                            $lim = now()->subDays(2);
                            $q->where('respondido_en', '>=', $lim);
                        }
                    }
                    $respuestas = (int) $q->count();
                }
            } catch (\Throwable $e) { $respuestas = 0; }

            $total = $evidencias + $reuniones + $respuestas;
            return response()->json(['ok'=>true,'evidencias_nuevas'=>$evidencias,'reuniones_nuevas'=>$reuniones,'respuestas_nuevas'=>$respuestas,'total'=>$total]);
        })->name('notifications.summary');

        // Calendario
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/calendario/events', [CalendarioController::class, 'events'])->name('calendario.events');
    });
