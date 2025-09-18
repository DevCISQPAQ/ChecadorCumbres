<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
     public function index(Request $request)
    {
        try {

            //throw new \PDOException('Simulando desconexión de base de datos');


            return view('admin.empleados.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de estudiantes: ' . $e->getMessage());
        }
    }
}
