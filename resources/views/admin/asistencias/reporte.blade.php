@php
$empleadoUnico = null;
$todosMismoEmpleado = true;

foreach ($asistencias as $asistencia) {
if (is_null($empleadoUnico)) {
$empleadoUnico = $asistencia->empleado;
} elseif ($empleadoUnico->id !== $asistencia->empleado->id) {
$todosMismoEmpleado = false;
break;
}
}
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}" type="text/css">
    <title>Reporte de Asistencias</title>
</head>

<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ public_path('img/escudo-gris.png') }}" alt="Logo" width="100" />
            </td>
            <td class="w-half">
                <h2>Cumbres International School</h2>
            </td>
        </tr>
    </table>


    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>
                        <h4>Reporte de Asistencias</h4>
                    </div>
                    @if($todosMismoEmpleado && $empleadoUnico)
                    <p>De: {{ $empleadoUnico->nombres }} {{ $empleadoUnico->apellido_paterno }} {{ $empleadoUnico->apellido_materno }}</p>
                    @endif
                </td>
                <td class="w-half" style="text-align: right;">
                   Fecha: {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>


    <div class="margin-top">
        <table class="products">
            <thead>
                <tr>
                    <th>N. Empleado</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Hora de entrada</th>
                    <th>Hora de salida</th>
                    <th>Retardo</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalRetardos = 0;
                @endphp
                @foreach($asistencias as $asistencia)
                @php
                $empleado = $asistencia->empleado;
                if ($asistencia->retardo) {
                $totalRetardos++;
                }
                @endphp
                <tr class="items">
                    <td>{{ $asistencia->empleado_id ?? 0 }}</td>
                    <td>{{ $empleado ? $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno : 'N/A' }}</td>
                    <td>{{ $empleado->departamento ?? 'N/A' }}</td>
                    <td>{{ $asistencia->hora_entrada ? $asistencia->hora_entrada->format('H:i') : 'N/A' }}</td>
                    <td>{{ $asistencia->hora_salida ? $asistencia->hora_salida->format('H:i') : 'N/A' }}</td>
                    <td>{{ $asistencia->retardo ? 'Sí' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold; padding-right: 1rem;">Total de retardos: {{ $totalRetardos }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <footer class="footer margin-top">
        <div>&copy; Cumbres International School</div>
        <div>Documento generado automáticamente por el sistema.</div>
    </footer>
</body>

</html>