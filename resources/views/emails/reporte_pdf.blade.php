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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            color: #000;
        }

        th {
            background-color: #ecf0f1;
            text-align: left;
            color: #2c3e50;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        p {
            margin-top: 20px;
        }

        .footer {
            font-size: 10px;
            color: #7f8c8d;
            text-align: center;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <h2>Reporte Semanal de Retardos y Asistencias</h2>

    <p>Hola,</p>
    <p>Este es el resumen semanal:</p>

    {{-- Tabla Retardos --}}
    <h3>Empleados con Retardos</h3>
    @if($retardos->isEmpty())
    <p style="font-style: italic; color: #666;">No hay empleados con retardos esta semana.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>N. Empleado</th>
                <th>Empleado</th>
                <th>Retardos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($retardos as $asistenciasEmpleado)
            <tr>
                <td>{{ $asistenciasEmpleado->first()->empleado->id }}</td>
                <td>{{ $asistenciasEmpleado->first()->empleado->nombres }} {{ $asistenciasEmpleado->first()->empleado->apellido_paterno }}</td>
                <td style="text-align: center;">{{ $asistenciasEmpleado->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Tabla Empleados sin Asistencia --}}
    <h3>Empleados sin Registro de Asistencia</h3>
    @if($empleadosSinAsistencia->isEmpty())
    <p style="font-style: italic; color: #666;">Todos los empleados registraron asistencia esta semana.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>N. Empleado</th>
                <th>Empleado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empleadosSinAsistencia as $empleado)
            <tr>
                <td>{{ $empleado->id }}</td>
                <td>{{ $empleado->nombres }} {{ $empleado->apellido_paterno }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <p>Por favor toma las medidas correspondientes.</p>

    <p style="font-size: 10px; color: #999;">Este es un documento generado automáticamente. No responda a este mensaje.</p>

    <div class="footer">
        &copy; {{ date('Y') }} Cumbres Querétaro. Todos los derechos reservados.
    </div>
</body>

</html>