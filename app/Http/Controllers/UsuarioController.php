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

    public function configurarData()
    {

        $empleados = Empleado::orderBy('nombres')
            ->get();

        $vacaciones = Vacacion::with('empleado')
            ->latest()
            ->get();

        $diasFestivos = DiaFestivo::latest()->get();

        return view('admin.usuarios.configurar', compact(
            'empleados',
            'vacaciones',
            'diasFestivos'
        ));
    }

    public function actualizarData(Request $request)
    {
        $request->validate([
            'hora_limite_entrada' => 'required|date_format:H:i',
            // 'hora_limite_salida' => 'required|date_format:H:i',
        ]);

        try {
            Configuracion::updateOrCreate(
                ['clave' => 'hora_limite_entrada'],
                ['valor' => $request->hora_limite_entrada]
            );

            // Configuracion::updateOrCreate(
            //     ['clave' => 'hora_limite_salida'],
            //     ['valor' => $request->hora_limite_salida]
            // );


            return redirect()->route('admin.preferencias')->with('success', 'Configuraciones actualizadas correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar usuario ' . $e->getMessage());
        }
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
            ->route('admin.usuarios.configurar')
            ->with('success', 'Vacaciones registradas correctamente.');
    }



    public function storeDiaFestivo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date|unique:dias_festivos,fecha',
        ]);

        DiaFestivo::create([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'oficial' => $request->boolean('oficial'),
        ]);

        return back()->with('success', 'Día festivo agregado correctamente.');
    }
}
