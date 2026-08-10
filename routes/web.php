<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'showWelcome']);
Route::get('/empleados/{n_empleado}/buscar', [HomeController::class, 'buscarEmpleado']);
Route::post('/asistencia/{n_empleado}/salida', [HomeController::class, 'marcarSalidaConfirmada']);

Route::get('/admin', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/admin', [AuthController::class, 'login'])->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    // Login y logout
    // Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
    // Route::post('/', [AuthController::class, 'login'])->name('login');

    // 🔐 Rutas protegidas
    Route::middleware('auth')->group(function () {
        // Dashboard de asistencias
        Route::get('/asistencias', [AdminController::class, 'asistencias'])->name('asistencias');
        // Route::get('/asistencias/reporte', [AdminController::class, 'generarReporte'])->name('asistencias.reporte');
        // Route::get('/asistencias/reporte', [AdminController::class, 'generarReporteExcel'])->name('asistencias.reporte.excel');
        Route::get('/asistencias/reporte/pdf', [AdminController::class, 'generarReporte'])
            ->name('asistencias.reporte.pdf');

        Route::get('/asistencias/reporte/excel', [AdminController::class, 'generarReporteExcel'])
            ->name('asistencias.reporte.excel');

        // Usuarios
        Route::get('/preferencias', [UsuarioController::class, 'listarUsuarios'])->name('preferencias');
        Route::get('/usuarios/crear', [UsuarioController::class, 'crearUsuario'])->name('usuarios.crear');
        Route::post('/usuarios', [UsuarioController::class, 'guardarUsuario'])->name('usuarios.guardar');
        Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.editar');
        Route::put('/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');



        Route::get('/preferencias/configuraciones', [UsuarioController::class, 'listarDepartamentos'])
            ->name('configuraciones');

        Route::post('/preferencias/configuraciones', [UsuarioController::class, 'storeDepartamento'])
            ->name('departamentos.store');

        Route::delete('/departamentos/{id}', [UsuarioController::class, 'destroyDepartamento'])
            ->name('departamentos.destroy');


        Route::get('/vacacionesfestivos', [UsuarioController::class, 'configurarVacacionesFestivos'])->name('vacacionesfestivos.index');
        Route::post('/configuracion/horario-0730', [UsuarioController::class, 'actualizarHorario0730'])->name('configuracion.horario0730');
        //Route::post('/preferencias/configuracion', [UsuarioController::class, 'actualizarData'])->name('usuarios.data');
        // vacaciones
        Route::post('/vacaciones/store', [UsuarioController::class, 'store'])->name('vacaciones.store');
        Route::post('/dias-festivos/store', [UsuarioController::class, 'storeDiaFestivo'])->name('diasfestivos.store');
        Route::delete('/vacaciones/{id}', [UsuarioController::class, 'destroyVacaciones'])->name('vacaciones.destroyVacaciones');
        Route::delete('/festivos/{id}', [UsuarioController::class, 'destroyFestivos'])->name('festivos.destroyFestivos');
        Route::put('/vacaciones/{vacacion}', [UsuarioController::class, 'updateVacaciones'])->name('vacaciones.update');
        Route::put('/festivos/{diaFestivo}', [UsuarioController::class, 'updateFestivos'])->name('festivos.update');

        Route::get('/vacaciones/excel', [UsuarioController::class, 'generarVacacionesExcel'])->name('vacaciones.excel');
        Route::get('/empleados/{empleado}/vacaciones/pdf', [EmpleadoController::class, 'generarVacacionesPdf'])->name('empleados.vacaciones.pdf');




        // Empleados
        Route::get('/empleados', [EmpleadoController::class, 'listarEmpleados'])->name('empleados');
        Route::get('/empleados/crear', [EmpleadoController::class, 'crearEmpleado'])->name('empleados.crear');
        Route::post('/empleados', [EmpleadoController::class, 'guardarEmpleado'])->name('empleados.guardar');
        Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'editarEmpleado'])->name('empleados.editar');
        Route::put('/empleados/{id}', [EmpleadoController::class, 'actualizarEmpleado'])->name('empleados.actualizar');
        Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    });
});

// Logout fuera del prefix si aplica a usuarios no administradores también
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
