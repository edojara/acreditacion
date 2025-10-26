@extends('layouts.app')

@section('title', 'Entidades Educativas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Entidades Educativas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university mr-2"></i>
                        Gestión de Entidades Educativas
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createEntityModal">
                            <i class="fas fa-plus"></i> Nueva Entidad
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="universidad" {{ request('type') == 'universidad' ? 'selected' : '' }}>Universidad</option>
                                    <option value="instituto" {{ request('type') == 'instituto' ? 'selected' : '' }}>Instituto</option>
                                    <option value="colegio" {{ request('type') == 'colegio' ? 'selected' : '' }}>Colegio</option>
                                    <option value="centro_educativo" {{ request('type') == 'centro_educativo' ? 'selected' : '' }}>Centro Educativo</option>
                                    <option value="otro" {{ request('type') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ request('status') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, código o ciudad" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-secondary btn-block">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Ciudad</th>
                                    <th>Estado</th>
                                    <th>Contactos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entities as $entity)
                                <tr>
                                    <td>{{ $entity->code }}</td>
                                    <td>
                                        <strong>{{ $entity->name }}</strong>
                                        @if($entity->email)
                                        <br><small class="text-muted">{{ $entity->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $entity->type)) }}</span>
                                    </td>
                                    <td>{{ $entity->city }}</td>
                                    <td>
                                        @if($entity->status == 'activo')
                                            <span class="badge badge-success">Activo</span>
                                        @elseif($entity->status == 'inactivo')
                                            <span class="badge badge-secondary">Inactivo</span>
                                        @else
                                            <span class="badge badge-warning">Suspendido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $entity->contact_count }}</span>
                                        @if($entity->primaryContact)
                                            <br><small class="text-muted">{{ $entity->primaryContact->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('educational-entities.show', $entity) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('educational-entities.edit', $entity) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('entity-contacts.create', ['educational_entity_id' => $entity->id]) }}" class="btn btn-success btn-sm" title="Agregar Contacto">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-university fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay entidades educativas registradas.</p>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createEntityModal">
                                                <i class="fas fa-plus"></i> Crear Primera Entidad
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($entities->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $entities->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el modal de creación -->
@include('educational-entities.create')
@endsection