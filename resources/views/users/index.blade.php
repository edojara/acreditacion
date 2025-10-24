@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Gestión de Usuarios
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Último Login</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'report' ? 'warning' : 'info') }}">
                                                {{ ucfirst($user->role->name) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->google_id === 'pending')
                                                <span class="badge badge-info">
                                                    <i class="fab fa-google"></i> Pendiente Google
                                                </span>
                                            @elseif($user->google_id)
                                                <span class="badge badge-success">
                                                    <i class="fab fa-google"></i> Google
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-key"></i> Local
                                                </span>
                                            @endif
                                            @if($user->must_change_password)
                                                <br><small class="text-warning">Debe cambiar contraseña</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="btn-group mt-1">
                                                <form action="{{ route('users.force-password-change', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-secondary btn-sm" title="Forzar cambio de contraseña">
                                                        <i class="fas fa-key"></i> Forzar Cambio
                                                    </button>
                                                </form>
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline ml-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-dark btn-sm" title="Resetear contraseña"
                                                            onclick="return confirm('¿Resetear contraseña a Abcd.1234?')">
                                                        <i class="fas fa-undo"></i> Reset
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> No hay usuarios registrados.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>
@endsection