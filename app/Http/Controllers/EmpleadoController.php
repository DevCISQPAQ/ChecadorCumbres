<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    public function index(Request $request)
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

    private function obtenerPeriodoEscolar()
    {
        $hoy = Carbon::now();
        $anioInicio = $hoy->month >= 9 ? $hoy->year : $hoy->year - 1;

        return [
            'inicio' => Carbon::create($anioInicio, 10, 1)->startOfMonth(),
            'fin' => Carbon::create($anioInicio + 1, 9, 30)->endOfMonth(),
            // 'etiqueta' => 'Septiembre ' . $anioInicio . ' - Septiembre ' . ($anioInicio + 1),
            'etiqueta' => $anioInicio . '-' . ($anioInicio + 1),
        ];
    }

    private function obtenerEmpleados(Request $request)
    {
        $query = Empleado::query();  // <-- Aquí no usas all(), sino query()

        $periodo = $this->obtenerPeriodoEscolar();

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);

            // Cuando hay búsqueda, quitamos el filtro por periodo para buscar en toda la tabla
            $query->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"]);
            // } else {
            //     // Solo si NO hay búsqueda, filtramos por periodo
            //     $query->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']]);
            // }
        }

        // Ordenar los resultados
        $query->orderByDesc('created_at');

        // Finalmente paginar
        $empleados = $query->paginate(10)->withQueryString();

        return $empleados;
    }

    private function obtenerConteosPorDepartamento()
    {
        $periodo = $this->obtenerPeriodoEscolar();

        $preescolarCount = Empleado::where(function ($query) {
            $query->where('departamento', 'LIKE', '%preescolar%');
        })
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $primariaCount = Empleado::where('departamento', 'LIKE', '%primaria%')
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $secundariaCount = Empleado::where('departamento', 'LIKE', '%secundaria%')
            ->whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])
            ->count();

        $totales_empleados = Empleado::whereBetween('created_at', [$periodo['inicio'], $periodo['fin']])->count();

        return compact('preescolarCount', 'primariaCount', 'secundariaCount', 'totales_empleados');
    }

    public function crearEmpleado()
    {
        return view('admin.empleados.crear');
    }

    public function guardarEmpleado(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'departamento' => 'required',
            'puesto' => 'required',
            'email' => ['required', 'email', 'unique:empleados', function ($attribute, $value, $fail) {
                $domain = substr(strrchr($value, "@"), 1);  // Obtener el dominio del correo
                if (!checkdnsrr($domain, 'MX')) {  // Verificar registros MX para el dominio
                    $fail('El dominio del correo electrónico no es válido.');
                }
            }],

            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validar la imagen
        ]);

        try {

            $fotoNombre = null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                // $fotoNombre = Str::uuid() . '.' . $file->getClientOriginalExtension();
                // $file->storeAs('public/empleados', $fotoNombre);
                $fotoNombre = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('img/empleados'), $fotoNombre);
            }

            Empleado::create([
                'id' => $request->id,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'departamento' => $request->departamento,
                'puesto' => $request->puesto,
                'email' => $request->email,
                'foto' => $fotoNombre, // Guarda el nombre de la foto o null

            ]);

            return redirect()->route('admin.empleados.index')->with('success', 'Empleado creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar empleado ' . $e->getMessage());
        }
    }

    public function editarEmpleado($id)
    {
        try {

            $empleado = Empleado::findOrFail($id);
            return view('admin.empleados.editar', compact('empleado'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al editar empleado ' . $e->getMessage());
        }
    }

    public function actualizarEmpleado(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6', // 👉 ahora es opcional
            'level_user' => 'required|integer|in:0,1,2',
            'yes_notifications' => 'nullable|boolean', // 👉 validación
        ]);

        try {

            $empleado = Empleado::findOrFail($id);

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

            $empleado->update($data);

            return redirect()->route('admin.empleados')->with('success', 'Empleado actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar empleado ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {

            $deleted = Empleado::destroy($id);

            if (!$deleted) {
                return redirect()->back()->with('error', 'No se pudo eliminar el empleado. El registro no existe.');
            }

            return redirect()->back()->with('success', 'Empleado eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar al empleado: ' . $e->getMessage());
        }
    }

    // public function eliminarEmpleado($id)
    // {
    //     try {
    //         $empleado = Empleado::findOrFail($id);
    //         $empleado->delete();

    //         return redirect()->route('admin.empleados.index')->with('success', 'Empleado eliminado.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Error al eliminar empleado ' . $e->getMessage());
    //     }
    // }
}
