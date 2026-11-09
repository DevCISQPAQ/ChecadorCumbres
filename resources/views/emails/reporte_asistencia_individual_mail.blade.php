
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>

    <h2>Hola, {{ $empleado->nombre }}</h2>

    <p>
        Te compartimos tu reporte de asistencia correspondiente
        a los últimos 15 días.
    </p>

    <p>
        En el archivo PDF adjunto encontrarás el detalle de tus
        registros de asistencia, incluyendo retardos, faltas,
        permisos, incapacidades y demás estados registrados.
    </p>

    <p>
        <strong>Periodo:</strong>
        {{ $fechaInicio->format('d/m/Y') }}
        al
        {{ $fechaFin->format('d/m/Y') }}
    </p>

    <p>
        Por favor, revisa tu reporte y comunícate con Recursos Humanos
        si encuentras alguna inconsistencia.
    </p>

    <p>
        Saludos.<br>
        Departamento de Recursos Humanos.
    </p>

</body>
</html>
