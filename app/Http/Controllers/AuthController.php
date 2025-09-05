<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

 public function login(Request $request)
{
    $request->validate([
        'usuario' => 'required|string',
        'password' => 'required|string',
        'g-recaptcha-response' => 'required|captcha', // <- validación de recaptcha
    ], [
        'g-recaptcha-response.required' => 'Por favor confirma que no eres un robot.',
        'g-recaptcha-response.captcha'  => 'Validación reCAPTCHA fallida. Inténtalo de nuevo.',
    ]);

    $credentials = $request->only('usuario', 'password');
    $credentials['usuario'] = trim($credentials['usuario'] ?? '');

    $user = User::whereRaw('BINARY `usuario` = ?', [$credentials['usuario']])->first();

    if ($user && Hash::check($credentials['password'], $user->contraseña)) {
        Auth::login($user);
        $request->session()->regenerate();

        switch ($user->rol) {
            case 'admin':
                return redirect()->route('admin.inicio');
            case 'avicultor':
                return redirect()->route('avicultor.inicio');
            case 'vendedor':
                return redirect()->route('vendedor.inicio');
            default:
                Auth::logout();
                return redirect('/login')->withErrors(['usuario' => 'Rol no autorizado.']);
        }
    }

    return back()->withErrors(['usuario' => 'Credenciales inválidas'])->withInput();
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:60|unique:usuarios,usuario',
            'correo' => 'nullable|email|unique:usuarios,correo',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'correo' => $request->correo,
            'contraseña' => Hash::make($request->password),
            'rol' => 'admin', // o el rol que necesites
        ]);

        return redirect()->route('login')->with('success', 'Cuenta creada. Ahora puedes iniciar sesión.');
    }
}
