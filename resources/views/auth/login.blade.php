<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; }
        .login-container { max-width: 400px; margin: 5rem auto; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn-google { background: #4285f4; color: white; padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; display: inline-block; width: 100%; text-align: center; margin-bottom: 1rem; }
        .btn-google:hover { background: #3367d6; }
        .divider { text-align: center; margin: 1rem 0; position: relative; }
        .divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e2e8f0; }
        .divider span { background: white; padding: 0 1rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #1f2937;">Iniciar Sesión</h2>

        @if($errors->any())
            <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('auth.google') }}" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 0.5rem;">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuar con Google
        </a>

        <div class="divider">
            <span>o</span>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; color: #374151;">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; color: #374151;">Contraseña</label>
                <input type="password" id="password" name="password" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #3b82f6; color: white; padding: 0.75rem; border: none; border-radius: 4px; cursor: pointer;">
                Iniciar Sesión
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: #6b7280;">
            ¿No tienes cuenta? <a href="{{ route('register') }}" style="color: #3b82f6; text-decoration: none;">Regístrate</a>
        </p>
    </div>
</body>
</html>