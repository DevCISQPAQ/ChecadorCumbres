<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $query = Asistencia::with('empleado');

        $query = $this->aplicarFiltroPorDefecto($query, $request);
        $query = $this->aplicarFiltroBusqueda($query, $request);
        $query = $this->aplicarFiltroFechas($query, $request);
        $query = $this->aplicarFiltroRetardo($query, $request);
        $query = $this->aplicarFiltroHoraEntrada($query, $request);
        $query = $this->aplicarFiltroHoraSalida($query, $request);
        $query = $this->aplicarFiltroDepartamento($query, $request);

        $query->orderByDesc('created_at');

        return $query->paginate(10)->withQueryString();
    }

    // public function listarAsistencias(Request $request)
    // {
    //     // Iniciamos el query builder, sin llamar a get()
    //     $query = Asistencia::with('empleado');

    //     // Solo filtrar por hoy si NO hay búsqueda ni filtro de fechas
    //     if (!$request->filled('buscar') && !$request->filled('fecha_inicio') && !$request->filled('fecha_fin')) {
    //         $query->whereDate('created_at', Carbon::today());
    //     }

    //     if ($request->filled('buscar')) {
    //         $query = Asistencia::with('empleado');
    //         $buscar = strtolower($request->buscar);

    //         // Buscamos en los campos del empleado relacionados usando whereHas
    //         $query->whereHas('empleado', function ($q) use ($buscar) {
    //             $q->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
    //                 ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
    //                 ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"]);
    //         });
    //     }

    //     // Filtrar por rango de fecha (fecha_inicio y fecha_fin sobre created_at)
    //     if ($request->filled('fecha_inicio')) {
    //         $query->whereDate('created_at', '>=', $request->fecha_inicio);
    //     }
    //     if ($request->filled('fecha_fin')) {
    //         $query->whereDate('created_at', '<=', $request->fecha_fin);
    //     }

    //     // Filtrar por retardo (1 o 0)
    //     if ($request->filled('retardo') && in_array($request->retardo, ['0', '1'])) {
    //         $query->where('retardo', $request->retardo);
    //     }

    //     // Filtrar por existencia de hora_entrada (1 = con hora, 0 = sin hora)
    //     if ($request->filled('hora_entrada') && in_array($request->hora_entrada, ['0', '1'])) {
    //         if ($request->hora_entrada == '1') {
    //             $query->whereNotNull('hora_entrada');
    //         } else {
    //             $query->whereNull('hora_entrada');
    //         }
    //     }

    //     // Filtrar por existencia de hora_salida (1 = con hora, 0 = sin hora)
    //     if ($request->filled('hora_salida') && in_array($request->hora_salida, ['0', '1'])) {
    //         if ($request->hora_salida == '1') {
    //             $query->whereNotNull('hora_salida');
    //         } else {
    //             $query->whereNull('hora_salida');
    //         }
    //     }

    //     // Ordenamos por fecha descendente
    //     $query->orderByDesc('created_at');

    //     // Finalmente paginamos la consulta
    //     $asistencias = $query->paginate(10)->withQueryString();

    //     return $asistencias;
    // }

    public function obtenerConteosdeAsistencia()
    {

        $asistenciaE = Asistencia::whereNotNull('hora_entrada')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $asistenciaS = Asistencia::whereNotNull('hora_salida')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $retardosHoy = Asistencia::where('retardo', 1)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $cantidadSinAsistencia = Empleado::doesntHave('asistencias')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return compact('asistenciaE', 'asistenciaS', 'retardosHoy', 'cantidadSinAsistencia');
    }

    private function aplicarFiltroPorDefecto($query, Request $request)
    {
        if (
            !$request->filled('buscar') &&
            !$request->filled('fecha_inicio') &&
            !$request->filled('fecha_fin')
        ) {
            $query->whereDate('created_at', Carbon::today());
        }

        return $query;
    }

    private function aplicarFiltroBusqueda($query, Request $request)
    {
        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);

            $query->whereHas('empleado', function ($q) use ($buscar) {
                $q->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"]);
            });
        }

        return $query;
    }

    private function aplicarFiltroFechas($query, Request $request)
    {
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        return $query;
    }

    private function aplicarFiltroRetardo($query, Request $request)
    {
        if ($request->filled('retardo') && in_array($request->retardo, ['0', '1'])) {
            $query->where('retardo', $request->retardo);
        }

        return $query;
    }

    private function aplicarFiltroHoraEntrada($query, Request $request)
    {
        if ($request->filled('hora_entrada') && in_array($request->hora_entrada, ['0', '1'])) {
            if ($request->hora_entrada == '1') {
                $query->whereNotNull('hora_entrada');
            } else {
                $query->whereNull('hora_entrada');
            }
        }

        return $query;
    }

    private function aplicarFiltroHoraSalida($query, Request $request)
    {
        if ($request->filled('hora_salida') && in_array($request->hora_salida, ['0', '1'])) {
            if ($request->hora_salida == '1') {
                $query->whereNotNull('hora_salida');
            } else {
                $query->whereNull('hora_salida');
            }
        }

        return $query;
    }

    private function aplicarFiltroDepartamento($query, Request $request)
    {
        if ($request->filled('departamento')) {
            $departamento = $request->departamento;

            $query->whereHas('empleado', function ($q) use ($departamento) {
                $q->where('departamento', $departamento);
            });
        }

        return $query;
    }

    public function generarReporte(Request $request)
    {
        try {
            // Reutilizar la lógica para obtener asistencias según filtro o por defecto del día
            $asistencias = $this->listarAsistencias($request);

            // Cargar vista para PDF (puede ser similar a la vista web, pero más sencilla para PDF)
            $pdf = PDF::loadView('admin.asistencias.reporte', compact('asistencias'));

            // Descargar o mostrar el PDF
            //  return $pdf->download('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
            return $pdf->stream('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al ver el PDF: ' . $e->getMessage());
        }
    }


}
