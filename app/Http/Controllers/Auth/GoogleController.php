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
            \Log::info('Google OAuth callback iniciado', [
                'url' => request()->fullUrl(),
                'params' => request()->all()
            ]);

            $googleUser = Socialite::driver('google')->user();

            \Log::info('Datos de Google OAuth recibidos', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ]);

            // Verificar si el email existe en la lista de usuarios pre-registrados
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                \Log::warning('Usuario NO encontrado en lista de pre-registrados - DENEGANDO ACCESO', [
                    'email' => $googleUser->getEmail(),
                    'ip' => request()->ip()
                ]);

                // Usuario NO pre-registrado, DENEGAR ACCESO COMPLETAMENTE
                \App\Models\AuditLog::log('login_google_denied', 'Intento de login con Google DENEGADO - email no encontrado en usuarios pre-registrados: ' . $googleUser->getEmail(), [
                    'user_email' => $googleUser->getEmail(),
                    'ip_address' => request()->ip(),
                ]);

                return redirect('/login')->with('error', 'Acceso denegado. Tu email no está registrado en el sistema. Contacta a un administrador.');
            }

            \Log::info('Usuario pre-registrado encontrado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_google_id' => !is_null($user->google_id)
            ]);

            // Si el usuario ya tiene google_id, verificar que coincida
            if (!is_null($user->google_id)) {
                if ($user->google_id !== $googleUser->getId()) {
                    \Log::warning('Usuario tiene google_id diferente - DENEGANDO ACCESO', [
                        'user_id' => $user->id,
                        'existing_google_id' => $user->google_id,
                        'attempted_google_id' => $googleUser->getId()
                    ]);

                    \App\Models\AuditLog::log('login_google_denied', 'Intento de login con Google DENEGADO - cuenta Google diferente para usuario: ' . $user->email, [
                        'user_email' => $user->email,
                        'ip_address' => request()->ip(),
                    ]);

                    return redirect('/login')->with('error', 'Esta cuenta de Google no está vinculada a tu usuario. Contacta a un administrador.');
                }
            } else {
                // Usuario pre-registrado sin google_id, vincular cuenta Google
                \Log::info('Vinculando cuenta Google a usuario pre-registrado', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'name' => $googleUser->getName(), // Actualizar nombre si cambió
                ]);

                \App\Models\AuditLog::log('google_account_linked', 'Cuenta Google vinculada exitosamente a usuario pre-registrado: ' . $user->email, [
                    'model_type' => 'User',
                    'model_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }

            // Actualizar avatar si cambió
            if ($user->avatar !== $googleUser->getAvatar()) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }

            // Actualizar último login
            $user->update(['last_login_at' => now()]);

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