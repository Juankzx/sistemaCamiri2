<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole(['bodeguero', 'vendedor'])) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });

    
}


    // Muestra el listado de usuarios
    public function index()
{
    $users = User::where('estado', true)->paginate(15);
    return view('users.index', compact('users'));
}

    // Muestra el formulario para crear un nuevo usuario
    public function create()
    {
        return view('users.create');
    }

    // Almacena un nuevo usuario en la base de datos
    public function store(Request $request)
{
    // Validar los datos del formulario
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',  // Valida que el email sea único en la tabla users
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string',
        'pin' => 'nullable|string|regex:/^\d{6}$/|unique:users,pin', // Cambiar a string y validar con regex
    ]);

    // Crear el usuario en la base de datos
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'pin' => $request->role === 'vendedor' ? $request->pin : null,
    ]);

    // Asignar el rol al usuario
    $user->assignRole($request->role);

    // Redirigir al índice de usuarios con mensaje de éxito
    return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
}


    // Muestra el formulario para editar un usuario
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Actualiza un usuario en la base de datos
    public function update(Request $request, $id)
    {
        // Validación de los campos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Buscar el usuario a actualizar
        $user = User::findOrFail($id);

        // Actualizar los datos del usuario
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    // Elimina un usuario de la base de datos
    public function destroy(User $user)
{
    // Verificar si el usuario autenticado intenta desactivarse a sí mismo
    if (auth()->id() == $user->id) {
        return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta.');
    }

    // Cambiar el estado del usuario a inactivo en lugar de eliminarlo
    $user->estado = false; // o cualquier valor que represente el estado inactivo, como '0' o 'inactivo'
    $user->save();

    // Redirigir al índice de usuarios con mensaje de éxito
    return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
}

public function mostrarFormularioAsignarRol()
{
    $usuarios = User::all();
    $roles = Role::all();
    return view('roles_permisos.asignar_rol', compact('usuarios', 'roles'));
}

public function asignarRol(Request $request)
{
    $request->validate([
        'usuario_id' => 'required|exists:users,id',
        'rol_id' => 'required|exists:roles,id'
    ]);

    $usuario = User::findOrFail($request->usuario_id);
    $rol = Role::findOrFail($request->rol_id);

    // Asigna el rol al usuario
    $usuario->syncRoles($rol->name);

    return redirect()->back()->with('success', 'Rol asignado correctamente.');
}

public function removerRol(Request $request)
{
    $request->validate([
        'usuario_id' => 'required|exists:users,id',
    ]);

    $usuario = User::findOrFail($request->usuario_id);

    // Remover todos los roles del usuario
    $usuario->syncRoles([]);

    return redirect()->back()->with('success', 'Rol removido correctamente.');
}

}