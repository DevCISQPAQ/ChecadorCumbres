
@php
    $empleadoUnico = null;
    $todosMismoEmpleado = true;

    if (isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Empleado) {
        foreach ($asistencias as $empleado) {
            if (is_null($empleadoUnico)) {
                $empleadoUnico = $empleado;
            } elseif ($empleadoUnico->id !== $empleado->id) {
                $todosMismoEmpleado = false;
                break;
            }
        }
    } elseif (isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Asistencia) {
        foreach ($asistencias as $asistencia) {
            if (is_null($empleadoUnico)) {
                $empleadoUnico = $asistencia->empleado;
            } elseif ($empleadoUnico->id !== $asistencia->empleado->id) {
                $todosMismoEmpleado = false;
                break;
            }
        }
    }

    $totalRetardos = 0;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}" type="text/css">
    <title>Reporte de Asistencias</title>

    <style>
        body {
            color: #374151;
            font-family: sans-serif;
            font-size: 11px;
        }

        .products {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .products thead {
            background-color: #f3f4f6;
        }

        .products th {
            color: #374151;
            font-weight: bold;
            padding: 10px 6px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .products td {
            padding: 9px 6px;
            border: 1px solid #e5e7eb;
            text-align: center;
            color: #4b5563;
        }

        .products tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .products tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .products tfoot td {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }

        .header-info {
            margin-top: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <table class="w-full">
        <tr>
            <td class="w-half">
                 <img src="{{ public_path('img/escudo-gris.svg') }}"
                    style="width: .5rem;">
            </td>

            <td class="w-half" style="text-align: right;">
                <h2 style="color: #374151; margin: 0;">
                    Cumbres International School
                </h2>
            </td>
        </tr>
    </table>

    {{-- INFORMACIÓN DEL REPORTE --}}
    <div class="header-info">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <h3 style="font-size: 16px; color: #1f2937; margin-bottom: 5px;">
                        Reporte de Asistencias
                    </h3>

                    @if($todosMismoEmpleado && $empleadoUnico)
                        <p>
                            <strong>Empleado:</strong>
                            {{ $empleadoUnico->nombres }}
                            {{ $empleadoUnico->apellido_paterno }}
                            {{ $empleadoUnico->apellido_materno }}
                        </p>
                    @endif
                </td>

                <td class="w-half" style="text-align: right;">
                    <p>
                        <strong>Fecha de generación:</strong>
                        {{ now()->format('d/m/Y') }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABLA --}}
    <div class="margin-top">

        <table class="products">

            <thead>
                <tr>
                    <th>N. Empleado</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Fecha</th>
                    <th>Hora de entrada</th>
                    <th>Hora de salida</th>
                    <th>Estado</th>
                </tr>
            </thead>

            <tbody>

                @forelse($asistencias as $asistencia)

                    @php
                        $empleado = $asistencia->empleado;

                        $finDeSemana = $asistencia->estado === 'libre'
                            && !$asistencia->hora_entrada
                            && !$asistencia->hora_salida
                            && in_array(date('N', strtotime($asistencia->fecha)), [6, 7]);

                        if ($asistencia->estado === 'retardo') {
                            $totalRetardos++;
                        }

                        $colorEstado = match($asistencia->estado) {
                            'presente' => '#15803d',
                            'retardo' => '#ca8a04',
                            'falta' => '#dc2626',
                            'vacaciones' => '#2563eb',
                            'permiso' => '#9333ea',
                            'libre' => '#0891b2',
                            'incapacidad' => '#0891b2',
                            'festivo' => '#6b7280',
                            default => '#6b7280',
                        };

                        $textoEstado = match($asistencia->estado) {
                            'presente' => 'Presente',
                            'retardo' => 'Retardo',
                            'falta' => 'Falta',
                            'vacaciones' => 'Vacaciones',
                            'permiso' => 'Permiso',
                            'incapacidad' => 'Incapacidad',
                            'libre' => 'Libre',
                            'festivo' => 'Festivo',
                            default => 'Sin registro',
                        };
                    @endphp

                    <tr>
                        <td>
                            {{ $asistencia->n_empleado ?? ($empleado->n_empleado ?? '-') }}
                        </td>

                        <td>
                            {{ $empleado
                                ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno
                                : 'N/A' }}
                        </td>

                        <td>
                            {{ $empleado->departamento->nombre ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $asistencia->created_at
                                ? $asistencia->created_at->format('d/m/Y')
                                : '-' }}
                        </td>

                        <td @if(!$asistencia->hora_entrada && $asistencia->estado !== 'libre') style="color: #dc2626;" @endif>
                            {{ $asistencia->estado === 'libre'
                                ? ($finDeSemana ? 'Fin de semana' : 'Libre')
                                : ($asistencia->hora_entrada
                                    ? \Carbon\Carbon::parse($asistencia->hora_entrada)->format('H:i')
                                    : 'Sin registro') }}
                        </td>

                        <td @if(!$asistencia->hora_salida && $asistencia->estado !== 'libre') style="color: #dc2626;" @endif>
                            {{ $asistencia->estado === 'libre'
                                ? ($finDeSemana ? 'Fin de semana' : 'Libre')
                                : ($asistencia->hora_salida
                                    ? \Carbon\Carbon::parse($asistencia->hora_salida)->format('H:i')
                                    : 'Sin registro') }}
                        </td>

                        <td style="color: {{ $colorEstado }}; font-weight: bold;">
                            {{ $textoEstado }}
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            No se encontraron registros.
                        </td>
                    </tr>

                @endforelse

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right; padding-right: 12px;">
                        Total de retardos: {{ $totalRetardos }}
                    </td>
                </tr>

                @if(isset($horasFormateadas))
                    <tr>
                        <td colspan="7" style="text-align: right; padding-right: 12px;">
                            Total de horas trabajadas: {{ $horasFormateadas }} horas
                        </td>
                    </tr>
                @endif
            </tfoot>

        </table>

    </div>

    {{-- PIE DE PÁGINA --}}
    <footer class="footer">
        <div>&copy; Cumbres International School</div>
        <div>Documento generado automáticamente por el sistema.</div>
    </footer>

</body>
</html>
