<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Acreditaciones - {{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        body { font-family: 'Instrument Sans', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1f2937; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .actions { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #2563eb; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table th { background: #f8fafc; font-weight: 600; }
        .status { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; }
        .status.pending { background: #fef3c7; color: #d97706; }
        .status.approved { background: #d1fae5; color: #059669; }
        .status.rejected { background: #fee2e2; color: #dc2626; }
        .empty-state { text-align: center; padding: 3rem; color: #6b7280; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <strong>{{ config('app.name', 'Laravel') }} - Gestión de Acreditaciones</strong>
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
            <h1>Gestión de Acreditaciones</h1>
            <p>Administra los procesos de acreditación y enrolamiento de usuarios.</p>
        </div>

        <div class="actions">
            <a href="#" class="btn">➕ Nueva Acreditación</a>
            <a href="#" class="btn btn-secondary">📤 Importar Datos</a>
            <a href="#" class="btn btn-secondary">📥 Exportar Lista</a>
        </div>

        <div class="card">
            <h2>Acreditaciones Activas</h2>

            @if(true) <!-- Simulando que no hay acreditaciones aún -->
                <div class="empty-state">
                    <h3>📋 No hay acreditaciones registradas</h3>
                    <p>Comienza creando la primera acreditación para el sistema.</p>
                    <a href="#" class="btn" style="margin-top: 1rem;">Crear Primera Acreditación</a>
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Tipo de Acreditación</th>
                            <th>Fecha Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí irían las filas de acreditaciones -->
                        <tr>
                            <td>#001</td>
                            <td>Juan Pérez</td>
                            <td>Certificación Profesional</td>
                            <td>2025-01-15</td>
                            <td><span class="status pending">Pendiente</span></td>
                            <td>
                                <a href="#" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Ver</a>
                                <a href="#" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Editar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card">
            <h2>Estadísticas de Acreditaciones</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #f59e0b;">0</div>
                    <div>Pendientes</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #10b981;">0</div>
                    <div>Aprobadas</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #ef4444;">0</div>
                    <div>Rechazadas</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #3b82f6;">0</div>
                    <div>Total</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>