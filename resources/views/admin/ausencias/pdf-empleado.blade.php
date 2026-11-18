
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <title>Reporte de Ausencias</title>

    <style>
        body {
            color: #374151;
            font-family: sans-serif;
            font-size: 11px;
        }

        .products {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .products thead {
            background-color: #f3f4f6;
        }

        .products th {
            color: #374151;
            font-weight: bold;
            padding: 10px 6px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .products td {
            padding: 9px 6px;
            border: 1px solid #e5e7eb;
            text-align: center;
            color: #4b5563;
        }

        .products tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .products tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .products tfoot td {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }

        .header-info {
            margin-top: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img
                    src="{{ public_path('img/escudo-gris.svg') }}"
                    style="width: .5rem;">
            </td>

            <td class="w-half" style="text-align: right;">
                <h2 style="color: #374151; margin: 0;">
                    Cumbres International School
                </h2>
            </td>
        </tr>
    </table>

    {{-- CÁLCULO DE DÍAS --}}
    @php
        $totalDias = 0;

        foreach ($ausencias as $ausencia) {
            $inicio = \Carbon\Carbon::parse($ausencia->fecha_inicio);
            $fin = \Carbon\Carbon::parse($ausencia->fecha_fin);

            $totalDias += $inicio->diffInDays($fin) + 1;
        }
    @endphp

    {{-- INFORMACIÓN DEL REPORTE --}}
    <div class="header-info">

        <table class="w-full">
            <tr>
                <td>
                    <h3 style="font-size: 16px; color: #1f2937; margin-bottom: 5px;">
                        Reporte de Ausencias
                    </h3>

                    <p>
                        <strong>Empleado:</strong>
                        {{ $empleado->nombres }}
                        {{ $empleado->apellido_paterno }}
                        {{ $empleado->apellido_materno }}
                    </p>

                    <p>
                        <strong>No. empleado:</strong>
                        {{ $empleado->n_empleado }}
                    </p>
                </td>

                <td style="text-align: right;">
                    <strong>Fecha de generación:</strong>
                    {{ now()->format('d/m/Y') }}
                </td>
            </tr>
        </table>

    </div>

    {{-- TABLA DE AUSENCIAS --}}
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
                        $colorEstado = '#6b7280';
                    } elseif (
                        $inicio->lte(now()->startOfDay()) &&
                        $fin->gte(now()->startOfDay())
                    ) {
                        $estado = 'En curso';
                        $colorEstado = '#15803d';
                    } else {
                        $estado = 'Próxima';
                        $colorEstado = '#2563eb';
                    }

                    $tipos = [
                        'vacaciones' => 'Vacaciones',
                        'permiso' => 'Permiso',
                        'incapacidad' => 'Incapacidad',
                    ];

                    $tipo = $tipos[$ausencia->tipo] ?? ucfirst($ausencia->tipo);
                @endphp

                <tr>

                    <td>{{ $tipo }}</td>

                    <td>
                        {{ $inicio->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $fin->format('d/m/Y') }}
                    </td>

                    <td>{{ $dias }}</td>

                    <td style="color: {{ $colorEstado }}; font-weight: bold;">
                        {{ $estado }}
                    </td>

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
                <td colspan="6" style="text-align: right; padding-right: 12px;">
                    Total días de ausencia: {{ $totalDias }} días
                </td>
            </tr>
        </tfoot>

    </table>

    {{-- PIE DE PÁGINA --}}
    <footer class="footer">
        <div>
            &copy; Cumbres International School
        </div>

        <div>
            Documento generado automáticamente por el sistema.
        </div>
    </footer>

</body>

</html>
