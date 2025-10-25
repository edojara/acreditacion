@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('breadcrumb')
<li class="breadcrumb-item active">Panel Admin</li>
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-2"></i>
                        Panel de Administración de Usuarios
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-4">Gestión completa de usuarios y permisos de acceso al sistema de acreditaciones.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">
                    Ver Usuarios <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('must_change_password', true)->count() }}</h3>
                    <p>Usuarios Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="small-box-footer">&nbsp;</div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\User::whereNotNull('google_id')->count() }}</h3>
                    <p>Usuarios Google</p>
                </div>
                <div class="icon">
                    <i class="fab fa-google"></i>
                </div>
                <div class="small-box-footer">&nbsp;</div>
            </div>
        </div>
    </div>

    <!-- Gestión de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-2"></i>
                        Gestión de Usuarios y Accesos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-users fa-lg d-block mb-2"></i>
                                <strong>Gestionar Usuarios</strong><br>
                                <small>Crear, editar y eliminar usuarios</small>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <a href="{{ route('audit-logs.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-chart-line fa-lg d-block mb-2"></i>
                                <strong>Actividad de Usuarios</strong><br>
                                <small>Ver logs de acceso y actividad</small>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <a href="{{ route('users.create') }}" class="btn btn-info btn-block">
                                <i class="fas fa-user-plus fa-lg d-block mb-2"></i>
                                <strong>Crear Usuario</strong><br>
                                <small>Agregar nuevo usuario al sistema</small>
                            </a>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <a href="#" class="btn btn-warning btn-block">
                                <i class="fas fa-cogs fa-lg d-block mb-2"></i>
                                <strong>Configuración</strong><br>
                                <small>Ajustes del sistema</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection