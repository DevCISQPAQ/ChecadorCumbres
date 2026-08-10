<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\Departamento;
use App\Models\Checada;
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

            $departamentos = Departamento::orderBy('nombre')->get();
            $conteosAsistencias = $this->obtenerConteosdeAsistencia();
            $hayFiltros = $this->hayFiltros($request);
            $asistencias = $this->listarAsistencias($request);
           

            return view('admin.asistencias.index', array_merge($conteosAsistencias, compact('asistencias', 'hayFiltros', 'departamentos')));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de Dashboard ' . $e->getMessage());
        }
    }

    public function listarAsistencias(Request $request, $paginado = true)
    {

        // Si se seleccionó SIN HORA DE ENTRADA y SIN HORA DE SALIDA
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


        $query = Asistencia::with([
            'empleado',
            'checadas'
        ]);

        $query = $this->aplicarFiltroPorDefecto($query, $request);
        $query = $this->aplicarFiltroBusqueda($query, $request);
        $query = $this->aplicarFiltroFechas($query, $request);
        $query = $this->aplicarFiltroRetardo($query, $request);
        $query = $this->aplicarFiltroHoraEntrada($query, $request);
        $query = $this->aplicarFiltroHoraSalida($query, $request);
        $query = $this->aplicarFiltroDepartamento($query, $request);
        $query = $this->aplicarFiltroEstado($query, $request);

        $query->orderByDesc('fecha');

        if ($paginado) {
            return $query->paginate(10)->withQueryString();
        }

        return $query->get(); // Obtener todos sin paginar
    }

    public function obtenerConteosdeAsistencia()
    {

        $asistenciaE = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'presente')
            ->count();

        $retardosHoy = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'retardo')
            ->count();

        $faltasHoy = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'falta')
            ->count();

        $vacacionesHoy = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'vacaciones')
            ->count();

        $permisosHoy = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'permiso')
            ->count();
        $libresHoy = Asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'libre')
            ->count();

        return compact(
            'asistenciaE',
            'retardosHoy',
            'faltasHoy',
            'vacacionesHoy',
            'permisosHoy',
            'libresHoy'
        );
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

                // Tiene entrada
                $query->whereHas('checadas', function ($q) {
                    $q->where('tipo', 'entrada');
                    $q->where('fecha_hora', today());
                });
            } else {

                // No tiene entrada
                $query->whereDoesntHave('checadas', function ($q) {
                    $q->where('tipo', 'entrada');
                    $q->where('fecha_hora', today());
                });
            }
        }

        return $query;
    }

    private function aplicarFiltroHoraSalida($query, Request $request)
    {
        if ($request->filled('hora_salida') && in_array($request->hora_salida, ['0', '1'])) {

            if ($request->hora_salida == '1') {

                $query->whereHas('checadas', function ($q) {
                    $q->where('tipo', 'salida');
                    $q->where('fecha_hora', today());
                });
            } else {

                $query->whereDoesntHave('checadas', function ($q) {
                    $q->where('tipo', 'salida');
                    $q->where('fecha_hora', today());
                });
            }
        }

        return $query;
    }

    private function aplicarFiltroDepartamento($query, Request $request)
    {
        if ($request->filled('departamento')) {

            $departamento = $request->departamento;

            $query->whereHas('empleado', function ($q) use ($departamento) {

                $q->where('departamento_id', $departamento);
            });
        }

        return $query;
    }

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
                                $empleado->departamento->nombre ?? '-',
                                $empleado->email ?? '-',
                                $asistencia->created_at ? $asistencia->created_at->format('d/m/Y') : '-',
                                $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'Sin registro',
                                $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'Sin registro',
                                $asistencia->estado ?? 'falta'
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
                        return ['N. Empleado', 'Nombre', 'Departamento', 'Correo', 'Fecha', 'Hora de entrada', 'Hora de salida', 'Estado'];
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

                        // Ajustar ancho automáticamente
                        foreach (range('A', 'H') as $column) {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
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

   
    private function obtenerAsistenciasConDiasFaltantes(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)
            : Carbon::today();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->fecha_fin)
            : Carbon::today();


        // Filtrar empleados primero
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


        // 2️⃣ Obtener asistencias en el rango
        $asistencias = Asistencia::with('empleado')
            ->whereBetween('fecha', [
                $fechaInicio->toDateString(),
                $fechaFin->toDateString()
            ])
            ->get();


        // ✅ NUEVO: Obtener checadas del periodo
        $checadas = Checada::whereBetween('fecha_hora', [
            $fechaInicio->copy()->startOfDay(),
            $fechaFin->copy()->endOfDay()
        ])
            ->get()
            ->groupBy(function ($checada) {
                return $checada->empleado_id . '_' .
                    $checada->fecha_hora->format('Y-m-d');
            });


        // ✅ NUEVO: Agregar entrada/salida a cada asistencia
        $asistencias = $asistencias->map(function ($asistencia) use ($checadas) {

            $key = $asistencia->empleado_id . '_' .
                Carbon::parse($asistencia->fecha)->format('Y-m-d');


            $checadasEmpleado = $checadas->get($key, collect());


            $entrada = $checadasEmpleado
                ->where('tipo', 'entrada')
                ->sortBy('fecha_hora')
                ->first();


            $salida = $checadasEmpleado
                ->where('tipo', 'salida')
                ->sortByDesc('fecha_hora')
                ->first();


            $asistencia->setAttribute(
                'hora_entrada',
                $entrada?->fecha_hora
            );

            $asistencia->setAttribute(
                'hora_salida',
                $salida?->fecha_hora
            );

            $asistencia->setAttribute(
                'retardo',
                $asistencia->estado === 'retardo'
            );


            return $asistencia;
        });


        // ✅ CORREGIDO: agrupar con la fecha correcta
        $asistencias = $asistencias->groupBy(
            fn($a) => $a->empleado_id . '_' .
                Carbon::parse($a->fecha)->format('Y-m-d')
        );


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
                        'fecha'         => $fecha->format('Y-m-d'),
                        'hora_entrada'  => null,
                        'hora_salida'   => null,
                        'retardo'       => null,
                        'estado'        => 'falta',
                    ]);
                }
            }
        }


        if ($request->filled('estado')) {

            $estado = $request->estado;

            $resultado = $resultado
                ->filter(function ($item) use ($estado) {
                    return $item->estado === $estado;
                })
                ->values();
        }


        // 🔹 Filtrar por retardo
        if ($request->filled('retardo')) {

            $retardo = (string) $request->retardo;

            $resultado = $resultado->filter(function ($item) use ($retardo) {

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

                return $horaEntradaFiltro === "1"
                    ? $item->hora_entrada !== null
                    : $item->hora_entrada === null;
            })->values();
        }


        // FILTRO HORA DE SALIDA
        if ($request->filled('hora_salida')) {

            $horaSalidaFiltro = $request->hora_salida;

            $resultado = $resultado->filter(function ($item) use ($horaSalidaFiltro) {

                return $horaSalidaFiltro === "1"
                    ? $item->hora_salida !== null
                    : $item->hora_salida === null;
            })->values();
        }

        return $resultado;
    }


    private function aplicarFiltroEstado($query, $request)
    {
        if ($request->filled('estado')) {


            $estadosPermitidos = [
                'presente',
                'retardo',
                'falta',
                'vacaciones',
                'permiso',
                'libre',
                'festivo',
                'sin_registro'
            ];

            $estado = $request->estado;

            if (!in_array($estado, $estadosPermitidos)) {
                return $query;
            }

            if ($estado === 'sin_registro') {
                return $query->whereNull('estado');
            }

            return $query->where('estado', $estado);
        }

        return $query;
    }


    private function hayFiltros(Request $request): bool
    {
        return $request->filled('buscar')
            || $request->filled('fecha_inicio')
            || $request->filled('fecha_fin')
            || $request->filled('departamento')
            || $request->filled('hora_entrada')
            || $request->filled('hora_salida')
            || $request->filled('retardo')
            || $request->filled('estado');
    }
}
