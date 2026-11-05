<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <title>Reporte de ausencias</title>
</head>

<body>

    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="{{ public_path('img/escudo-gris.svg') }}"
                    style="width: .5rem;">
            </td>
            <td class="w-half">
                <h2>Cumbres International School</h2>
            </td>
        </tr>
    </table>

    @php
    $totalDias = 0;

    foreach ($ausencias as $ausencia) {
    $inicio = \Carbon\Carbon::parse($ausencia->fecha_inicio);
    $fin = \Carbon\Carbon::parse($ausencia->fecha_fin);

    $totalDias += $inicio->diffInDays($fin) + 1;
    }
    @endphp

    <div class="margin-top">

        <table class="w-full">
            <tr>
                <td>
                    <h4>Reporte de Ausencias</h4>

                    <p>
                        Empleado:
                        <strong>
                            {{ $empleado->nombres }}
                            {{ $empleado->apellido_paterno }}
                            {{ $empleado->apellido_materno }}
                        </strong>
                    </p>

                    <p>
                        No. empleado:
                        {{ $empleado->n_empleado }}
                    </p>
                </td>

                <td style="text-align:right">
                    Fecha:
                    {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>

    </div>

    <table class="products">

        <thead>
            <tr>
                <th>Tipo</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Días</th>
                <th>Estado</th>
                <th>Motivo</th>
            </tr>
        </thead>

        <tbody>

            @forelse($ausencias as $ausencia)

            @php
            $inicio = \Carbon\Carbon::parse($ausencia->fecha_inicio);
            $fin = \Carbon\Carbon::parse($ausencia->fecha_fin);

            $dias = $inicio->diffInDays($fin) + 1;

            if ($fin->lt(now()->startOfDay())) {
            $estado = 'Finalizada';
            } elseif (
            $inicio->lte(now()->startOfDay()) &&
            $fin->gte(now()->startOfDay())
            ) {
            $estado = 'En curso';
            } else {
            $estado = 'Próxima';
            }

            $tipos = [
            'vacaciones' => 'Vacaciones',
            'permiso' => 'Permiso',
            'incapacidad' => 'Incapacidad',
            ];

            $tipo = $tipos[$ausencia->tipo] ?? ucfirst($ausencia->tipo);
            @endphp

            <tr class="items">

                <td>{{ $tipo }}</td>

                <td>
                    {{ $inicio->format('d/m/Y') }}
                </td>

                <td>
                    {{ $fin->format('d/m/Y') }}
                </td>

                <td>{{ $dias }}</td>

                <td>{{ $estado }}</td>

                <td>
                    {{ $ausencia->motivo ?: '-' }}
                </td>

            </tr>

            @empty

            <tr>
                <td colspan="6">
                    No tiene ausencias registradas.
                </td>
            </tr>

            @endforelse

        </tbody>

        <tfoot>
            <tr>
                <td colspan="6"
                    style="text-align:right;font-weight:bold;padding-right:1rem;">
                    Total días de ausencia: {{ $totalDias }} días
                </td>
            </tr>
        </tfoot>

    </table>

    <footer class="footer">
        <div>
            © Cumbres International School
        </div>

        <div>
            Documento generado automáticamente por el sistema.
        </div>
    </footer>

</body>

</html>