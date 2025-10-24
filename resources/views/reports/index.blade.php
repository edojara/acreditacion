<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1f2937; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
        .report-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; background: #f8fafc; }
        .report-card h3 { margin: 0 0 0.5rem 0; color: #1f2937; }
        .report-card p { margin: 0 0 1rem 0; color: #6b7280; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #2563eb; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-item { text-align: center; padding: 1rem; background: #f8fafc; border-radius: 8px; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #3b82f6; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <strong>{{ config('app.name', 'Laravel') }} - Reportes</strong>
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
            <h1>Centro de Reportes de Usuarios</h1>
            <p>Reportes y estadísticas sobre la gestión de usuarios y accesos al sistema.</p>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div>Usuarios Registrados</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ \App\Models\User::whereNotNull('google_id')->count() }}</div>
                <div>Usuarios Google</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ \App\Models\User::whereNull('google_id')->count() }}</div>
                <div>Usuarios Locales</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ \App\Models\Role::count() }}</div>
                <div>Roles Configurados</div>
            </div>
        </div>

        <div class="card">
            <h2>Reportes de Gestión de Usuarios</h2>
            <div class="reports-grid">
                <div class="report-card">
                    <h3>👥 Lista de Usuarios</h3>
                    <p>Usuarios registrados con roles, fechas de registro y estado.</p>
                    <a href="#" class="btn">Ver Lista</a>
                </div>
                <div class="report-card">
                    <h3>📊 Distribución por Roles</h3>
                    <p>Estadísticas de usuarios por cada rol del sistema.</p>
                    <a href="#" class="btn">Ver Gráfico</a>
                </div>
                <div class="report-card">
                    <h3>📅 Actividad Reciente</h3>
                    <p>Usuarios activos en los últimos 30 días.</p>
                    <a href="#" class="btn">Ver Actividad</a>
                </div>
                <div class="report-card">
                    <h3>🔐 Usuarios Pendientes</h3>
                    <p>Usuarios que deben cambiar contraseña.</p>
                    <a href="#" class="btn">Ver Pendientes</a>
                </div>
                <div class="report-card">
                    <h3>📈 Crecimiento de Usuarios</h3>
                    <p>Tendencia de registro de usuarios por mes.</p>
                    <a href="#" class="btn">Ver Tendencia</a>
                </div>
                <div class="report-card">
                    <h3>📤 Exportar Usuarios</h3>
                    <p>Exportar lista de usuarios en Excel/CSV.</p>
                    <a href="#" class="btn">Exportar</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>