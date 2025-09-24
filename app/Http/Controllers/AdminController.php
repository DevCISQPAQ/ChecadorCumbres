<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function asistencias(Request $request)
    {
        try {

            if (!Auth::check()) {
                abort(403, 'Acceso no autorizado');
            }

            $user = Auth::user();

            // if (!$user->is_admin) {
            //     return redirect()->route('estudiantes.index');
            // }

            $conteosAsistencias = $this->obtenerConteosdeAsistencia();
            // $conteosRetardos = $this->obtenerConteosdeRetardos();
            // $conteosSalidas = $this->obtenerConteosdeSalidas();
            // $conteosFaltantes = $this->obtenerConteosdeFaltantes();

            $asistencias = $this->listarAsistencias($request);

            return view('admin.asistencias.index', array_merge($conteosAsistencias, compact('asistencias')));

            // return view('admin.asistencias.index', compact('asistencias'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de Dashboard ' . $e->getMessage());
        }
    }


    public function listarAsistencias(Request $request)
    {
        // Iniciamos el query builder, sin llamar a get()
        $query = Asistencia::with('empleado')
            ->whereDate('created_at', Carbon::today());

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);

            // Buscamos en los campos del empleado relacionados usando whereHas
            $query->whereHas('empleado', function ($q) use ($buscar) {
                $q->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"]);
            });
        }

        // Ordenamos por fecha descendente
        $query->orderByDesc('created_at');

        // Finalmente paginamos la consulta
        $asistencias = $query->paginate(10)->withQueryString();

        return $asistencias;
    }

    public function obtenerConteosdeAsistencia()
    {

        $asistenciaE = Asistencia::whereNotNull('hora_entrada')
            ->count();

        $asistenciaS = Asistencia::whereNotNull('hora_salida')
            ->count();

        $retardosHoy = Asistencia::where('retardo', 1)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $cantidadSinAsistencia = Empleado::doesntHave('asistencias')
            ->count();

        return compact('asistenciaE', 'asistenciaS', 'retardosHoy', 'cantidadSinAsistencia');
    }
}
