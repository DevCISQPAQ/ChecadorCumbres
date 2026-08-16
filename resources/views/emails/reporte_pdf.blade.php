<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>Reporte Semanal de Retardos y Asistencias</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            background-color: #fff;
            color: #000;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        h3 {
            color: #2c3e50;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        p {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 7px;
            color: #000;
        }

        th {
            background-color: #ecf0f1;
            text-align: left;
            color: #2c3e50;
        }

        .center {
            text-align: center;
        }

        .total {
            font-weight: bold;
        }

        .footer {
            font-size: 10px;
            color: #7f8c8d;
            text-align: center;
            margin-top: 50px;
        }

        .sin-registro {
            color: #c0392b;
        }

        .retardo {
            color: #d35400;
        }
    </style>
</head>

<body>

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <h2>Reporte Semanal de Retardos y Asistencias</h2>

    <p>
        <strong>Periodo:</strong>
        {{ $inicioSemana->format('d/m/Y') }}
        al
        {{ $finSemana->format('d/m/Y') }}
    </p>

    <p>
        Hola,
    </p>

    <p>
        Este es el resumen semanal de retardos y faltas de asistencia
        correspondientes al periodo indicado.
    </p>


    {{-- ========================================================= --}}
    {{-- RETARDOS --}}
    {{-- ========================================================= --}}

    <h3>Empleados con Retardos</h3>

    @if($retardos->isEmpty())

    <p style="font-style: italic; color: #666;">
        No hay empleados con retardos esta semana.
    </p>

    @else

    <table>

        <thead>
            <tr>
                <th>N. Empleado</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Minutos de retardo</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($retardos as $asistenciasEmpleado)

            @php
            $empleado = $asistenciasEmpleado->first()->empleado;
            $totalMinutos = $asistenciasEmpleado->sum('minutos_retardo');
            @endphp

            @foreach ($asistenciasEmpleado->sortBy('fecha') as $asistencia)

            <tr>

                <td>
                    {{ $empleado->n_empleado }}
                </td>

                <td>
                    {{ $empleado->nombres }}
                    {{ $empleado->apellido_paterno }}
                    {{ $empleado->apellido_materno }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}
                </td>

                <td class="center retardo">
                    {{ $asistencia->minutos_retardo }} min
                </td>

            </tr>

            @endforeach

            {{-- Total del empleado --}}

            <tr>

                <td colspan="3" class="total" style="text-align: right;">
                    Total de minutos de retardo:
                </td>

                <td class="center total">
                    {{ $totalMinutos }} min
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @endif


    {{-- ========================================================= --}}
    {{-- FALTAS --}}
    {{-- ========================================================= --}}

    <h3>Empleados con Faltas o Sin Registro de Asistencia</h3>

    @if($empleadosSinAsistencia->isEmpty())

    <p style="font-style: italic; color: #666;">
        No hay faltas ni empleados sin registro de asistencia
        esta semana.
    </p>

    @else

    <table>

        <thead>

            <tr>
                <th>N. Empleado</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Día</th>
                <th>Motivo</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($empleadosSinAsistencia as $registro)

            @php
            $empleado = $registro['empleado'];
            @endphp

            @foreach ($registro['faltas'] as $falta)

            <tr>

                <td>
                    {{ $empleado->n_empleado }}
                </td>

                <td>
                    {{ $empleado->nombres }}
                    {{ $empleado->apellido_paterno }}
                    {{ $empleado->apellido_materno }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($falta['fecha'])->format('d/m/Y') }}
                </td>

                <td>
                    {{ ucfirst($falta['dia']) }}
                </td>

                <td class="sin-registro">
                    {{ $falta['motivo'] }}
                </td>

            </tr>

            @endforeach

            @endforeach

        </tbody>

    </table>

    @endif


    {{-- ========================================================= --}}
    {{-- RESUMEN --}}
    {{-- ========================================================= --}}

    <h3>Resumen</h3>

    <table>

        <tbody>

            <tr>
                <th>Empleados con retardos</th>
                <td class="center">
                    {{ $retardos->count() }}
                </td>
            </tr>

            <tr>
                <th>Total de retardos</th>
                <td class="center">
                    {{ $retardos->flatten()->count() }}
                </td>
            </tr>

            <tr>
                <th>Total de minutos de retardo</th>
                <td class="center">
                    {{ $retardos->flatten()->sum('minutos_retardo') }} min
                </td>
            </tr>

            <tr>
                <th>Empleados con faltas</th>
                <td class="center">
                    {{ $empleadosSinAsistencia->count() }}
                </td>
            </tr>

            <tr>
                <th>Total de faltas / días sin asistencia</th>
                <td class="center">
                    {{ $empleadosSinAsistencia->sum(fn ($registro) => count($registro['faltas'])) }}
                </td>
            </tr>

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- MENSAJE FINAL --}}
    {{-- ========================================================= --}}

    <p>
        Por favor toma las medidas correspondientes.
    </p>

    <p style="font-size: 10px; color: #999;">
        Este es un documento generado automáticamente.
        No responda a este mensaje.
    </p>

    <div class="footer">
        &copy; {{ date('Y') }} Cumbres Querétaro.
        Todos los derechos reservados.
    </div>

</body>

</html>