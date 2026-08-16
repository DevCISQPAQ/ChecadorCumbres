<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Hash;
use App\Models\Empleado;
use App\Models\Vacacion;
use App\Models\DiaFestivo;
use App\Models\Departamento;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class UsuarioController extends Controller
{

    public function listarUsuarios()
    {
        try {

            $usuarios = User::all();
            return view('admin.usuarios.index', compact('usuarios'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de Usuarios ' . $e->getMessage());
        }
    }

    public function crearUsuario()
    {
        return view('admin.usuarios.crear');
    }

    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', 'unique:users', function ($attribute, $value, $fail) {
                $domain = substr(strrchr($value, "@"), 1);  // Obtener el dominio del correo
                if (!checkdnsrr($domain, 'MX')) {  // Verificar registros MX para el dominio
                    $fail('El dominio del correo electrónico no es válido.');
                }
            }],

            'password' => 'required|min:6',
            'level_user' => 'required|integer|in:0,1,2',
            'yes_notifications' => 'nullable|boolean',
        ]);

        try {

            User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'level_user' => $request->level_user,
                'yes_notifications' => $request->yes_notifications ?? false,
            ]);

            return redirect()->route('admin.preferencias')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar usuario ' . $e->getMessage());
        }
    }

    public function editarUsuario($id)
    {
        try {

            $usuario = User::findOrFail($id);
            return view('admin.usuarios.editar', compact('usuario'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al editar usuario ' . $e->getMessage());
        }
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'level_user' => 'required|integer|in:0,1,2',
            'yes_notifications' => 'nullable|boolean',
        ]);

        try {

            $usuario = User::findOrFail($id);

            $data = [
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'level_user' => $request->level_user,
                'yes_notifications' => $request->yes_notifications ?? false,
            ];


            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $usuario->update($data);

            return redirect()->route('admin.preferencias')->with('success', 'Usuario actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar usuario ' . $e->getMessage());
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return redirect()->route('admin.preferencias')->with('success', 'Usuario eliminado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar usuario ' . $e->getMessage());
        }
    }

    public function configurarVacacionesFestivos()
    {

        $empleados = Empleado::orderBy('nombres')
            ->get();

        $vacaciones = Vacacion::with('empleado')
            ->latest()
            ->get();

        $diasFestivos = DiaFestivo::latest()->get();

        return view('admin.vacacionesfestivos.index', compact(
            'empleados',
            'vacaciones',
            'diasFestivos'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:1000',
        ]);

        // validar traslape de vacaciones
        $existe = Vacacion::where('empleado_id', $request->empleado_id)
            ->where(function ($query) use ($request) {

                $query->whereBetween('fecha_inicio', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ])
                    ->orWhereBetween('fecha_fin', [
                        $request->fecha_inicio,
                        $request->fecha_fin
                    ]);
            })
            ->exists();

        if ($existe) {
            return back()
                ->withErrors([
                    'fecha_inicio' => 'El empleado ya tiene vacaciones registradas en esas fechas.'
                ])
                ->withInput();
        }

        Vacacion::create([
            'empleado_id' => $request->empleado_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'motivo' => $request->motivo,
        ]);

        return redirect()
            ->route('admin.vacacionesfestivos.index')
            ->with('success', 'Vacaciones registradas correctamente.');
    }


    public function storeDiaFestivo(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'fecha' => 'required|date|unique:dias_festivos,fecha',
            ],
            [
                'fecha.unique' => 'Ya existe un día festivo registrado para esa fecha.',
                'fecha.required' => 'La fecha es obligatoria.',
                'fecha.date' => 'La fecha no es válida.',
                'nombre.required' => 'El nombre es obligatorio.',
            ]
        );

        DiaFestivo::create([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'oficial' => $request->boolean('oficial'),
        ]);

        return back()->with('success', 'Día festivo agregado correctamente.');
    }


    public function destroyVacaciones($id)
    {
        $vacacion = Vacacion::find($id);

        if (!$vacacion) {
            return redirect()
                ->back()
                ->with('error', 'El registro no existe');
        }

        $vacacion->delete();

        return redirect()
            ->back()
            ->with('success', 'Registro eliminado correctamente');
    }

    public function destroyFestivos($id)
    {
        $diasFestivos = DiaFestivo::find($id);

        if (!$diasFestivos) {
            return redirect()
                ->back()
                ->with('error', 'El registro no existe');
        }

        $diasFestivos->delete();

        return redirect()
            ->back()
            ->with('success', 'Registro eliminado correctamente');
    }


    public function storeDepartamento(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:departamentos,nombre'
        ]);

        // Verificar límite de departamentos
        if (Departamento::count() >= 10) {
            return redirect()
                ->back()
                ->with('error', 'No se pueden crear más de 10 departamentos.');
        }

        Departamento::create([
            'nombre' => $request->nombre
        ]);

        return redirect()
            ->back()
            ->with('success', 'Departamento creado correctamente');
    }

    public function listarConfiguracion()
    {
        try {

            // AJUSTE DE ENTRADA
            $ajusteActivo = Configuracion::where(
                'clave',
                'ajuste_horario_0730'
            )->value('valor');

            $horaAjustada = Configuracion::where(
                'clave',
                'hora_entrada_ajustada_0730'
            )->value('valor');


            // AJUSTE DE SALIDA
            $ajusteSalidaActivo = Configuracion::where(
                'clave',
                'ajuste_horario_salida_1500'
            )->value('valor');

            $horaSalidaAjustada = Configuracion::where(
                'clave',
                'hora_salida_ajustada_1500'
            )->value('valor');


            $departamentos = Departamento::all();

            return view('admin.usuarios.configuraciones', compact(
                'departamentos',
                'ajusteActivo',
                'horaAjustada',
                'ajusteSalidaActivo',
                'horaSalidaAjustada'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Error al cargar la página de Usuarios ' . $e->getMessage()
            );
        }
    }

    public function destroyDepartamento($id)
    {
        $departamento = Departamento::find($id);

        if (!$departamento) {
            return redirect()
                ->back()
                ->with('error', 'El registro no existe');
        }

        $departamento->delete();

        return redirect()
            ->back()
            ->with('success', 'Registro eliminado correctamente');
    }

    public function updateVacaciones(Request $request, Vacacion $vacacion)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:500',
        ]);

        $vacacion->update([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'motivo' => $request->motivo,
        ]);

        return back()->with(
            'success',
            'Vacaciones actualizadas correctamente.'
        );
    }


    public function updateFestivos(Request $request, DiaFestivo $diaFestivo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'oficial' => 'nullable|boolean',
        ]);

        $diaFestivo->update([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'oficial' => $request->boolean('oficial'),
        ]);

        return back()->with(
            'success',
            'Día festivo actualizado correctamente.'
        );
    }

    public function generarVacacionesExcel(Request $request)
    {
        try {

            $query = Vacacion::with('empleado');

            if ($request->filled('empleado_id')) {
                $query->where('empleado_id', $request->empleado_id);
            }

            $vacaciones = $query->orderBy('fecha_inicio')->get();

            return Excel::download(
                new class($vacaciones) implements FromArray, WithHeadings, WithStyles {

                    private $vacaciones;

                    public function __construct($vacaciones)
                    {
                        $this->vacaciones = $vacaciones;
                    }

                    public function array(): array
                    {
                        $data = [];

                        foreach ($this->vacaciones as $vacacion) {

                            $inicio = \Carbon\Carbon::parse($vacacion->fecha_inicio);
                            $fin = \Carbon\Carbon::parse($vacacion->fecha_fin);

                            $dias = $inicio->diffInDays($fin) + 1;

                            if ($fin->lt(now())) {
                                $estado = 'Finalizadas';
                            } elseif ($inicio->lte(now()) && $fin->gte(now())) {
                                $estado = 'En curso';
                            } else {
                                $estado = 'Próximas';
                            }

                            $data[] = [
                                $vacacion->empleado->n_empleado,
                                $vacacion->empleado->nombres . ' ' .
                                    $vacacion->empleado->apellido_paterno . ' ' .
                                    $vacacion->empleado->apellido_materno,

                                $inicio->format('d/m/Y'),
                                $fin->format('d/m/Y'),
                                $dias,
                                $estado,
                                $vacacion->motivo ?: '-'
                            ];
                        }

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            'No. Empleado',
                            'Empleado',
                            'Fecha inicio',
                            'Fecha fin',
                            'Días',
                            'Estado',
                            'Motivo'
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $highestRow = $sheet->getHighestRow();

                        // Encabezado
                        $sheet->getStyle("A1:G1")->getFont()->setBold(true);

                        $sheet->getStyle("A1:G1")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('D9EAD3');

                        // Centrar contenido
                        $sheet->getStyle("A1:G{$highestRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Bordes
                        $sheet->getStyle("A1:G{$highestRow}")
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN);

                        // Filas alternadas
                        for ($row = 2; $row <= $highestRow; $row++) {

                            if ($row % 2 == 0) {

                                $sheet->getStyle("A{$row}:G{$row}")
                                    ->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB('F8F9FA');
                            }

                            // Colorear estado
                            $estado = $sheet->getCell("F{$row}")->getValue();

                            if ($estado == 'Próximas') {

                                $sheet->getStyle("F{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('008000');
                            } elseif ($estado == 'En curso') {

                                $sheet->getStyle("F{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('E69138');
                            } elseif ($estado == 'Finalizadas') {

                                $sheet->getStyle("F{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('CC0000');
                            }
                        }

                        // Ajustar ancho automáticamente
                        foreach (range('A', 'G') as $column) {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                        }
                    }
                },
                'vacaciones_' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Error al generar el Excel: ' . $e->getMessage()
            );
        }
    }

    public function actualizarHorario0730(Request $request)
    {
        $request->validate([
            'hora_entrada_ajustada_0730' => [
                'required',
                'date_format:H:i'
            ],
        ]);

        Configuracion::updateOrCreate(
            [
                'clave' => 'ajuste_horario_0730'
            ],
            [
                'valor' => $request->has('ajuste_horario_0730')
                    ? '1'
                    : '0'
            ]
        );

        Configuracion::updateOrCreate(
            [
                'clave' => 'hora_entrada_ajustada_0730'
            ],
            [
                'valor' => $request->hora_entrada_ajustada_0730
            ]
        );

        return back()->with(
            'success',
            'Configuración de horario actualizada correctamente.'
        );
    }

    public function actualizarHorarioSalida1500(Request $request)
    {
        $request->validate([
            'hora_salida_ajustada_1500' => [
                'required',
                'date_format:H:i'
            ],
        ]);

        Configuracion::updateOrCreate(
            [
                'clave' => 'ajuste_horario_salida_1500'
            ],
            [
                'valor' => $request->has('ajuste_horario_salida_1500')
                    ? '1'
                    : '0'
            ]
        );

        Configuracion::updateOrCreate(
            [
                'clave' => 'hora_salida_ajustada_1500'
            ],
            [
                'valor' => $request->hora_salida_ajustada_1500
            ]
        );

        return back()->with(
            'success',
            'Configuración de horario de salida actualizada correctamente.'
        );
    }

    public function updateDepartamento(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $departamento = Departamento::findOrFail($id);

        $departamento->nombre = $request->nombre;
        $departamento->save();

        return redirect()
            ->route('admin.configuraciones')
            ->with('success', 'Departamento actualizado correctamente.');
    }
}
