@php
$empleadoUnico = null;
$todosMismoEmpleado = true;

if(isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Empleado) {
    // Si es colección de empleados
    $primerEmpleado = $asistencias[0] ?? null;
    foreach ($asistencias as $empleado) {
        if (is_null($empleadoUnico)) {
            $empleadoUnico = $empleado;
        } elseif ($empleadoUnico->id !== $empleado->id) {
            $todosMismoEmpleado = false;
            break;
        }
    }
} elseif(isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Asistencia) {
    // Si es colección de asistencias
    $primerEmpleado = $asistencias[0]->empleado ?? null;
    foreach ($asistencias as $asistencia) {
        if (is_null($empleadoUnico)) {
            $empleadoUnico = $asistencia->empleado;
        } elseif ($empleadoUnico->id !== $asistencia->empleado->id) {
            $todosMismoEmpleado = false;
            break;
        }
    }
} else {
    $primerEmpleado = null;
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
                <img src="{{ public_path('img/escudo-gris.svg') }}" alt="Logo" style="width: .5rem;">
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

                @if(isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Empleado)
                    @forelse($asistencias as $empleado)
                        <tr class="items">
                            <td>{{ $empleado->id }}</td>
                            <td>{{ $empleado->nombres . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno }}</td>
                            <td>{{ $empleado->departamento }}</td>
                            <td style="color: red;">Sin registro</td>
                            <td style="color: red;">Sin registro</td>
                            <td style="color: red;">Sin registro</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No se encontraron registros.</td></tr>
                    @endforelse
                @elseif(isset($asistencias[0]) && $asistencias[0] instanceof \App\Models\Asistencia)
                    @forelse($asistencias as $asistencia)
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
                            <td @if(!$asistencia->hora_entrada) style="color: red;" @endif>
                                {{ $asistencia->hora_entrada ? $asistencia->hora_entrada->format('Y/m/d H:i') : 'N/A' }}
                            </td>
                            <td @if(!$asistencia->hora_salida) style="color: red;" @endif>
                                {{ $asistencia->hora_salida ? $asistencia->hora_salida->format('Y/m/d H:i') : 'N/A' }}
                            </td>
                            <td @if($asistencia->retardo) style="color: red;" @else style="color: green;" @endif>
                                {{ $asistencia->retardo ? 'Sí' : 'No' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No se encontraron registros.</td></tr>
                    @endforelse
                @else
                    <tr><td colspan="6">No se encontraron registros.</td></tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold; padding-right: 1rem;">Total de retardos: {{ $totalRetardos }}</td>
                </tr>
                @if(isset($horasFormateadas))
                    <tr>
                        <td colspan="6" style="text-align: right; font-weight: bold; padding-right: 1rem;">
                            <strong> Total de horas trabajadas:</strong> {{ $horasFormateadas }} horas
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>

    <footer class="footer">
        <div>&copy; Cumbres International School</div>
        <div>Documento generado automáticamente por el sistema.</div>
    </footer>
</body>

</html>
