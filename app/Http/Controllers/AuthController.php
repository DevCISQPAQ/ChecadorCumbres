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
              return redirect('/admin/asistencias');
            // } else {
            // Auth::logout();
            // return redirect('/login')->withErrors(['no_admin' => 'Acceso solo para administradores.']);
            // }
        }

        return redirect('/admin')->withErrors(['login_error' => 'Credenciales inválidas.']);
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }

   
}
