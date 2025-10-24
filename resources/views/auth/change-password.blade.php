<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; }
        .container { max-width: 400px; margin: 5rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .alert { background: #fef3c7; color: #d97706; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; background: #3b82f6; color: white; padding: 0.75rem; border: none; border-radius: 4px; cursor: pointer; margin-top: 1rem; }
        .btn:hover { background: #2563eb; }
        .password-requirements { font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Cambiar Contraseña</h2>

        @if(session('success'))
            <div class="alert" style="background: #d1fae5; color: #059669;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert">
            <strong>¡Importante!</strong> Esta es tu primera vez iniciando sesión. Debes cambiar tu contraseña por seguridad.
        </div>

        <form method="POST" action="{{ route('password.change.post') }}">
            @csrf

            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">
                    La contraseña debe tener al menos 8 caracteres.
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn">Cambiar Contraseña</button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: #6b7280;">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #dc2626; text-decoration: underline; cursor: pointer; padding: 0;">Cerrar Sesión</button>
            </form>
        </p>
    </div>
</body>
</html>