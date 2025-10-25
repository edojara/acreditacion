@extends('layouts.app')

@section('title', 'Detalle del Log de Auditoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
<li class="breadcrumb-item"><a href="{{ route('audit-logs.index') }}">Logs de Auditoría</a></li>
<li class="breadcrumb-item active">Detalle del Log</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye mr-2"></i>
                        Detalle del Log de Auditoría
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">ID del Log:</th>
                                    <td>{{ $auditLog->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha/Hora:</th>
                                    <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario:</th>
                                    <td>
                                        @if($auditLog->user)
                                            {{ $auditLog->user->name }}
                                            <br><small class="text-muted">{{ $auditLog->user->email }}</small>
                                        @else
                                            <span class="text-muted">Usuario no encontrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Rol del Usuario:</th>
                                    <td>
                                        @if($auditLog->user && $auditLog->user->role)
                                            <span class="badge badge-{{ $auditLog->user->role->slug == 'admin' ? 'danger' : ($auditLog->user->role->slug == 'reportes' ? 'warning' : 'info') }}">
                                                {{ $auditLog->user->role->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Acción:</th>
                                    <td>
                                        @switch($auditLog->action)
                                            @case('login_google')
                                                <span class="badge badge-success">
                                                    <i class="fab fa-google mr-1"></i>Login Google Exitoso
                                                </span>
                                                @break
                                            @case('login_google_denied')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times mr-1"></i>Login Google Denegado
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
                                                <span class="badge badge-secondary">{{ $auditLog->action }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dirección IP:</th>
                                    <td>{{ $auditLog->ip_address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent:</th>
                                    <td>
                                        @if($auditLog->user_agent)
                                            <small class="text-muted">{{ Str::limit($auditLog->user_agent, 100) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Detalles Adicionales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($auditLog->details)
                                        <pre style="background: #f8f9fa; padding: 1rem; border-radius: 4px; font-size: 0.9rem; white-space: pre-wrap;">{{ $auditLog->details }}</pre>
                                    @else
                                        <p class="text-muted">No hay detalles adicionales para este registro.</p>
                                    @endif
                                </div>
                            </div>

                            @if($auditLog->old_values || $auditLog->new_values)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-exchange-alt mr-2"></i>
                                        Cambios Realizados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($auditLog->old_values)
                                        <div class="col-md-6">
                                            <h6>Valores Anteriores:</h6>
                                            <pre style="background: #fee; padding: 0.5rem; border-radius: 4px; font-size: 0.8rem;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        @endif
                                        @if($auditLog->new_values)
                                        <div class="col-md-6">
                                            <h6>Valores Nuevos:</h6>
                                            <pre style="background: #efe; padding: 0.5rem; border-radius: 4px; font-size: 0.8rem;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection