<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación condicional según tipo de cuenta
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'account_type' => 'required|in:local,google'
        ];

        if ($request->account_type === 'local') {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['must_change_password'] = 'boolean';
        }
        // Para cuentas Google no se requiere contraseña ni forzar cambio

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'role_id' => $request->role_id,
            'must_change_password' => $request->has('must_change_password'),
        ];

        if ($request->account_type === 'local') {
            // Cuenta local
            $userData['email'] = $request->email;
            $userData['password'] = Hash::make($request->password);
            $userData['google_id'] = null; // Asegurar que no tenga google_id
        } else {
            // Cuenta Google - el usuario podrá acceder con Google OAuth
            $userData['email'] = $request->email; // Usar el email común
            $userData['password'] = Hash::make('temp_password_' . time()); // Contraseña temporal
            $userData['google_id'] = 'pending'; // Marcador para indicar que debe vincularse con Google
            $userData['must_change_password'] = false; // No necesita cambiar contraseña ya que usará Google
        }

        $user = User::create($userData);

        $message = $request->account_type === 'local'
            ? 'Usuario local creado exitosamente.'
            : 'Usuario Google creado exitosamente. El usuario podrá acceder con su cuenta Google.';

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'must_change_password' => 'boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'must_change_password' => $request->has('must_change_password'),
        ]);

        // Si se marca para cambiar contraseña, forzar el cambio
        if ($request->has('must_change_password')) {
            $user->update(['must_change_password' => true]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // No permitir eliminar al propio usuario
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Force password change for a user.
     */
    public function forcePasswordChange(User $user)
    {
        $user->update(['must_change_password' => true]);
        return redirect()->route('users.index')->with('success', 'Se ha forzado el cambio de contraseña para el usuario.');
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(User $user)
    {
        $defaultPassword = 'Abcd.1234';
        $user->update([
            'password' => Hash::make($defaultPassword),
            'must_change_password' => true
        ]);

        return redirect()->route('users.index')->with('success', 'Contraseña reseteada. El usuario debe cambiarla en el próximo login.');
    }
}