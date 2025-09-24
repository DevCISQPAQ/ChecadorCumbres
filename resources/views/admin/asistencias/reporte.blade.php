<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencias</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Reporte de Asistencias</h2>
    <table>
        <thead>
            <tr>
                <th>IdEmpleado</th>
                <th>Nombre</th>
                <th>Hora de entrada</th>
                <th>Hora de salida</th>
                <th>Retardo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistencias as $asistencia)
                @php
                    $empleado = $asistencia->empleado;
                @endphp
                <tr>
                    <td>{{ $asistencia->empleado_id }}</td>
                    <td>{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                    <td>{{ $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'N/A' }}</td>
                    <td>{{ $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'N/A' }}</td>
                    <td>{{ $asistencia->retardo ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
