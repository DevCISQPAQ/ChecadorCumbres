<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte Semanal de Retardos y Asistencias</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 700px; margin: auto; background-color: #ffffff; border-radius: 6px;">

        {{-- ================================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ================================================= --}}

        <tr>
            <td style="background-color: #2c3e50; color: #ffffff; padding: 20px; text-align: center;">

                <h2 style="margin: 0;">
                    📋 Reporte Semanal de Retardos y Asistencias
                </h2>

            </td>
        </tr>


        {{-- ================================================= --}}
        {{-- CONTENIDO --}}
        {{-- ================================================= --}}

        <tr>

            <td style="padding: 20px;">

                <p>Hola,</p>

                <p>
                    Este es el resumen semanal de retardos y faltas de asistencia.
                </p>

                {{-- PERIODO --}}

                @if(isset($inicioSemana) && isset($finSemana))

                <p style="color: #555;">

                    <strong>Periodo:</strong>

                    {{ $inicioSemana->format('d/m/Y') }}

                    al

                    {{ $finSemana->format('d/m/Y') }}

                </p>

                @endif


                {{-- ================================================= --}}
                {{-- RETARDOS --}}
                {{-- ================================================= --}}

                <h3 style="color: #2c3e50; margin-top: 25px;">
                    ⏰ Empleados con Retardos
                </h3>


                @if($retardos->isEmpty())

                <p style="font-style: italic; color: #666;">
                    No hay empleados con retardos esta semana.
                </p>

                @else

                <table width="100%" cellpadding="0" cellspacing="0"
                    style="border-collapse: collapse; margin-bottom: 30px;">

                    <thead>

                        <tr>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                N. Empleado
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Empleado
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Fecha
                            </th>

                            <th align="center"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Retardo
                            </th>

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

                            <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                {{ $empleado->n_empleado }}
                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee;">

                                {{ $empleado->nombres }}
                                {{ $empleado->apellido_paterno }}

                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee;">

                                {{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}

                            </td>


                            <td align="center"
                                style="padding: 8px; border-bottom: 1px solid #eee; color: #d35400;">

                                {{ $asistencia->minutos_retardo }} min

                            </td>

                        </tr>

                        @endforeach


                        {{-- TOTAL DEL EMPLEADO --}}

                        <tr>

                            <td colspan="3"
                                align="right"
                                style="padding: 8px; background-color: #fafafa; font-weight: bold;">

                                Total de minutos:

                            </td>

                            <td align="center"
                                style="padding: 8px; background-color: #fafafa; font-weight: bold;">

                                {{ $totalMinutos }} min

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

                @endif


                {{-- ================================================= --}}
                {{-- FALTAS --}}
                {{-- ================================================= --}}

                <h3 style="color: #2c3e50; margin-top: 25px;">
                    ⚠️ Empleados con Faltas o Sin Registro
                </h3>


                @if($empleadosSinAsistencia->isEmpty())

                <p style="font-style: italic; color: #666;">
                    No hay faltas ni empleados sin registro de asistencia esta semana.
                </p>

                @else

                <table width="100%" cellpadding="0" cellspacing="0"
                    style="border-collapse: collapse; margin-bottom: 30px;">

                    <thead>

                        <tr>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                N. Empleado
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Empleado
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Fecha
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Día
                            </th>

                            <th align="left"
                                style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">
                                Motivo
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($empleadosSinAsistencia as $registro)

                        @php

                        $empleado = $registro['empleado'];

                        @endphp


                        @foreach ($registro['faltas'] as $falta)

                        <tr>

                            <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                {{ $empleado->n_empleado }}
                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee;">

                                {{ $empleado->nombres }}
                                {{ $empleado->apellido_paterno }}

                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee;">

                                {{ \Carbon\Carbon::parse($falta['fecha'])->format('d/m/Y') }}

                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee;">

                                {{ ucfirst($falta['dia']) }}

                            </td>


                            <td style="padding: 8px; border-bottom: 1px solid #eee; color: #c0392b;">

                                {{ $falta['motivo'] }}

                            </td>

                        </tr>

                        @endforeach

                        @endforeach

                    </tbody>

                </table>

                @endif


                {{-- ================================================= --}}
                {{-- RESUMEN --}}
                {{-- ================================================= --}}

                <h3 style="color: #2c3e50; margin-top: 25px;">
                    📊 Resumen
                </h3>


                <table width="100%" cellpadding="0" cellspacing="0"
                    style="border-collapse: collapse; margin-bottom: 25px;">

                    <tr>

                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                            Empleados con retardos
                        </td>

                        <td align="right"
                            style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">

                            {{ $retardos->count() }}

                        </td>

                    </tr>


                    <tr>

                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                            Total de retardos
                        </td>

                        <td align="right"
                            style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">

                            {{ $retardos->flatten()->count() }}

                        </td>

                    </tr>


                    <tr>

                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                            Total de minutos de retardo
                        </td>

                        <td align="right"
                            style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">

                            {{ $retardos->flatten()->sum('minutos_retardo') }} min

                        </td>

                    </tr>


                    <tr>

                        <td style="padding: 8px; border-bottom: 1px solid #eee;">
                            Empleados con faltas
                        </td>

                        <td align="right"
                            style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">

                            {{ $empleadosSinAsistencia->count() }}

                        </td>

                    </tr>


                    <tr>

                        <td style="padding: 8px;">
                            Total de faltas / días sin asistencia
                        </td>

                        <td align="right"
                            style="padding: 8px; font-weight: bold;">

                            {{ $empleadosSinAsistencia->sum(fn ($registro) => count($registro['faltas'])) }}

                        </td>

                    </tr>

                </table>


                {{-- ================================================= --}}
                {{-- MENSAJE FINAL --}}
                {{-- ================================================= --}}

                <p style="margin-top: 20px;">
                    Por favor toma las medidas correspondientes.
                </p>

                <p style="color: #999; font-size: 12px;">
                    Este es un correo automático. No respondas a este mensaje.
                </p>

            </td>

        </tr>


        {{-- ================================================= --}}
        {{-- FOOTER --}}
        {{-- ================================================= --}}

        <tr>

            <td style="background-color: #ecf0f1;
                       color: #7f8c8d;
                       text-align: center;
                       padding: 10px;
                       border-bottom-left-radius: 6px;
                       border-bottom-right-radius: 6px;
                       font-size: 12px;">

                &copy; {{ date('Y') }}
                Cumbres Querétaro.
                Todos los derechos reservados.

            </td>

        </tr>

    </table>

</body>

</html>