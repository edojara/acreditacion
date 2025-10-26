<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EducationalEntityController;
use App\Http\Controllers\EntityContactController;

Route::get('/', function () {
    // Si el usuario ya está autenticado, redirigir a la página principal
    if (auth()->check()) {
        return redirect()->route('educational-entities.index');
    }

    // Si no está autenticado, mostrar login
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Google OAuth Routes
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

Route::middleware('auth')->group(function () {
    // Dashboard principal redirige a instituciones
    Route::get('/dashboard', function () {
        return redirect()->route('educational-entities.index');
    })->name('dashboard');

    // Ruta para cambio de contraseña obligatorio
    Route::get('/password/change', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [LoginController::class, 'changePassword'])->name('password.change.post');

    // Rutas protegidas por roles
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // User Management Routes
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::patch('users/{user}/force-password-change', [App\Http\Controllers\UserController::class, 'forcePasswordChange'])->name('users.force-password-change');
        Route::patch('users/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');

        // Audit Logs Routes
        Route::resource('audit-logs', App\Http\Controllers\AuditLogController::class)->only(['index', 'show']);
    });

    // Rutas para admin y enrolador
    Route::middleware(['auth', 'role:admin,enrolador'])->group(function () {
        // Educational Entities Routes
        Route::resource('educational-entities', EducationalEntityController::class);

        // Entity Contacts Routes
        Route::resource('entity-contacts', EntityContactController::class);
    });

    Route::middleware('role:report')->group(function () {
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });

    Route::middleware('role:enroller')->group(function () {
        Route::get('/enrollments', function () {
            return view('enrollments.index');
        })->name('enrollments.index');
    });

    Route::post('/logout', function () {
        // Registrar logout en audit log antes de cerrar sesión
        if (auth()->check()) {
            \App\Models\AuditLog::log('logout', 'Usuario cerró sesión', [
                'user_email' => auth()->user()->email,
                'model_type' => 'User',
                'model_id' => auth()->id(),
            ]);
        }

        auth()->logout();
        return redirect('/');
    })->name('logout');
});
