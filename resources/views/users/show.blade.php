@extends('layouts.app')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        Detalles del Usuario: {{ $user->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="user-avatar mb-3">
                                <i class="fas fa-user-circle fa-5x text-secondary"></i>
                            </div>
                            <h4>{{ $user->name }}</h4>
                            <p class="text-muted">{{ $user->email }}</p>
                            <span class="badge badge-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'report' ? 'warning' : 'info') }} badge-lg">
                                {{ ucfirst($user->role->name) }}
                            </span>
                        </div>

                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="30%">ID de Usuario:</th>
                                        <td>{{ $user->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Correo Electrónico:</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rol:</th>
                                        <td>
                                            <span class="badge badge-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'report' ? 'warning' : 'info') }}">
                                                {{ ucfirst($user->role->name) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tipo de Cuenta:</th>
                                        <td>
                                            @if($user->google_id === 'pending')
                                                <span class="badge badge-info">
                                                    <i class="fab fa-google"></i> Pendiente vinculación Google
                                                </span>
                                            @elseif($user->google_id)
                                                <span class="badge badge-success">
                                                    <i class="fab fa-google"></i> Google OAuth
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-key"></i> Local
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Estado de Contraseña:</th>
                                        <td>
                                            @if($user->must_change_password)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Debe cambiar contraseña
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Contraseña actualizada
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación:</th>
                                        <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización:</th>
                                        <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Último Login:</th>
                                        <td>
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Nunca' }}
                                        </td>
                                    </tr>
                                    @if($user->google_id)
                                    <tr>
                                        <th>ID de Google:</th>
                                        <td>{{ $user->google_id }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>Acciones Rápidas</h5>
                            <div class="btn-group">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Usuario
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline ml-2"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar Usuario
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="btn-group ml-3">
                                <form action="{{ route('users.force-password-change', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-key"></i> Forzar Cambio de Contraseña
                                    </button>
                                </form>
                                <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline ml-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-dark"
                                            onclick="return confirm('¿Resetear contraseña a Abcd.1234?')">
                                        <i class="fas fa-undo"></i> Resetear Contraseña
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.user-avatar {
    margin-bottom: 1rem;
}

.badge-lg {
    font-size: 1em;
    padding: 0.5rem 1rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border: none;
}

.table td {
    border: none;
    padding: 0.75rem 0;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection