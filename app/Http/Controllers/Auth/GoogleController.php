<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())
                       ->orWhere('email', $googleUser->getEmail())
                       ->first();

            if ($user) {
                // Usuario existente, actualizar datos de Google si no los tiene
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Nuevo usuario, asignar rol por defecto (enrolador)
                $defaultRole = Role::where('slug', 'enrolador')->first();

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role_id' => $defaultRole->id ?? 1,
                    'password' => bcrypt(uniqid()), // Contraseña aleatoria ya que usa Google
                ]);
            }

            Auth::login($user);

            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google');
        }
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