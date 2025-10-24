<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('User logged in: ' . $user->email);

            // Registrar login en audit log
            \App\Models\AuditLog::log('login', 'Usuario inició sesión con credenciales', [
                'user_email' => $user->email,
                'model_type' => 'User',
                'model_id' => $user->id,
            ]);

            // Si el usuario debe cambiar contraseña, redirigir
            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son correctas.',
        ])->onlyInput('email');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Contraseña cambiada exitosamente.');
    }

    private function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        } elseif ($user->isReport()) {
            return redirect('/reports');
        } elseif ($user->isEnroller()) {
            return redirect('/enrollments');
        } elseif ($user->isReadOnly()) {
            return redirect('/dashboard');
        }

        return redirect('/dashboard');
    }
}