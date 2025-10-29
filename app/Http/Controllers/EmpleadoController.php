<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Str;

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
        $query = Empleado::query();  

        if ($request->filled('buscar')) {
            $buscar = strtolower($request->buscar);
            $query->whereRaw('LOWER(nombres) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(apellido_materno) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(departamento) LIKE ?', ["%{$buscar}%"])
                ->orWhereRaw('LOWER(id) LIKE ?', ["%{$buscar}%"]);
        }

        // Ordenar los resultados
        $query->orderByDesc('created_at');

        // paginar
        $empleados = $query->paginate(10)->withQueryString();

        return $empleados;
    }

    private function obtenerConteosPorDepartamento()
    {
        $preescolarCount = Empleado::where(function ($query) {
            $query->where('departamento', 'LIKE', '%preescolar%');
        })->count();

        $primariaCount = Empleado::where('departamento', 'LIKE', '%primaria%')
            ->count();

        $secundariaCount = Empleado::where('departamento', 'LIKE', '%secundaria%')
            ->count();

        $administrativosCount = Empleado::where('departamento', 'LIKE', '%administracion%')
            ->count();

        $academiasCount = Empleado::where('departamento', 'LIKE', '%academia%')
            ->count();

        $promocionCount = Empleado::where('departamento', 'LIKE', '%promocion%')
            ->count();

        $mantenimientoCount = Empleado::where('departamento', 'LIKE', '%mantenimiento%')
            ->count();

        $direccionCount = Empleado::where('departamento', 'LIKE', '%direccion%')
            ->count();

        $totales_empleados = Empleado::query()->count();

        return compact('preescolarCount', 'primariaCount', 'secundariaCount', 'administrativosCount', 'academiasCount','promocionCount', 'mantenimientoCount','direccionCount', 'totales_empleados');
    }

    public function crearEmpleado()
    {
        return view('admin.empleados.crear');
    }

    public function guardarEmpleado(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:empleados,id',
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'departamento' => 'required',
            'puesto' => 'required',
            'tipo_horario' => 'required',
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
                'tipo_horario' => $request->tipo_horario,
                'foto' => $fotoNombre, // Guarda el nombre de la foto o null

            ]);

            return redirect()->route('admin.empleados')->with('success', 'Empleado creado correctamente.');
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
            'id' => 'required',
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'departamento' => 'required',
            'puesto' => 'required',
            'email' => 'required|email|unique:empleados,email,' . $id,
            'tipo_horario' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validar la imagen

        ]);

        try {

            $empleado = Empleado::findOrFail($id);

            $fotoNombre = $empleado->foto;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                if ($empleado->foto && File::exists(public_path('img/empleados/' . $empleado->foto))) {
                    File::delete(public_path('img/empleados/' . $empleado->foto));
                }

                // Guardar la nueva foto
                $file = $request->file('foto');
                $fotoNombre = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('img/empleados'), $fotoNombre);
            }

            $data = [
                'id' => $request->id,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'departamento' => $request->departamento,
                'puesto' => $request->puesto,
                'email' => $request->email,
                'tipo_horario' =>$request->tipo_horario,
                'foto' => $fotoNombre, // Guarda el nombre de la foto o null
            ];

            $empleado->update($data);

            return redirect()->route('admin.empleados')->with('success', 'Empleado actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar empleado ' . $e->getMessage());
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
