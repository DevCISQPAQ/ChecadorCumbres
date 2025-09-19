<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        try {

            //throw new \PDOException('Simulando desconexión de base de datos');

            $conteos = $this->obtenerConteosPorDepartamento();
            $empleados = $this->obtenerEmpleados($request);

            return view('admin.empleados.index', array_merge($conteos, compact('empleados')));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de empleados: ' . $e->getMessage());
        }
    }


    private function obtenerPeriodoEscolar()
    {
        $hoy = Carbon::now();
        $anioInicio = $hoy->month >= 9 ? $hoy->year : $hoy->year - 1;

        return [
            'inicio' => Carbon::create($anioInicio, 10, 1)->startOfMonth(),
            'fin' => Carbon::create($anioInicio + 1, 9, 30)->endOfMonth(),
            // 'etiqueta' => 'Septiembre ' . $anioInicio . ' - Septiembre ' . ($anioInicio + 1),
            'etiqueta' => $anioInicio . '-' . ($anioInicio + 1),
        ];
    }

    private function obtenerEmpleados(Request $request)
    {
        $query = Empleado::query();  // <-- Aquí no usas all(), sino query()

        $periodo = $this->obtenerPeriodoEscolar();

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);

            // Cuando hay búsqueda, quitamos el filtro por periodo para buscar en toda la tabla
            $query->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"]);
            // } else {
            //     // Solo si NO hay búsqueda, filtramos por periodo
            //     $query->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']]);
            // }
        }

        // Ordenar los resultados
        $query->orderByDesc('created_at');

        // Finalmente paginar
        $empleados = $query->paginate(10)->withQueryString();

        return $empleados;
    }



    private function obtenerConteosPorDepartamento()
    {
        $periodo = $this->obtenerPeriodoEscolar();

        $prescolarCount = Empleado::where(function ($query) {
            $query->where('departamento', 'LIKE', '%preescolar%');
        })
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $primariaCount = Empleado::where('departamento', 'LIKE', '%primaria%')
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $secundariaCount = Empleado::where('departamento', 'LIKE', '%secundaria%')
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $totales_empleados = Empleado::whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])->count();

        return compact('prescolarCount', 'primariaCount', 'secundariaCount', 'totales_empleados');
    }
}
