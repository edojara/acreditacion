@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">¡Bienvenido, {{ auth()->user()->name }}!</h3>
                </div>
                <div class="card-body">
                    <p>Has iniciado sesión exitosamente en el sistema de acreditaciones.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas Generales</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ \App\Models\EducationalEntity::count() }}</h3>
                                    <p>Instituciones Educativas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-university"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ \App\Models\Participant::count() }}</h3>
                                    <p>Integrantes Registrados</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ \App\Models\AuditLog::whereDate('created_at', today())->count() }}</h3>
                                    <p>Actividades Hoy</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ \App\Models\AuditLog::count() }}</h3>
                                    <p>Total de Logs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-history"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Accesos Rápidos</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('admin'))
                            <div class="col-md-3">
                                <a href="{{ route('users.index') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-users"></i> Gestión de Usuarios
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('enrolador'))
                            <div class="col-md-3">
                                <a href="{{ route('educational-entities.index') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-university"></i> Instituciones
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                            <div class="col-md-3">
                                <a href="{{ route('entity-contacts.index') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-address-book"></i> Contactos
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('informe'))
                            <div class="col-md-3">
                                <a href="{{ route('reports.index') }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-chart-bar"></i> Reportes
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('enrolador'))
                            <div class="col-md-3">
                                <a href="{{ route('enrollments.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-graduation-cap"></i> Inscripciones
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                            <div class="col-md-3">
                                <a href="{{ route('audit-logs.index') }}" class="btn btn-dark btn-block">
                                    <i class="fas fa-history"></i> Logs de Auditoría
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection