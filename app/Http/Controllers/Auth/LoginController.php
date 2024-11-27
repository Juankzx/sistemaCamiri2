<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

     public function __construct()
     {
         // Middleware para rutas específicas
         $this->middleware('guest')->except(['logout', 'loginWithPin']);
         $this->middleware('auth')->only('logout');
     }
     

    public function loginCajero(Request $request)
{
    $request->validate([
        'pin' => 'required|numeric', // Valida que el PIN sea numérico
    ]);

    $user = User::where('pin', $request->pin)->whereHas('roles', function ($query) {
        $query->where('name', 'cajero');
    })->first();

    if ($user) {
        Auth::login($user);
        return redirect()->route('cajas.index');
    }

    return back()->withErrors(['pin' => 'PIN incorrecto o usuario no tiene acceso.']);
}

public function loginWithPin(Request $request)
{
    //\Log::info('PIN recibido: ' . $request->pin);
    \Log::info('Datos recibidos en loginWithPin:', $request->all());
    
    $request->validate([
        'pin' => 'required|string|digits:6',
    ]);

    $user = User::where('pin', $request->pin)->whereHas('roles', function ($query) {
        $query->where('name', 'vendedor'); // Validar solo usuarios con rol 'vendedor'
    })->first();

    if ($user) {
        Auth::login($user);

        return response()->json([
            'success' => true,
            'redirect' => route('cajas.index'), // Ruta después de autenticarse
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'PIN incorrecto.',
        ], 401);
    }
}
}
