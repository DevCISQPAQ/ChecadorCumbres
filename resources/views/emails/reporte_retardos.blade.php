<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Semanal de Retardos y Asistencias</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color: #2c3e50; color: #ffffff; padding: 20px; text-align: center; border-top-left-radius: 6px; border-top-right-radius: 6px;">
                <h2 style="margin: 0;">📋 Reporte Semanal de Retardos y Asistencias</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Hola,</p>
                <p>Este es el resumen semanal:</p>

                {{-- Tabla Retardos --}}
                <h3 style="color: #2c3e50;">Empleados con Retardos</h3>
                @if($retardos->isEmpty())
                    <p style="font-style: italic; color: #666;">No hay empleados con retardos esta semana.</p>
                @else
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 30px;">
                        <thead>
                            <tr>
                                <th align="left" style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">N. Empleado</th>
                                <th align="left" style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">Empleado</th>
                                <th align="center" style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">Retardos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($retardos as $asistenciasEmpleado)
                                <tr>
                                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                        {{ $asistenciasEmpleado->first()->empleado->id }}
                                    </td>
                                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                        {{ $asistenciasEmpleado->first()->empleado->nombres }}
                                    </td>
                                    <td align="center" style="padding: 8px; border-bottom: 1px solid #eee;">
                                        {{ $asistenciasEmpleado->count() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Tabla Empleados sin Asistencia --}}
                <h3 style="color: #2c3e50;">Empleados sin Registro de Asistencia</h3>
                @if($empleadosSinAsistencia->isEmpty())
                    <p style="font-style: italic; color: #666;">Todos los empleados registraron asistencia esta semana.</p>
                @else
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th align="left" style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">N. Empleado</th>
                                <th align="left" style="padding: 8px; border-bottom: 2px solid #ddd; background-color: #ecf0f1;">Empleado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleadosSinAsistencia as $empleado)
                                <tr>
                                     <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                        {{ $empleado->id }} 
                                    </td>
                                    <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                        {{ $empleado->nombres }} 
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <p style="margin-top: 20px;">Por favor toma las medidas correspondientes.</p>

                <p style="color: #999; font-size: 12px;">Este es un correo automático. No respondas a este mensaje.</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #ecf0f1; color: #7f8c8d; text-align: center; padding: 10px; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; font-size: 12px;">
                &copy; {{ date('Y') }} Cumbres Querétaro. Todos los derechos reservados.
            </td>
        </tr>
    </table>
</body>
</html>
