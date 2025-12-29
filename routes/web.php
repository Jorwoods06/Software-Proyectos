<?php

use Illuminate\Support\Facades\Route;

// Llamado de los Controladores
use App\Http\Controllers\AuthController; // Controlador de Autenticacion e inicio de sesion
use App\Http\Controllers\DashboardController; // Controlador del Dashboard o panel principal para los procesos
use App\Http\Controllers\UserController; // Controlador de gestion de usuarios y asignacion de roles y permisos
use App\Http\Controllers\RoleController; // Controlador de gestion de roles y permisos base
use App\Http\Controllers\ActividadController; // Controlador de gestion de actividades dentro de proyectos 
use App\Http\Controllers\TareaController; // Controlador de gestion de tareas dentro de actividades de los proyectos
use App\Http\Controllers\EvidenciaController; // Controlador de gestion de evidencias dentro de tareas de las actividades de los proyectos
use App\Http\Controllers\ProyectoController; // Controlador de gestion de proyectos
use App\Http\Controllers\ProyectoMetricaController; // Controlador de metricas de proyectos
use App\Http\Controllers\InvitacionController; // Controlador de invitaciones a proyectos
// Llamado de los Middleware
use App\Http\Middleware\CheckPermission; // Revision de permisos  
use App\Http\Middleware\JwtMiddleware; // Validacion de token JWT
use App\Http\Middleware\RoleTiOrAdmin; // Validacion de rol TI o Administrador (aqui solo se gestionan las rutas para administracion de usuarios tambien roles y permisos)
use App\Http\Middleware\SanitizeInput; // Sanitizacion de entradas (sanitiza todos los request GET, POST, etc para evitar inyecciones XSS y otros ataques)
use App\Http\Controllers\CalendarioController; // Controlador de gestion de calendario


Route::get('/', fn() => redirect()->route('login'));

// ----- Autenticación -----
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ----- Rutas protegidas con JWT -----
Route::middleware([JwtMiddleware::class, SanitizeInput::class])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inicio (página principal con tareas y proyectos)
    Route::get('/inicio', [DashboardController::class, 'inicio'])->name('inicio');

    // ========= Gestión de Usuarios SOLO Admin/TI =========
    Route::middleware([RoleTiOrAdmin::class])->group(function () {

        Route::resource('users', UserController::class);

        Route::get('/users/{id}/manage', [UserController::class, 'manageRolesAndPermissions'])
            ->name('users.manage');

        Route::put('/users/{id}/update-roles-permissions', [UserController::class, 'updateRolesAndPermissions'])
            ->name('users.updateRolesAndPermissions');

        Route::post('/users/duplicate', [UserController::class, 'duplicateRolesAndPermissions'])
            ->name('users.duplicateRolesAndPermissions');

        Route::get('/users/duplicate/form', [UserController::class, 'duplicateForm'])
            ->name('users.duplicateForm');

        // Roles base
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('/roles/permissions/update', [RoleController::class, 'updatePermissions'])
            ->name('roles.permissions.update');
    });


    // Proyectos y (permiso por proyecto) / Actividades / Tareas / Evidencias (esto aun me falta)
    Route::prefix('proyectos')->group(function () {

        // Listar
        Route::get('/', [ProyectoController::class, 'index'])
            ->middleware([CheckPermission::class . ':ver proyecto'])
            ->name('proyectos.index');

        // Crear
        Route::get('/create', [ProyectoController::class, 'create'])
            ->middleware([CheckPermission::class . ':crear proyecto'])
            ->name('proyectos.create');

        Route::post('/', [ProyectoController::class, 'store'])
            ->middleware([CheckPermission::class . ':crear proyecto'])
            ->name('proyectos.store');

        // Editar
        Route::get('/{id}/edit', [ProyectoController::class, 'edit'])
            ->middleware([CheckPermission::class . ':editar proyecto'])
            ->name('proyectos.edit');

        Route::put('/{id}', [ProyectoController::class, 'update'])
            ->middleware([CheckPermission::class . ':editar proyecto'])
            ->name('proyectos.update');

        // Eliminar
        Route::delete('/{id}', [ProyectoController::class, 'destroy'])
            ->middleware([CheckPermission::class . ':eliminar proyecto'])
            ->name('proyectos.destroy');

        // Usuarios por departamento (para el modal de invitacion de usuarios a los proyetos)
        Route::get('/{id}/usuarios', [ProyectoController::class, 'usuariosPorDepartamento'])
            ->middleware([CheckPermission::class . ':invitar usuarios a este proyecto'])
            ->name('proyectos.usuarios');

        // Invitar colaboradores al proyecto
        Route::post('/{id}/invitar', [ProyectoController::class, 'invitarUsuario'])
            ->middleware([CheckPermission::class . ':invitar usuarios a este proyecto'])
            ->name('proyectos.invitar');

        // Aceptar/Rechazar invitaciones
        Route::get('/invitaciones/aceptar/{token}', [InvitacionController::class, 'aceptar'])
            ->name('proyectos.invitaciones.aceptar');
        
        Route::get('/invitaciones/rechazar/{token}', [InvitacionController::class, 'rechazar'])
            ->name('proyectos.invitaciones.rechazar');

        // Asignar permisos específicos a un usuario dentro del proyecto
        Route::post('/{id}/permisos', [ProyectoController::class, 'asignarPermisoProyecto'])
            ->middleware([CheckPermission::class . ':asignar permisos proyecto'])
            ->name('proyectos.asignarPermiso');

        // Ver trazabilidad del proyecto
        Route::get('/proyectos/{id}/trazabilidad', [ProyectoController::class, 'verTrazabilidad'])
            ->name('proyectos.trazabilidad');

        // Métricas del proyecto
        Route::get('/{id}/metricas', [ProyectoMetricaController::class, 'index'])
            ->middleware([CheckPermission::class . ':ver proyecto'])
            ->name('proyectos.metricas');
    });

    Route::prefix('actividades')->group(function () {

        Route::get('/{proyecto}', [ActividadController::class, 'index'])
            ->middleware([CheckPermission::class . ':ver actividades'])
            ->name('actividades.index');

        Route::post('/', [ActividadController::class, 'store'])
            ->middleware([CheckPermission::class . ':crear actividades'])
            ->name('actividades.store');

        Route::put('/{id}', [ActividadController::class, 'update'])
            ->middleware([CheckPermission::class . ':editar actividades'])
            ->name('actividades.update');

        Route::delete('/{id}', [ActividadController::class, 'destroy'])
            ->middleware([CheckPermission::class . ':eliminar actividades'])
            ->name('actividades.destroy');

        Route::get('/{id}/tareas', [ActividadController::class, 'getTareas'])
            ->middleware([CheckPermission::class . ':ver actividades'])
            ->name('actividades.tareas');
    });

    Route::prefix('tareas')->group(function () {

        Route::get('/{actividad}', [TareaController::class, 'index'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.index');

        Route::post('/', [TareaController::class, 'store'])
            ->middleware([CheckPermission::class . ':crear tarea'])
            ->name('tareas.store');

        Route::post('/{id}/toggle', [TareaController::class, 'toggleCompletada'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.toggle');

        Route::post('/{id}/toggle-estado', [TareaController::class, 'toggleEstadoPendienteEnProgreso'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.toggle-estado');

        Route::post('/{id}/update-fecha', [TareaController::class, 'updateFecha'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.update-fecha');

        Route::put('/{id}', [TareaController::class, 'update'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.update');

        Route::post('/{id}/asignar', [TareaController::class, 'asignarUsuarios'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.asignar');

        Route::delete('/{id}', [TareaController::class, 'destroy'])
            ->middleware([CheckPermission::class . ':eliminar tarea'])
            ->name('tareas.destroy');

        Route::put('/{id}/descripcion',[TareaController::class, 'updateDescripcion'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.updateDescripcion');

        Route::put('/{id}/comentarios',[TareaController::class, 'updateComentarios'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.updateComentarios');

        Route::get('/{id}/comentarios',[TareaController::class, 'obtenerComentarios'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.obtenerComentarios');

        Route::get('/{id}/descripcion',[TareaController::class, 'obtenerDescripcion'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.obtenerDescripcion');

        // Evidencias de tareas
        Route::post('/{id}/evidencias', [EvidenciaController::class, 'store'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.evidencias.store');

        Route::get('/{id}/evidencias', [EvidenciaController::class, 'index'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.evidencias.index');

        Route::get('/{tareaId}/evidencias/{evidenciaId}', [EvidenciaController::class, 'show'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.evidencias.show');

        Route::get('/{tareaId}/evidencias/{evidenciaId}/download', [EvidenciaController::class, 'download'])
            ->middleware([CheckPermission::class . ':ver tarea'])
            ->name('tareas.evidencias.download');

        Route::delete('/{tareaId}/evidencias/{evidenciaId}', [EvidenciaController::class, 'destroy'])
            ->middleware([CheckPermission::class . ':editar tarea'])
            ->name('tareas.evidencias.destroy');
    });

    Route::prefix('calendario')->group(function () {
        Route::get('/', [CalendarioController::class, 'index'])->name('calendario.calendario');
    });
});
