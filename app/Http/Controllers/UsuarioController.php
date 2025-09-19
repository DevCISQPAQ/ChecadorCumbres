<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'yes_notifications' => 'nullable|boolean', // 👉 validación del nuevo campo
        ]);

        try {

            User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'level_user' => $request->level_user,
                'yes_notifications' => $request->yes_notifications ?? false, // 👉 guardar campo
            ]);

            return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente.');
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
            'password' => 'nullable|min:6', // 👉 ahora es opcional
            'level_user' => 'required|integer|in:0,1,2',
            'yes_notifications' => 'nullable|boolean', // 👉 validación
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

            return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar usuario ' . $e->getMessage());
        }
    }

    public function eliminarUsuario($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar usuario ' . $e->getMessage());
        }
    }
}
