@extends('layouts.app')

@section('title', 'Logs de Auditoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
<li class="breadcrumb-item active">Logs de Auditoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['total_logs']) }}</h3>
                    <p>Total de Logs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['today_logs']) }}</h3>
                    <p>Logs de Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['login_attempts']) }}</h3>
                    <p>Intentos de Login</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['failed_logins']) }}</h3>
                    <p>Logins Fallidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filtros de Búsqueda
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="action" class="mr-2">Acción:</label>
                    <select name="action" id="action" class="form-control">
                        <option value="">Todas las acciones</option>
                        <option value="login_google" {{ request('action') == 'login_google' ? 'selected' : '' }}>Login Google</option>
                        <option value="login_google_denied" {{ request('action') == 'login_google_denied' ? 'selected' : '' }}>Login Denegado</option>
                        <option value="user_created" {{ request('action') == 'user_created' ? 'selected' : '' }}>Usuario Creado</option>
                        <option value="user_updated" {{ request('action') == 'user_updated' ? 'selected' : '' }}>Usuario Actualizado</option>
                        <option value="user_deleted" {{ request('action') == 'user_deleted' ? 'selected' : '' }}>Usuario Eliminado</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="date_from" class="mr-2">Desde:</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="form-group mr-3">
                    <label for="date_to" class="mr-2">Hasta:</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <button type="submit" class="btn btn-primary mr-2">
                    <i class="fas fa-search"></i> Filtrar
                </button>

                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
    </div>

    <!-- Tabla de Logs -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Registro de Actividad
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acción</th>
                        <th>Detalles</th>
                        <th>IP</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                {{ $log->user->name }}
                                <br><small class="text-muted">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted">Usuario no encontrado</span>
                            @endif
                        </td>
                        <td>
                            @if($log->user && $log->user->role)
                                <span class="badge badge-{{ $log->user->role->slug == 'admin' ? 'danger' : ($log->user->role->slug == 'reportes' ? 'warning' : 'info') }}">
                                    {{ $log->user->role->name }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @switch($log->action)
                                @case('login_google')
                                    <span class="badge badge-success">
                                        <i class="fab fa-google mr-1"></i>Login Exitoso
                                    </span>
                                    @break
                                @case('login_google_denied')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times mr-1"></i>Login Denegado
                                    </span>
                                    @break
                                @case('user_created')
                                    <span class="badge badge-primary">
                                        <i class="fas fa-user-plus mr-1"></i>Usuario Creado
                                    </span>
                                    @break
                                @case('user_updated')
                                    <span class="badge badge-info">
                                        <i class="fas fa-user-edit mr-1"></i>Usuario Actualizado
                                    </span>
                                    @break
                                @case('user_deleted')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-user-times mr-1"></i>Usuario Eliminado
                                    </span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $log->action }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($log->details)
                                <span title="{{ $log->details }}">{{ Str::limit($log->details, 50) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $log->ip_address ?: '-' }}</td>
                        <td>
                            <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <br>No se encontraron registros de auditoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $auditLogs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection