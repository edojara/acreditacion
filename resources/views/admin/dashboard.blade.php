<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administración - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1f2937; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #3b82f6; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
        .menu-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; text-decoration: none; color: #374151; transition: all 0.2s; }
        .menu-item:hover { background: #e2e8f0; transform: translateY(-2px); }
        .menu-item h3 { margin: 0 0 0.5rem 0; color: #1f2937; }
        .menu-item p { margin: 0; color: #6b7280; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <strong>{{ config('app.name', 'Laravel') }} - Panel de Administración</strong>
        </div>
        <div>
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 0.5rem;">
            @endif
            <span>{{ auth()->user()->name }}</span>
            <a href="{{ route('dashboard') }}">← Volver al Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; text-decoration: underline; cursor: pointer; padding: 0;">Cerrar Sesión</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h1>Panel de Administración</h1>
            <p>Bienvenido al panel de control completo del sistema de acreditaciones.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div>Usuarios Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ \App\Models\Role::count() }}</div>
                <div>Roles Configurados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div>Acreditaciones Activas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div>Reportes Generados</div>
            </div>
        </div>

        <div class="card">
            <h2>Herramientas de Administración</h2>
            <div class="menu-grid">
                <a href="#" class="menu-item">
                    <h3>👥 Gestionar Usuarios</h3>
                    <p>Crear, editar y eliminar usuarios del sistema</p>
                </a>
                <a href="#" class="menu-item">
                    <h3>🔐 Gestionar Roles</h3>
                    <p>Configurar permisos y roles de usuario</p>
                </a>
                <a href="#" class="menu-item">
                    <h3>📊 Reportes Avanzados</h3>
                    <p>Generar reportes detallados del sistema</p>
                </a>
                <a href="#" class="menu-item">
                    <h3>⚙️ Configuración del Sistema</h3>
                    <p>Ajustes generales de la aplicación</p>
                </a>
                <a href="#" class="menu-item">
                    <h3>📋 Acreditaciones</h3>
                    <p>Gestionar procesos de acreditación</p>
                </a>
                <a href="#" class="menu-item">
                    <h3>📈 Estadísticas</h3>
                    <p>Ver métricas y análisis del sistema</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>