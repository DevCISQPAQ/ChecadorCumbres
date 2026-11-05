<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Hash;
use App\Models\Empleado;
use App\Models\Ausencias;
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


    public function configurarAusencias()
    {
        $empleados = Empleado::orderBy('nombres')->get();

        $ausencias = Ausencias::with('empleado')
            ->latest()
            ->get();

        return view('admin.ausencias.index', compact(
            'empleados',
            'ausencias'
        ));
    }


    public function configurarFestivos()
    {
        //$empleados = Empleado::orderBy('nombres')->get();

        // $ausencias = Ausencias::with('empleado')
        //     ->latest()
        //     ->get();

        $diasFestivos = DiaFestivo::latest()->get();

        return view('admin.festivos.index', compact(
            'diasFestivos'
        ));
    }


    public function storeAusencia(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|in:vacaciones,permiso,incapacidad',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:1000',
        ]);

        // Validar traslape de ausencias
        $existe = Ausencias::where('empleado_id', $request->empleado_id)
            ->where(function ($query) use ($request) {
                $query->where('fecha_inicio', '<=', $request->fecha_fin)
                    ->where('fecha_fin', '>=', $request->fecha_inicio);
            })
            ->exists();

        if ($existe) {
            return back()
                ->withErrors([
                    'fecha_inicio' => 'El empleado ya tiene una ausencia registrada en esas fechas.'
                ])
                ->withInput();
        }

        Ausencias::create([
            'empleado_id' => $request->empleado_id,
            'tipo' => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'motivo' => $request->motivo,
        ]);

        return redirect()
            ->route('admin.ausencias.index')
            ->with('success', 'Ausencia registrada correctamente.');
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



    public function destroyAusencia($id)
    {
        $ausencia = Ausencias::find($id);

        if (!$ausencia) {
            return redirect()
                ->back()
                ->with('error', 'El registro no existe.');
        }

        $ausencia->delete();

        return redirect()
            ->back()
            ->with('success', 'Ausencia eliminada correctamente.');
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


    public function updateAusencia(Request $request, Ausencias $ausencia)
    {
        $request->validate([
            'tipo' => 'required|in:vacaciones,permiso,incapacidad',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'nullable|string|max:1000',
        ]);

        $existe = Ausencias::where('empleado_id', $ausencia->empleado_id)
            ->where('id', '!=', $ausencia->id)
            ->where('fecha_inicio', '<=', $request->fecha_fin)
            ->where('fecha_fin', '>=', $request->fecha_inicio)
            ->exists();

        if ($existe) {
            return back()
                ->withErrors([
                    'fecha_inicio' => 'El empleado ya tiene otra ausencia registrada en esas fechas.'
                ])
                ->withInput();
        }

        $ausencia->update([
            'tipo' => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'motivo' => $request->motivo,
        ]);

        return back()->with(
            'success',
            'Ausencia actualizada correctamente.'
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


    public function generarAusenciasExcel(Request $request)
    {
        try {

            $query = Ausencias::with('empleado');

            if ($request->filled('empleado_id')) {
                $query->where('empleado_id', $request->empleado_id);
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            $ausencias = $query->orderBy('fecha_inicio')->get();

            return Excel::download(

                new class($ausencias) implements FromArray, WithHeadings, WithStyles {

                    private $ausencias;

                    public function __construct($ausencias)
                    {
                        $this->ausencias = $ausencias;
                    }

                    public function array(): array
                    {
                        $data = [];

                        foreach ($this->ausencias as $ausencia) {

                            $inicio = Carbon::parse($ausencia->fecha_inicio);
                            $fin = Carbon::parse($ausencia->fecha_fin);
                            $hoy = now()->startOfDay();

                            $dias = $inicio->diffInDays($fin) + 1;

                            if ($fin->lt($hoy)) {
                                $estado = 'Finalizadas';
                            } elseif ($inicio->lte($hoy) && $fin->gte($hoy)) {
                                $estado = 'En curso';
                            } else {
                                $estado = 'Próximas';
                            }

                            $tipos = [
                                'vacaciones' => 'Vacaciones',
                                'permiso' => 'Permiso',
                                'incapacidad' => 'Incapacidad',
                            ];

                            $data[] = [
                                $ausencia->empleado->n_empleado,
                                $ausencia->empleado->nombres . ' ' .
                                    $ausencia->empleado->apellido_paterno . ' ' .
                                    $ausencia->empleado->apellido_materno,

                                $tipos[$ausencia->tipo] ?? $ausencia->tipo,
                                $inicio->format('d/m/Y'),
                                $fin->format('d/m/Y'),
                                $dias,
                                $estado,
                                $ausencia->motivo ?: '-'
                            ];
                        }

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            'No. Empleado',
                            'Empleado',
                            'Tipo de ausencia',
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

                        $sheet->getStyle("A1:H1")->getFont()->setBold(true);

                        $sheet->getStyle("A1:H1")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('D9EAD3');

                        $sheet->getStyle("A1:H{$highestRow}")
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        $sheet->getStyle("A1:H{$highestRow}")
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN);

                        for ($row = 2; $row <= $highestRow; $row++) {

                            if ($row % 2 == 0) {
                                $sheet->getStyle("A{$row}:H{$row}")
                                    ->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB('F8F9FA');
                            }

                            $estado = $sheet->getCell("G{$row}")->getValue();

                            if ($estado == 'Próximas') {
                                $sheet->getStyle("G{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('008000');
                            } elseif ($estado == 'En curso') {
                                $sheet->getStyle("G{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('E69138');
                            } elseif ($estado == 'Finalizadas') {
                                $sheet->getStyle("G{$row}")
                                    ->getFont()
                                    ->getColor()
                                    ->setRGB('CC0000');
                            }
                        }

                        foreach (range('A', 'H') as $column) {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                        }
                    }
                },

                'ausencias_' . now()->format('Y-m-d') . '.xlsx'
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
