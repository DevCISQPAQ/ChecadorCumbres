
<!DOCTYPE html>
<html>
<head>
    <title>Reporte Semanal de Retardos</title>
</head>
<body>
    <h2>Reporte Semanal de Retardos</h2>

    <ul>
        @foreach ($retardos as $asistenciasEmpleado)
            <li>
                <strong>{{ $asistenciasEmpleado->first()->empleado->nombres }}</strong><br>
                Retardos en la semana: {{ $asistenciasEmpleado->count() }}
            </li>
        @endforeach
    </ul>
</body>
</html>
