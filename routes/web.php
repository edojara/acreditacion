<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Ruta para cambio de contraseña obligatorio
    Route::get('/password/change', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [LoginController::class, 'changePassword'])->name('password.change.post');

    // Rutas protegidas por roles
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    Route::middleware('role:informe')->group(function () {
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });

    Route::middleware('role:enrolador')->group(function () {
        Route::get('/enrollments', function () {
            return view('enrollments.index');
        })->name('enrollments.index');
    });

    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});
