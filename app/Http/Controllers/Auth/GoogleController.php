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

            // PRIMERO: Verificar si existe usuario con google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Usuario ya vinculado con Google, permitir acceso
                // Actualizar avatar si cambió
                if ($user->avatar !== $googleUser->getAvatar()) {
                    $user->update(['avatar' => $googleUser->getAvatar()]);
                }
            } else {
                // SEGUNDO: Verificar si el email está pre-registrado (sin google_id)
                $preRegisteredUser = User::where('email', $googleUser->getEmail())
                                        ->whereNull('google_id')
                                        ->first();

                if (!$preRegisteredUser) {
                    // Usuario NO pre-registrado, DENEGAR ACCESO COMPLETAMENTE
                    \App\Models\AuditLog::log('login_google_denied', 'Intento de login con Google DENEGADO - usuario no pre-registrado: ' . $googleUser->getEmail(), [
                        'user_email' => $googleUser->getEmail(),
                        'ip_address' => request()->ip(),
                    ]);

                    return redirect('/login')->with('error', 'Acceso denegado. Solo usuarios pre-registrados por administradores pueden acceder con Google.');
                }

                // Usuario pre-registrado encontrado, vincular cuenta Google
                $preRegisteredUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName(), // Actualizar nombre si cambió
                ]);

                $user = $preRegisteredUser;

                \App\Models\AuditLog::log('google_account_linked', 'Cuenta Google vinculada exitosamente a usuario pre-registrado: ' . $user->email, [
                    'model_type' => 'User',
                    'model_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }

            Auth::login($user);

            // Registrar login con Google en audit log
            \App\Models\AuditLog::log('login_google', 'Usuario inició sesión con Google OAuth', [
                'user_email' => $user->email,
                'model_type' => 'User',
                'model_id' => $user->id,
            ]);

            // Si el usuario debe cambiar contraseña, redirigir a cambio de contraseña
            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }

            return $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());
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