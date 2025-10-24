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
        <div class="col-lg-3 col-6">
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

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Role::count() }}</h3>
                    <p>Roles Configurados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="small-box-footer">&nbsp;</div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
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

        <div class="col-lg-3 col-6">
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
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <a href="{{ route('users.index') }}" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                <strong>Gestionar Usuarios</strong><br>
                                <small>Crear, editar y eliminar usuarios del sistema</small>
                            </a>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <button class="btn btn-secondary btn-block btn-lg" disabled>
                                <i class="fas fa-shield-alt fa-2x d-block mb-2"></i>
                                <strong>Gestionar Roles</strong><br>
                                <small>Configurar permisos y roles de usuario</small>
                            </button>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <button class="btn btn-info btn-block btn-lg" disabled>
                                <i class="fas fa-envelope fa-2x d-block mb-2"></i>
                                <strong>Invitaciones</strong><br>
                                <small>Enviar invitaciones a nuevos usuarios</small>
                            </button>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <button class="btn btn-warning btn-block btn-lg" disabled>
                                <i class="fas fa-key fa-2x d-block mb-2"></i>
                                <strong>Restablecer Contraseñas</strong><br>
                                <small>Forzar cambio de contraseña a usuarios</small>
                            </button>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <button class="btn btn-success btn-block btn-lg" disabled>
                                <i class="fas fa-chart-line fa-2x d-block mb-2"></i>
                                <strong>Actividad de Usuarios</strong><br>
                                <small>Ver logs de acceso y actividad</small>
                            </button>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                            <button class="btn btn-dark btn-block btn-lg" disabled>
                                <i class="fas fa-cogs fa-2x d-block mb-2"></i>
                                <strong>Configuración de Acceso</strong><br>
                                <small>Configurar políticas de seguridad</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection