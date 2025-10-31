@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('breadcrumb')
<li class="breadcrumb-item active">Panel Admin</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Minimalista -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
                    <p class="text-muted mb-0">Sistema de Acreditación</p>
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
                                Usuarios Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::where('must_change_password', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Con Google
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\User::whereNotNull('google_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-google fa-2x text-gray-300"></i>
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
                                Activos Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\AuditLog::whereDate('created_at', today())->where('action', 'login')->distinct('user_id')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menú Compacto -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="row text-center">
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('users.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center justify-content-center p-2 rounded hover-bg-light">
                                    <i class="fas fa-users fa-lg text-primary mr-2"></i>
                                    <span class="text-primary font-weight-bold">Gestionar Usuarios</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 mb-2">
                            <a href="{{ route('audit-logs.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center justify-content-center p-2 rounded hover-bg-light">
                                    <i class="fas fa-history fa-lg text-info mr-2"></i>
                                    <span class="text-info font-weight-bold">Ver Actividad</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-4 mb-2">
                            <a href="{{ route('educational-entities.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center justify-content-center p-2 rounded hover-bg-light">
                                    <i class="fas fa-university fa-lg text-success mr-2"></i>
                                    <span class="text-success font-weight-bold">Instituciones</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente</h6>
                </div>
                <div class="card-body">
                    @php
                        $recentLogs = \App\Models\AuditLog::with('user')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($recentLogs->count() > 0)
                        <div class="timeline timeline-inverse">
                            @foreach($recentLogs as $log)
                            <div class="time-label">
                                <span class="bg-primary">{{ $log->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-user bg-info"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $log->created_at->diffForHumans() }}</span>
                                    <h3 class="timeline-header">
                                        <strong>{{ $log->user->name ?? 'Sistema' }}</strong>
                                        {{ $log->description ?? ucfirst($log->action) }}
                                    </h3>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                            No hay actividad reciente para mostrar
                        </p>
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

.hover-bg-light:hover {
    background-color: #f8f9fc !important;
    transition: background-color 0.2s ease;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
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
</style>
@endsection