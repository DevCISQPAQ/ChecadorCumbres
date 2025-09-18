<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // if (Auth::user()->is_admin) {
              return redirect('/admin/dashboard');
            // } else {
            // Auth::logout();
            // return redirect('/login')->withErrors(['no_admin' => 'Acceso solo para administradores.']);
            // }
        }

        return redirect('/login')->withErrors(['login_error' => 'Credenciales inválidas.']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function dashboard()
    {
        // if (!Auth::check() || !Auth::user()->is_admin) {
        //     abort(403, 'Acceso no autorizado');
        // }

        if (!Auth::check()) {
            abort(403, 'Acceso no autorizado');
        }

        $usuarios = User::count();
        $activosHoy = User::whereDate('updated_at', now()->toDateString())->count();
        $pendientes = 8; // Aquí podrías usar algún modelo real

        return view('admin.dashboard', compact('usuarios', 'activosHoy', 'pendientes'));
    }
}
