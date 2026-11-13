<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Reporte Individual de Asistencias</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 25px 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 19px;
            margin: 0 0 4px 0;
        }

        h3 {
            color: #2c3e50;
            font-size: 11px;
            margin: 12px 0 7px 0;
        }

        p {
            margin: 5px 0;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 5px;
            color: #000;
        }

        th {
            background-color: #ecf0f1;
            color: #2c3e50;
            text-align: center;
            font-size: 9px;
        }

        td {
            text-align: center;
            font-size: 9px;
        }

        .encabezado,
        .encabezado td {
            border: none;
            padding: 0;
        }

        .encabezado {
            margin-bottom: 10px;
        }

        .subtitulo {
            font-size: 12px;
            text-align: center;
            margin: 0;
        }

        .datos th {
            width: 20%;
        }

        .datos td {
            width: 30%;
            font-size: 9px;
        }

        .detalle {
            table-layout: fixed;
        }

        .detalle th,
        .detalle td {
            padding: 7px 3px;
            font-size: 8px;
            vertical-align: middle;
        }

        .detalle th:nth-child(1) {
            width: 13%;
        }

        .detalle th:nth-child(2) {
            width: 11%;
        }

        .detalle th:nth-child(3) {
            width: 14%;
        }

        .detalle th:nth-child(4) {
            width: 14%;
        }

        .detalle th:nth-child(5) {
            width: 14%;
        }

        .detalle th:nth-child(6) {
            width: 17%;
        }

        .detalle th:nth-child(7) {
            width: 17%;
        }

        .resumen {
            margin-top: 4px;
        }

        .resumen th,
        .resumen td {
            padding: 6px;
            font-size: 9px;
        }

        .resumen th {
            text-align: left;
        }

        .observaciones {
            text-align: left;
            padding: 8px;
            line-height: 1.4;
        }

        .firma-container {
            margin-top: 25px;
            margin-bottom: 5px;
        }

        .firma {
            width: 45%;
            border: none;
            text-align: center;
            padding: 0 15px;
        }

        .linea-firma {
            border-top: 1px solid #000;
            padding-top: 8px;
        }

        .footer {
            font-size: 8px;
            color: #7f8c8d;
            text-align: center;
            margin-top: 5px;
            padding: 0;
            line-height: 1;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .saludo {
            margin: 7px 0;
        }

        .texto-descripcion {
            margin: 5px 0 8px 0;
        }

        .seccion {
            page-break-inside: avoid;
        }

        .firma-container,
        .firma-container tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}

    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td style="border: none; text-align: center;">
                <h2 style="font-size: 18px;">
                    Cumbres International School
                </h2>

                <p style="text-align: center; font-size: 13px;">
                    Reporte Individual de Asistencias
                </p>
            </td>
        </tr>
    </table>


    {{-- INFORMACIÓN DEL EMPLEADO --}}

    <h3>Datos del empleado</h3>

    <table>
        <tbody>

            <tr>
                <th>N. Empleado</th>
                <td>{{ $empleado->n_empleado }}</td>

                <th>Departamento</th>
                <td>{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Nombre</th>
                <td colspan="3">
                    {{ $empleado->nombres }}
                    {{ $empleado->apellido_paterno }}
                    {{ $empleado->apellido_materno }}
                </td>
            </tr>

            <tr>
                <th>Periodo</th>
                <td>
                    {{ $fechaInicio->format('d/m/Y') }}
                    al
                    {{ $fechaFin->format('d/m/Y') }}
                </td>

                <th>Generado</th>
                <td>{{ now()->format('d/m/Y H:i') }}</td>
            </tr>

        </tbody>
    </table>


    <p>
        Hola, {{ $empleado->nombres }}.
    </p>

    <p>
        Este documento contiene el registro de tus asistencias,
        entradas, salidas, retardos, faltas, permisos, vacaciones
        e incapacidades correspondientes al periodo indicado.
    </p>


    {{-- DETALLE DE ASISTENCIAS --}}

    <h3>Detalle de asistencias</h3>

    <table>

        <thead>
            <tr>
                <th>Fecha</th>
                <th>Día</th>
                <th>Hora de entrada</th>
                <th>Hora de salida</th>
                <th>Estado</th>
                <th>Minutos de retardo</th>
                <!-- <th>Horas trabajadas</th> -->
            </tr>
        </thead>

        <tbody>

            @foreach($asistencias as $registro)

            @php

            $estado = strtolower(trim($registro['estado'] ?? ''));

            $colorEstado = match($estado) {
            'presente' => 'green',
            'retardo' => '#ca8a04',
            'falta', 'sin registro' => 'red',
            'vacaciones' => 'blue',
            'permiso' => 'purple',
            'incapacidad' => '#0891b2',
            'libre' => '#0891b2',
            'festivo' => 'gray',
            default => '#6b7280',
            };

            $textoEstado = match($estado) {
            'presente' => 'Presente',
            'retardo' => 'Retardo',
            'falta' => 'Falta',
            'vacaciones' => 'Vacaciones',
            'permiso' => 'Permiso',
            'incapacidad' => 'Incapacidad',
            'libre' => 'Libre',
            'festivo' => 'Festivo',
            'sin registro' => 'Sin registro',
            default => ucfirst($estado),
            };

            @endphp

            <tr>

                <td>
                    {{ $registro['fecha']->format('d/m/Y') }}
                </td>

                <td>
                    {{ $registro['dia'] }}
                </td>

                <td>
                    {{ $registro['hora_entrada'] ?? 'Sin registro' }}
                </td>

                <td>
                    {{ $registro['hora_salida'] ?? 'Sin registro' }}
                </td>

                <td style="color: {{ $colorEstado }}; font-weight: bold;">
                    {{ $textoEstado }}
                </td>

                <td>
                    @if($estado === 'retardo')
                    {{ $registro['minutos_retardo'] }} min
                    @else
                    -
                    @endif
                </td>

               

            </tr>

            @endforeach

        </tbody>

    </table>


    {{-- RESUMEN --}}

    @php

    $registrosValidos = $asistencias->where('tipo', 'registro');

    $totalPresentes = $registrosValidos
    ->where('estado', 'presente')
    ->count();

    $totalRetardos = $registrosValidos
    ->where('estado', 'retardo')
    ->count();

    $totalFaltas = $registrosValidos
    ->where('estado', 'falta')
    ->count();

    $totalPermisos = $registrosValidos
    ->where('estado', 'permiso')
    ->count();

    $totalVacaciones = $registrosValidos
    ->where('estado', 'vacaciones')
    ->count();

    $totalIncapacidades = $registrosValidos
    ->where('estado', 'incapacidad')
    ->count();

    $totalSinRegistro = $asistencias
    ->where('tipo', 'sin_registro')
    ->count();

    $totalLibres = $asistencias
    ->where('estado', 'libre')
    ->count();

    $totalFestivos = $asistencias
    ->where('estado', 'festivo')
    ->count();

    $totalMinutosRetardo = $registrosValidos
    ->where('estado', 'retardo')
    ->sum('minutos_retardo');

    $totalHorasTrabajadas = $registrosValidos
    ->sum('horas_trabajadas');

    @endphp

    <h3>Resumen de asistencia</h3>

    <table class="resumen">

        <tbody>

            <tr>
                <th>Asistencias normales</th>
                <td>{{ $totalPresentes }}</td>

                <th>Retardos</th>
                <td>{{ $totalRetardos }}</td>
            </tr>

            <tr>
                <th>Faltas registradas</th>
                <td>{{ $totalFaltas }}</td>

                <th>Días sin registro</th>
                <td>{{ $totalSinRegistro }}</td>
            </tr>

            <tr>
                <th>Permisos</th>
                <td>{{ $totalPermisos }}</td>

                <th>Vacaciones</th>
                <td>{{ $totalVacaciones }}</td>
            </tr>

            <tr>
                <th>Incapacidades</th>
                <td>{{ $totalIncapacidades }}</td>

                <th>Días libres</th>
                <td>{{ $totalLibres }}</td>
            </tr>

            <tr>
                <th>Días festivos</th>
                <td>{{ $totalFestivos }}</td>

                <th>Minutos de retardo</th>
                <td>{{ $totalMinutosRetardo }} min</td>
            </tr>

            <!-- <tr>
                <th>Total de horas trabajadas</th>
                <td colspan="3">
                    {{ number_format((float) $totalHorasTrabajadas, 2) }} horas
                </td>
            </tr> -->

        </tbody>

    </table>


    {{-- OBSERVACIONES --}}

    <h3>Observaciones</h3>

    <table>
        <tr>
            <td class="observaciones">
                @foreach($registrosValidos as $registro)
                @if(!empty($registro['observaciones']))
                <strong>
                    {{ $registro['fecha']->format('d/m/Y') }}:
                </strong>
                {{ $registro['observaciones'] }}
                <br>
                @endif
                @endforeach
            </td>
        </tr>
    </table>


    {{-- FIRMAS --}}

    <h3>Acuse de recibido</h3>

    <p>
        Mediante mi firma hago constar que he recibido y revisado
        el presente reporte de asistencia correspondiente al periodo indicado.
        Mi firma acredita únicamente la recepción del documento y no implica
        necesariamente mi conformidad con todos los registros.
    </p>

    <table class="firma-container">

        <tr>

            <td class="firma">

                <div class="linea-firma">

                    {{ $empleado->nombres }}
                    {{ $empleado->apellido_paterno }}
                    {{ $empleado->apellido_materno }}

                    <br>

                    <strong>Firma del empleado</strong>

                </div>

            </td>

            <td style="width: 10%; border: none;"></td>

            <td class="firma">

                <div class="linea-firma">

                    <strong>Nombre y firma</strong>

                    <br>

                    Recursos Humanos

                </div>

            </td>

        </tr>

    </table>


    {{-- PIE DE PÁGINA --}}

    <footer class="footer">

        <div>
            &copy; {{ date('Y') }} Cumbres International School.
            Todos los derechos reservados.
        </div>

        <div>
            Documento generado automáticamente por el sistema.
        </div>

    </footer>

</body>

</html>