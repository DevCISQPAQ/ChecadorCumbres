<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'showWelcome']);
Route::get('/empleado/{id}/buscar', [HomeController::class, 'buscarEmpleado']);

Route::prefix('admin')->name('admin.')->group(function () {
    // Login y logout
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/', [AuthController::class, 'login'])->name('login');
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'listarUsuarios'])->name('usuarios');
    Route::get('/usuarios/crear', [UsuarioController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::post('/usuarios', [UsuarioController::class, 'guardarUsuario'])->name('usuarios.guardar');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    // Empleados
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadoController::class, 'crearEmpleado'])->name('empleados.crear');
    Route::post('/empleados', [EmpleadoController::class, 'guardarEmpleado'])->name('empleados.guardar');
    Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'editarEmpleado'])->name('empleados.editar');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'actualizarEmpleado'])->name('empleados.actualizar');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

// Logout fuera del prefix si aplica a usuarios no administradores también
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');