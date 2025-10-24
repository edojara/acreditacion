<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1f2937; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .welcome { text-align: center; margin-bottom: 2rem; }
        .role-badge { display: inline-block; padding: 0.25rem 0.75rem; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 0.875rem; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .menu-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; text-align: center; text-decoration: none; color: #374151; transition: all 0.2s; }
        .menu-item:hover { background: #e2e8f0; transform: translateY(-2px); }
        .menu-item.locked { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <strong>{{ config('app.name', 'Laravel') }}</strong>
        </div>
        <div>
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.5rem;">
            @endif
            <span>{{ auth()->user()->name }}</span>
            <span class="role-badge">{{ auth()->user()->role->name ?? 'Sin rol' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; text-decoration: underline; cursor: pointer; padding: 0;">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome">
            <h1>¡Bienvenido, {{ auth()->user()->name }}!</h1>
            <p>Has iniciado sesión exitosamente en el sistema de acreditaciones.</p>
        </div>

        <div class="card">
            <h2>Menú Principal</h2>
            <div class="menu-grid">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="menu-item">
                        <h3>👥 Administración de Usuarios</h3>
                        <p>Gestionar usuarios y permisos de acceso</p>
                    </a>
                @else
                    <div class="menu-item locked">
                        <h3>👥 Administración de Usuarios</h3>
                        <p>Solo para administradores</p>
                    </div>
                @endif

                @if(auth()->user()->isReport() || auth()->user()->isAdmin())
                    <a href="{{ route('reports.index') }}" class="menu-item">
                        <h3>📊 Centro de Reportes</h3>
                        <p>Reportes de usuarios y actividad</p>
                    </a>
                @else
                    <div class="menu-item locked">
                        <h3>📊 Centro de Reportes</h3>
                        <p>Solo para usuarios de informes</p>
                    </div>
                @endif

                @if(auth()->user()->isEnroller() || auth()->user()->isAdmin())
                    <a href="{{ route('enrollments.index') }}" class="menu-item">
                        <h3>📋 Gestión de Acreditaciones</h3>
                        <p>Administrar procesos de acreditación</p>
                    </a>
                @else
                    <div class="menu-item locked">
                        <h3>📋 Gestión de Acreditaciones</h3>
                        <p>Solo para enroladores</p>
                    </div>
                @endif

                @if(auth()->user()->isReadOnly() || auth()->user()->isAdmin())
                    <div class="menu-item">
                        <h3>👁️ Vista de Solo Lectura</h3>
                        <p>Acceso limitado a información</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>