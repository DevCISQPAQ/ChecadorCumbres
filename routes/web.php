<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'showWelcome']);
Route::get('/empleado/{id}/buscar', [HomeController::class, 'buscarEmpleado']);
Route::post('/asistencia/{id}/salida', [HomeController::class, 'marcarSalidaConfirmada']);

Route::prefix('admin')->name('admin.')->group(function () {
    // Login y logout
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/', [AuthController::class, 'login'])->name('login');
    // Dashboard de asistencias
    Route::get('/asistencias', [AdminController::class, 'asistencias'])->name('asistencias');
    Route::get('/asistencias/reporte', [AdminController::class, 'generarReporte'])->name('asistencias.reporte');
    // Usuarios
    Route::get('/preferencias', [UsuarioController::class, 'listarUsuarios'])->name('preferencias');
    Route::get('/usuarios/crear', [UsuarioController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/usuarios', [UsuarioController::class, 'guardarUsuario'])->name('usuarios.guardar');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::get('/preferencias/configurar', [UsuarioController::class, 'configurarData'])->name('usuarios.configurar');
    Route::post('/preferencias/configuracion', [UsuarioController::class, 'actualizarData'])->name('usuarios.data');



    // Empleados
    Route::get('/empleados', [EmpleadoController::class, 'listarEmpleados'])->name('empleados');
    Route::get('/empleados/crear', [EmpleadoController::class, 'crearEmpleado'])->name('empleados.crear');
    Route::post('/empleados', [EmpleadoController::class, 'guardarEmpleado'])->name('empleados.guardar');
    Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'editarEmpleado'])->name('empleados.editar');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'actualizarEmpleado'])->name('empleados.actualizar');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

// Logout fuera del prefix si aplica a usuarios no administradores también
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
