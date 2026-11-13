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

    <hr>

    <h3>Importante: Firma y envío del reporte</h3>

    <p>
        Una vez que hayas revisado tu reporte de asistencia,
        deberás realizar lo siguiente:
    </p>

    <ol>
        <li>Descargar el archivo PDF adjunto.</li>
        <li>Revisar que la información registrada sea correcta.</li>
        <li>Firmar el documento como evidencia de que has revisado
            tu reporte.</li>
        <li>Enviar el documento firmado al siguiente correo electrónico:
            <strong>capital.humano@cumbresqueretaro.com</strong>.
        </li>
    </ol>

    <p>
        <strong>Nota importante:</strong>
        Por favor, no respondas a este correo electrónico, ya que
        es un mensaje enviado automáticamente. Cualquier documento
        firmado o aclaración deberá enviarse directamente al correo
        anteriormente indicado.
    </p>

    <hr>

    <p>
        Si encuentras alguna inconsistencia en tu reporte, favor de
        comunicarte con el Departamento de Recursos Humanos.
    </p>

    <p>
        Agradecemos tu atención y colaboración.
    </p>

    <p>
        Saludos.<br>
        <strong>Departamento de Recursos Humanos.</strong>
    </p>

</body>

</html>