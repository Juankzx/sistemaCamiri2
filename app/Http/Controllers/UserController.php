<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
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
    ]);

    // Crear el usuario en la base de datos
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

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

}
