<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajeroLoginController extends Controller
{
    public function login(Request $request)
    {
         // Validar el campo de PIN
         $request->validate([
            'pin' => 'required|numeric|digits:6', // Ejemplo: un PIN de 6 dígitos
        ]);

        // Autenticación basada en el PIN
        $user = User::where('pin', $request->pin)->first();

        if ($user && $user->hasRole('cajero')) {
            // Iniciar sesión manualmente para el cajero
            Auth::login($user);

            // Redirigir al dashboard o a la vista de cajeros
            return redirect()->route('cajas')->with('success', 'Inicio de sesión exitoso.');
        }

        // Si el PIN es incorrecto o el rol no es cajero
        return redirect()->back()->with('error', 'PIN inválido o no tienes permisos.');
    }
}
