@extends('layouts.app')

@section('title', 'Logs de Auditoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
<li class="breadcrumb-item active">Logs de Auditoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Minimalista -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Logs de Auditoría</h1>
                    <p class="text-muted mb-0">Registro completo de actividad del sistema</p>
                </div>
                <div class="text-right">
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Logs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_logs']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Logs de Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['today_logs']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Intentos Login
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['login_attempts']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Logins Fallidos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['failed_logins']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Minimalistas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
                    <div class="d-flex align-items-center">
                        @if(request()->hasAny(['action', 'date_from', 'date_to']))
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="collapse" data-target="#filtersCollapse">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
                <div class="collapse {{ request()->hasAny(['action', 'date_from', 'date_to']) ? 'show' : '' }}" id="filtersCollapse">
                    <div class="card-body">
                        <form method="GET" action="{{ route('audit-logs.index') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="action" class="form-label">Acción</label>
                                        <select name="action" id="action" class="form-control">
                                            <option value="">Todas las acciones</option>
                                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login Exitoso</option>
                                            <option value="login_failed" {{ request('action') == 'login_failed' ? 'selected' : '' }}>Login Fallido</option>
                                            <option value="login_google" {{ request('action') == 'login_google' ? 'selected' : '' }}>Login Google</option>
                                            <option value="login_google_denied" {{ request('action') == 'login_google_denied' ? 'selected' : '' }}>Login Denegado</option>
                                            <option value="user_created" {{ request('action') == 'user_created' ? 'selected' : '' }}>Usuario Creado</option>
                                            <option value="user_updated" {{ request('action') == 'user_updated' ? 'selected' : '' }}>Usuario Actualizado</option>
                                            <option value="user_deleted" {{ request('action') == 'user_deleted' ? 'selected' : '' }}>Usuario Eliminado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_from" class="form-label">Desde</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_to" class="form-label">Hasta</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Minimalista -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Registro de Actividad</h6>
                    <span class="badge badge-primary">{{ $auditLogs->total() }} registros</span>
                </div>
                <div class="card-body">
                    @if($auditLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Fecha/Hora</th>
                                        <th class="border-0">Usuario</th>
                                        <th class="border-0">Acción</th>
                                        <th class="border-0">Detalles</th>
                                        <th class="border-0">IP</th>
                                        <th class="border-0 text-center">Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLogs as $log)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <div class="d-flex flex-column">
                                                    <span>{{ $log->user->name }}</span>
                                                    <small class="text-muted">{{ $log->user->email }}</small>
                                                    @if($log->user->role)
                                                        <span class="badge badge-sm badge-{{ $log->user->role->slug == 'admin' ? 'danger' : 'secondary' }} mt-1">
                                                            {{ $log->user->role->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                @if(in_array($log->action, ['login_failed', 'login_google_denied']))
                                                    <div class="d-flex flex-column">
                                                        <span class="text-warning">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            Email no registrado
                                                        </span>
                                                        @if($log->details && str_contains($log->details, 'email no encontrado'))
                                                            <small class="text-muted">{{ Str::after($log->details, 'email no encontrado en usuarios pre-registrados: ') ?: Str::after($log->details, 'email: ') }}</small>
                                                        @elseif($log->old_values && isset($log->old_values['attempted_email']))
                                                            <small class="text-muted">{{ $log->old_values['attempted_email'] }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Usuario no encontrado</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @switch($log->action)
                                                @case('login')
                                                    <span class="badge badge-success badge-sm">
                                                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                                                    </span>
                                                    @break
                                                @case('login_failed')
                                                    <span class="badge badge-danger badge-sm">
                                                        <i class="fas fa-times mr-1"></i>Fallido
                                                    </span>
                                                    @break
                                                @case('login_google')
                                                    <span class="badge badge-info badge-sm">
                                                        <i class="fab fa-google mr-1"></i>Google
                                                    </span>
                                                    @break
                                                @case('login_google_denied')
                                                    <span class="badge badge-warning badge-sm">
                                                        <i class="fas fa-ban mr-1"></i>Denegado
                                                    </span>
                                                    @break
                                                @case('user_created')
                                                    <span class="badge badge-primary badge-sm">
                                                        <i class="fas fa-user-plus mr-1"></i>Creado
                                                    </span>
                                                    @break
                                                @case('user_updated')
                                                    <span class="badge badge-secondary badge-sm">
                                                        <i class="fas fa-user-edit mr-1"></i>Actualizado
                                                    </span>
                                                    @break
                                                @case('user_deleted')
                                                    <span class="badge badge-danger badge-sm">
                                                        <i class="fas fa-user-times mr-1"></i>Eliminado
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="badge badge-light badge-sm">{{ $log->action }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($log->details)
                                                <span title="{{ $log->details }}" class="d-inline-block text-truncate" style="max-width: 200px;">
                                                    {{ Str::limit($log->details, 50) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-muted small">{{ $log->ip_address ?: '-' }}</code>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $auditLogs->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No se encontraron registros</h5>
                            <p class="text-muted">No hay actividad registrada con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
    border-bottom: 2px solid #e3e6f0;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #e9ecef;
}

.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.text-primary {
    color: #5a5c69 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-muted {
    color: #858796 !important;
}

.text-gray-300 {
    color: #d1d3e2 !important;
}

.text-gray-500 {
    color: #6c757d !important;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.btn-outline-primary:hover, .btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
}
</style>
@endsection