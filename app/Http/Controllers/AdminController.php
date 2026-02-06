<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\CarbonPeriod;


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
            $hayFiltros = $this->hayFiltros($request);

            if ($this->hayFiltros($request)) {
                $asistencias = $this->obtenerAsistenciasConDiasFaltantes($request);
            } else {
                $asistencias = $this->listarAsistencias($request);
            }

            return view('admin.asistencias.index', array_merge($conteosAsistencias, compact('asistencias', 'hayFiltros')));

            // return view('admin.asistencias.index', compact('asistencias'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de Dashboard ' . $e->getMessage());
        }
    }

    public function listarAsistencias(Request $request, $paginado = true)
    {

        // ⚠️ Si se seleccionó SIN HORA DE ENTRADA y SIN HORA DE SALIDA
        if ((string)$request->hora_entrada === '0' && (string)$request->hora_salida === '0') {
            // Filtrar empleados sin asistencia para la fecha específica (hoy o filtrada)
            $fecha = Carbon::today(); // por defecto

            if ($request->filled('fecha_inicio')) {
                $fecha = Carbon::parse($request->fecha_inicio);
            }

            $query = Empleado::whereDoesntHave('asistencias', function ($q) use ($fecha) {
                $q->whereDate('created_at', $fecha);
            });

            if ($request->filled('departamento')) {
                $query->where('departamento', $request->departamento);
            }

            if ($paginado) {
                return $query->paginate(10)->withQueryString();
            }

            return $query->get();
        }


        $query = Asistencia::with('empleado');

        $query = $this->aplicarFiltroPorDefecto($query, $request);
        $query = $this->aplicarFiltroBusqueda($query, $request);
        $query = $this->aplicarFiltroFechas($query, $request);
        $query = $this->aplicarFiltroRetardo($query, $request);
        $query = $this->aplicarFiltroHoraEntrada($query, $request);
        $query = $this->aplicarFiltroHoraSalida($query, $request);
        $query = $this->aplicarFiltroDepartamento($query, $request);

        $query->orderByDesc('created_at');

        if ($paginado) {
            return $query->paginate(10)->withQueryString();
        }

        return $query->get(); // Obtener todos sin paginar
    }

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

        $cantidadSinAsistencia = Empleado::whereDoesntHave('asistencias', function ($query) {
            $query->whereDate('created_at', Carbon::today());
        })->count();

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
                    ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(id) LIKE ?', ["%{$buscar}%"]);
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

    // public function generarReporte(Request $request)
    // {
    //     try {
    //         // Reutilizar la lógica para obtener asistencias según filtro o por defecto del día
    //         // $asistencias = $this->listarAsistencias($request, false);
    //         $asistencias = $this->obtenerAsistenciasConDiasFaltantes($request);
    //         // $asistencias = $this->obtenerAsistenciasConDiasFaltantes($request);

    //         // Llamar a la nueva función para calcular horas trabajadas
    //         $horasDecimales = $this->calcularHorasTrabajadas($asistencias);
    //         $horasFormateadas = $this->formatearHoras($horasDecimales);

    //         // Cargar vista para PDF 
    //         $pdf = PDF::loadView('admin.asistencias.reporte', compact('asistencias', 'horasFormateadas'));

    //         // Descargar o mostrar el PDF
    //         //  return $pdf->download('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
    //         return $pdf->stream('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error al ver el PDF: ' . $e->getMessage());
    //     }
    // }

    public function generarReporte(Request $request)
    {
        try {
            // Aumentar temporalmente memoria y tiempo de ejecución
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

            // Obtener asistencias con días faltantes
            $asistencias = $this->obtenerAsistenciasConDiasFaltantes($request);

            // Calcular horas trabajadas
            $horasDecimales = $this->calcularHorasTrabajadas($asistencias);
            $horasFormateadas = $this->formatearHoras($horasDecimales);

            // Renderizar la vista como HTML primero
            $html = view('admin.asistencias.reporte', compact('asistencias', 'horasFormateadas'))->render();

            // Cargar PDF desde HTML
            $pdf = PDF::loadHTML($html)
                ->setPaper('A4', 'landscape') // orientación horizontal si hay muchas columnas
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true); // si hay imágenes externas

            // Streaming, evita cargar todo en memoria
            return $pdf->stream('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }


    private function calcularHorasTrabajadas($asistencias)
    {
        $horasTotales = null;

        if ($asistencias->count() > 0) {
            $empleadoIds = $asistencias->pluck('empleado_id')->unique();

            if ($empleadoIds->count() === 1) {
                $horasTotales = 0;

                foreach ($asistencias as $asistencia) {
                    if ($asistencia->hora_entrada && $asistencia->hora_salida) {
                        try {
                            $entrada = Carbon::parse($asistencia->hora_entrada);
                            $salida = Carbon::parse($asistencia->hora_salida);

                            if ($salida->gte($entrada)) {
                                $diffInMinutes = $salida->diffInMinutes($entrada);
                            } else {
                                // Turno pasa al día siguiente
                                $diffInMinutes = $salida->addDay()->diffInMinutes($entrada);
                            }

                            $horasTotales += $diffInMinutes / 60;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }

                $horasTotales = round($horasTotales, 2);
            }
        }

        return $horasTotales;
    }

    private function formatearHoras($horasDecimales)
    {
        if ($horasDecimales === null) {
            return null;
        }

        $horas = floor(abs($horasDecimales));   // abs para evitar negativos
        $minutos = round((abs($horasDecimales) - $horas) * 60);

        return "{$horas}h {$minutos}min";
    }


    public function generarReporteExcel(Request $request)
    {
        try {
            // Obtener asistencias con días faltantes
            $asistencias = $this->obtenerAsistenciasConDiasFaltantes($request);

            // Calcular horas trabajadas
            $horasDecimales = $this->calcularHorasTrabajadas($asistencias);
            $horasFormateadas = $this->formatearHoras($horasDecimales);

            return Excel::download(
                new class($asistencias, $horasFormateadas) implements FromArray, WithHeadings, WithStyles {
                    private $asistencias;
                    private $horasFormateadas;

                    public function __construct($asistencias, $horasFormateadas)
                    {
                        $this->asistencias = $asistencias;
                        $this->horasFormateadas = $horasFormateadas;
                    }

                    public function array(): array
                    {
                        $data = [];
                        foreach ($this->asistencias as $asistencia) {
                            $empleado = $asistencia->empleado ?? $asistencia;

                            $data[] = [
                                $empleado->id ?? '-',
                                $empleado->nombres . ' ' . ($empleado->apellido_paterno ?? '') . ' ' . ($empleado->apellido_materno ?? ''),
                                $empleado->departamento ?? '-',
                                $empleado->email ?? '-',
                                $asistencia->created_at ? $asistencia->created_at->format('d/m/Y') : '-',
                                $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'Sin registro',
                                $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'Sin registro',
                                isset($asistencia->retardo) ? ($asistencia->retardo ? 'Sí' : 'No') : 'Sin registro',
                            ];
                        }

                        // Total de horas trabajadas
                        if ($this->horasFormateadas) {
                            $data[] = ['', '', '', '', 'Total de horas trabajadas', $this->horasFormateadas . ' horas', '', ''];
                        }

                        return $data;
                    }

                    public function headings(): array
                    {
                        return ['N. Empleado', 'Nombre', 'Departamento', 'Correo', 'Fecha', 'Hora de entrada', 'Hora de salida', 'Retardo'];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $highestRow = $sheet->getHighestRow();
                        $sheet->getStyle("A1:H1")->getFont()->setBold(true);
                        $sheet->getStyle("A1:H1")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('DDDDDD');
                        $sheet->getStyle("A1:H" . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("A2:H" . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        // Colores alternados filas
                        for ($row = 2; $row <= $highestRow; $row++) {
                            if ($row % 2 == 0) {
                                $sheet->getStyle("A{$row}:H{$row}")->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setRGB('F9F9F9');
                            }
                        }

                        // Resaltar "Sin registro" en rojo y Retardo
                        for ($row = 2; $row <= $highestRow; $row++) {
                            // Hora de entrada (columna F) y Hora de salida (columna G)
                            foreach (['F', 'G'] as $col) {
                                $valor = $sheet->getCell("{$col}{$row}")->getValue();
                                if ($valor === 'Sin registro') {
                                    $sheet->getStyle("{$col}{$row}")->getFont()->getColor()->setRGB('FF0000');
                                }
                            }

                            // Retardo (columna H)
                            $retardo = $sheet->getCell("H{$row}")->getValue();
                            if ($retardo === 'Sí') {
                                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('FF0000'); // rojo
                            } elseif ($retardo === 'No') {
                                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('008000'); // verde
                            } elseif ($retardo === 'Sin registro') {
                                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('FF0000'); // rojo
                            }
                        }

                        // Total de horas trabajadas en negrita
                        $sheet->getStyle("E{$highestRow}:F{$highestRow}")->getFont()->setBold(true);
                    }
                },
                'reporte_asistencias_' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar Excel: ' . $e->getMessage());
        }
    }

    //principal
    private function obtenerAsistenciasConDiasFaltantes(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)
            : Carbon::today();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->fecha_fin)
            : Carbon::today();

        // 1️⃣ Filtrar empleados primero
        $empleadosQuery = Empleado::query();

        if ($request->filled('departamento')) {
            $empleadosQuery->where('departamento', $request->departamento);
        }

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);
            $empleadosQuery->where(function ($q) use ($buscar) {
                $q->whereRaw('LOWER(nombres) LIKE ?', ["%$buscar%"])
                    ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%$buscar%"])
                    ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%$buscar%"])
                    ->orWhereRaw('LOWER(id) LIKE ?', ["%$buscar%"]);
            });
        }

        $empleados = $empleadosQuery->get();

        // 2️⃣ Obtener todas las asistencias en el rango
        $asistencias = Asistencia::with('empleado')
            ->whereBetween('created_at', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->get()
            ->groupBy(fn($a) => $a->empleado_id . '_' . $a->created_at->format('Y-m-d'));

        $periodo = CarbonPeriod::create($fechaInicio, $fechaFin);
        $resultado = collect();

        // 3️⃣ Recorrer empleados y fechas
        foreach ($empleados as $empleado) {
            foreach ($periodo as $fecha) {
                $key = $empleado->id . '_' . $fecha->format('Y-m-d');

                if (isset($asistencias[$key])) {
                    $resultado->push($asistencias[$key]->first());
                } else {
                    // Registro virtual sin asistencia
                    $resultado->push((object)[
                        'empleado'      => $empleado,
                        'empleado_id'   => $empleado->id,
                        'created_at'    => $fecha->copy(),
                        'hora_entrada'  => null,
                        'hora_salida'   => null,
                        'retardo'       => null,
                    ]);
                }
            }
        }

        // 🔹 Filtrar por retardo (si viene en request)
        if ($request->filled('retardo')) {
            $retardo = (string) $request->retardo; // "1" o "0"

            $resultado = $resultado->filter(function ($item) use ($retardo) {
                // excluir registros virtuales
                if ($item->retardo === null) {
                    return false;
                }
                return (string) $item->retardo === $retardo;
            })->values();
        }

        // FILTRO HORA DE ENTRADA
        if ($request->filled('hora_entrada')) {
            $horaEntradaFiltro = $request->hora_entrada;
            $resultado = $resultado->filter(function ($item) use ($horaEntradaFiltro) {
                return $horaEntradaFiltro === "1" ? $item->hora_entrada !== null : $item->hora_entrada === null;
            })->values();
        }

        // FILTRO HORA DE SALIDA
        if ($request->filled('hora_salida')) {
            $horaSalidaFiltro = $request->hora_salida;
            $resultado = $resultado->filter(function ($item) use ($horaSalidaFiltro) {
                return $horaSalidaFiltro === "1" ? $item->hora_salida !== null : $item->hora_salida === null;
            })->values();
        }

        return $resultado;
    }


    private function hayFiltros(Request $request): bool
    {
        return $request->filled('buscar')
            || $request->filled('fecha_inicio')
            || $request->filled('fecha_fin')
            || $request->filled('departamento')
            || $request->filled('hora_entrada')
            || $request->filled('hora_salida')
            || $request->filled('retardo');
    }

}
