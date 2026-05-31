<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Departamento;
use App\Models\HorarioEmpleado;

class EmpleadoController extends Controller
{
    public function listarEmpleados(Request $request)
    {
        try {

            //throw new \PDOException('Simulando desconexión de base de datos');

            $conteos = $this->obtenerConteosPorDepartamento();
            $empleados = $this->obtenerEmpleados($request);

            return view('admin.empleados.index', array_merge($conteos, compact('empleados')));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la página de empleados: ' . $e->getMessage());
        }
    }

    private function obtenerEmpleados(Request $request)
    {
        $query = Empleado::with('departamento');

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(id) LIKE ?', ["%{$buscar}%"])
                    ->orWhereHas('departamento', function ($d) use ($buscar) {
                        $d->whereRaw('LOWER(nombre) LIKE ?', ["%{$buscar}%"]);
                    });
            });
        }
        // Ordenar los resultados
        $query->orderByDesc('created_at');

        // paginar
        $empleados = $query->paginate(10)->withQueryString();

        return $empleados;
    }

    private function obtenerConteosPorDepartamento()
    {
        $departamentos = Departamento::withCount('empleados')
            ->orderBy('nombre')
            ->get();

        $totales_empleados = Empleado::count();

        return compact(
            'departamentos',
            'totales_empleados'
        );
    }

    public function crearEmpleado()
    {

        $departamentos = Departamento::orderBy('nombre')->get();

        return view('admin.empleados.crear', compact('departamentos'));

        //return view('admin.empleados.crear');
    }

    public function guardarEmpleado(Request $request)
    {
        $request->validate([

            'n_empleado' => 'required|unique:empleados,n_empleado',

            'nombres' => 'required',

            'apellido_paterno' => 'required',

            'apellido_materno' => 'required',

            'departamento_id' => 'required|exists:departamentos,id',

            'puesto' => 'required',

            'email' => [
                'required',
                'email',
                'unique:empleados,email',

                function ($attribute, $value, $fail) {

                    $domain = substr(strrchr($value, "@"), 1);

                    if (!checkdnsrr($domain, 'MX')) {
                        $fail('El dominio del correo electrónico no es válido.');
                    }
                }
            ],

            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // VALIDACIÓN HORARIOS
            'horarios' => 'nullable|array',

            'horarios.*.dia_semana' => 'required|integer|min:1|max:7',

            'horarios.*.hora_entrada' => 'nullable|date_format:H:i',

            'horarios.*.hora_salida' => 'nullable|date_format:H:i',

            'horarios.*.hora_salida_comida' => 'nullable|date_format:H:i',

            'horarios.*.hora_regreso_comida' => 'nullable|date_format:H:i',

            'horarios.*.tolerancia_minutos' => 'nullable|integer|min:0|max:120',
        ]);

        DB::beginTransaction();

        try {

            $fotoNombre = null;

            // =========================================
            // SUBIR FOTO
            // =========================================
            if ($request->hasFile('foto')) {

                $file = $request->file('foto');

                $fotoNombre = Str::uuid() . '.' . $file->getClientOriginalExtension();

                $file->move(
                    public_path('img/empleados'),
                    $fotoNombre
                );
            }

            // =========================================
            // CREAR EMPLEADO
            // =========================================
            $empleado = Empleado::create([

                'n_empleado' => $request->n_empleado,

                'nombres' => $request->nombres,

                'apellido_paterno' => $request->apellido_paterno,

                'apellido_materno' => $request->apellido_materno,

                'departamento_id' => $request->departamento_id,

                'puesto' => $request->puesto,

                'email' => $request->email,

                'foto' => $fotoNombre,
            ]);

            // =========================================
            // GUARDAR HORARIOS
            // =========================================
            if ($request->has('horarios')) {

                foreach ($request->horarios as $horario) {

                    // si no tiene entrada/salida no guardar
                    if (
                        empty($horario['hora_entrada']) ||
                        empty($horario['hora_salida'])
                    ) {
                        continue;
                    }

                    HorarioEmpleado::create([

                        'empleado_id' => $empleado->id,

                        'dia_semana' => $horario['dia_semana'],

                        'hora_entrada' => $horario['hora_entrada'],

                        'hora_salida' => $horario['hora_salida'],

                        'hora_salida_comida' =>
                        $horario['hora_salida_comida'] ?? null,

                        'hora_regreso_comida' =>
                        $horario['hora_regreso_comida'] ?? null,

                        'tolerancia_minutos' =>
                        $horario['tolerancia_minutos'] ?? 10,

                        'activo' => isset($horario['activo']),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.empleados')
                ->with(
                    'success',
                    'Empleado creado correctamente.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Error al guardar empleado: ' . $e->getMessage()
                );
        }
    }

    public function editarEmpleado($id)
    {
        try {

            $empleado = Empleado::with('horarios')
                ->findOrFail($id);

            $departamentos = Departamento::all();

            return view(
                'admin.empleados.editar',
                compact(
                    'empleado',
                    'departamentos'
                )
            );
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Error al editar empleado: ' . $e->getMessage()
                );
        }
    }

    public function actualizarEmpleado(Request $request, $id)
    {
        $request->validate([

            'n_empleado' =>
            'required|unique:empleados,n_empleado,' . $id,

            'nombres' => 'required',

            'apellido_paterno' => 'required',

            'apellido_materno' => 'required',

            'departamento_id' =>
            'required|exists:departamentos,id',

            'puesto' => 'required',

            'email' => [
                'required',
                'email',
                'unique:empleados,email,' . $id,
            ],

            'foto' =>
            'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // horarios
            'horarios' => 'nullable|array',

            'horarios.*.dia_semana' =>
            'required|integer|min:1|max:7',

            'horarios.*.hora_entrada' =>
            'nullable|date_format:H:i',

            'horarios.*.hora_salida' =>
            'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();

        try {

            $empleado = Empleado::findOrFail($id);

            // =====================================
            // FOTO
            // =====================================
            $fotoNombre = $empleado->foto;

            if ($request->hasFile('foto')) {

                // borrar foto anterior
                if (
                    $empleado->foto &&
                    File::exists(
                        public_path(
                            'img/empleados/' . $empleado->foto
                        )
                    )
                ) {

                    File::delete(
                        public_path(
                            'img/empleados/' . $empleado->foto
                        )
                    );
                }

                // guardar nueva
                $file = $request->file('foto');

                $fotoNombre =
                    Str::uuid() .
                    '.' .
                    $file->getClientOriginalExtension();

                $file->move(
                    public_path('img/empleados'),
                    $fotoNombre
                );
            }

            // =====================================
            // ACTUALIZAR EMPLEADO
            // =====================================
            $empleado->update([

                'n_empleado' => $request->n_empleado,

                'nombres' => $request->nombres,

                'apellido_paterno' =>
                $request->apellido_paterno,

                'apellido_materno' =>
                $request->apellido_materno,

                'departamento_id' =>
                $request->departamento_id,

                'puesto' => $request->puesto,

                'email' => $request->email,

                'foto' => $fotoNombre,
            ]);

            // =====================================
            // ACTUALIZAR HORARIOS
            // =====================================

            // borrar horarios anteriores
            $empleado->horarios()->delete();

            // insertar nuevos
            if ($request->has('horarios')) {

                foreach ($request->horarios as $horario) {

                    // evitar días vacíos
                    if (
                        empty($horario['hora_entrada']) ||
                        empty($horario['hora_salida'])
                    ) {
                        continue;
                    }

                    $empleado->horarios()->create([

                        'dia_semana' =>
                        $horario['dia_semana'],

                        'hora_entrada' =>
                        $horario['hora_entrada'],

                        'hora_salida' =>
                        $horario['hora_salida'],

                        'hora_salida_comida' =>
                        $horario['hora_salida_comida']
                            ?? null,

                        'hora_regreso_comida' =>
                        $horario['hora_regreso_comida']
                            ?? null,

                        'tolerancia_minutos' =>
                        $horario['tolerancia_minutos']
                            ?? 10,

                        'activo' =>
                        isset($horario['activo']),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.empleados')
                ->with(
                    'success',
                    'Empleado actualizado correctamente.'
                );
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Error al actualizar empleado: '
                        . $e->getMessage()
                );
        }
    }


    public function destroy($id)
    {
        try {

            // Buscar al empleado primero
            $empleado = Empleado::findOrFail($id);

            // Eliminar la foto si existe
            if ($empleado->foto) {
                $fotoPath = public_path('img/empleados/' . $empleado->foto);

                if (File::exists($fotoPath)) {
                    File::delete($fotoPath);
                }
            }

            // Eliminar el registro del empleado
            $empleado->delete();

            return redirect()->back()->with('success', 'Empleado eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar al empleado: ' . $e->getMessage());
        }
    }
}
