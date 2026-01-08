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
            $asistencias = $this->listarAsistencias($request);

            return view('admin.asistencias.index', array_merge($conteosAsistencias, compact('asistencias')));

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

    public function generarReporte(Request $request)
    {
        try {
            // Reutilizar la lógica para obtener asistencias según filtro o por defecto del día
            $asistencias = $this->listarAsistencias($request, false);

            // Llamar a la nueva función para calcular horas trabajadas
            $horasDecimales = $this->calcularHorasTrabajadas($asistencias);
            $horasFormateadas = $this->formatearHoras($horasDecimales);

            // Cargar vista para PDF 
            $pdf = PDF::loadView('admin.asistencias.reporte', compact('asistencias', 'horasFormateadas'));

            // Descargar o mostrar el PDF
            //  return $pdf->download('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
            return $pdf->stream('reporte_asistencias_' . now()->format('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al ver el PDF: ' . $e->getMessage());
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
            // Reutiliza tu lógica para obtener asistencias filtradas
            $asistencias = $this->listarAsistencias($request, false);

            // Calcula horas trabajadas
            $horasDecimales = $this->calcularHorasTrabajadas($asistencias);
            $horasFormateadas = $this->formatearHoras($horasDecimales);

            // Exportación de Excel
            return Excel::download(
                new class($asistencias, $horasFormateadas) implements FromArray, WithHeadings, WithStyles {
                    private $asistencias;
                    private $horasFormateadas;

                    public function __construct($asistencias, $horasFormateadas)
                    {
                        $this->asistencias = $asistencias;
                        $this->horasFormateadas = $horasFormateadas;
                    }

                    // Contenido del Excel
                    public function array(): array
                    {
                        $data = [];
                        foreach ($this->asistencias as $asistencia) {
                            $empleado = $asistencia->empleado ?? $asistencia;

                            $data[] = [
                                $empleado->id ?? '-',
                                $empleado->nombres . ' ' . ($empleado->apellido_paterno ?? '') . ' ' . ($empleado->apellido_materno ?? ''),
                                $empleado->departamento ?? '-',
                                $asistencia->created_at ? $asistencia->created_at->format('d/m/Y') : '-',
                                $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'Sin registro',
                                $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'Sin registro',
                                isset($asistencia->retardo) ? ($asistencia->retardo ? 'Sí' : 'No') : 'Sin registro',

                            ];
                        }

                        // Agregar fila de total de horas trabajadas al final
                        if ($this->horasFormateadas) {
                            $data[] = ['', '', '', '', 'Total de horas trabajadas', $this->horasFormateadas . ' horas', ''];
                        }

                        return $data;
                    }

                    // Encabezados
                    public function headings(): array
                    {
                        return ['N. Empleado', 'Nombre', 'Departamento', 'Fecha', 'Hora de entrada', 'Hora de salida', 'Retardo'];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $highestRow = $sheet->getHighestRow();

                        // Estilo encabezados: fondo gris, negrita, bordes
                        $sheet->getStyle("A1:G1")->getFont()->setBold(true);
                        $sheet->getStyle("A1:G1")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('DDDDDD');
                        $sheet->getStyle("A1:G1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                        $sheet->getStyle("A1:G" . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Bordes para todo el contenido
                        $sheet->getStyle("A2:G" . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        // Colores alternados por fila (tipo tabla)
                        for ($row = 2; $row <= $highestRow; $row++) {
                            if ($row % 2 == 0) {
                                $sheet->getStyle("A{$row}:G{$row}")->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setRGB('F9F9F9');
                            }
                        }

                        // Resaltar retardos en rojo y no retardos en verde
                        for ($row = 2; $row < $highestRow; $row++) {
                            $retardo = $sheet->getCell("F{$row}")->getValue();
                            if ($retardo === 'Sí') {
                                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('FF0000');
                            } elseif ($retardo === 'No') {
                                $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('008000');
                            }
                        }

                        // Fila de total de horas trabajadas en negrita
                        $sheet->getStyle("E{$highestRow}:F{$highestRow}")->getFont()->setBold(true);
                    }
                },
                'reporte_asistencias_' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Error al generar Excel: ' . $e->getMessage()
            );
        }
    }
}
