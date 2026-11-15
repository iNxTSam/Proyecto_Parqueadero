<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $usuario = Usuario::where('cedula', $request->numeroDocumento)->first();

        if (! $usuario || ! Hash::check($request->clave, $usuario->password)) {
            throw ValidationException::withMessages([
                'numeroDocumento' => 'Documento o contraseña incorrectos.',
            ]);
        }

        if (! $usuario->activo) {
            throw ValidationException::withMessages([
                'numeroDocumento' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ]);
        }

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route($usuario->dashboardRoute()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
