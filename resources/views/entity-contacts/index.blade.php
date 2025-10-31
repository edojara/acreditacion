@extends('layouts.app')

@section('title', 'Contactos de Entidades')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Entidades</a></li>
    <li class="breadcrumb-item active">Contactos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book mr-2"></i>
                        Gestión de Contactos de Entidades
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('entity-contacts.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Contacto
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="educational_entity_id" class="form-control">
                                    <option value="">Todas las entidades</option>
                                    @foreach($entities as $entity)
                                        <option value="{{ $entity->id }}" {{ request('educational_entity_id') == $entity->id ? 'selected' : '' }}>
                                            {{ $entity->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filtros de tipo y estado eliminados -->
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o email" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('entity-contacts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Entidad Educativa</th>
                                    <th>Cargo</th>
                                    <th>Contacto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                <tr>
                                    <td>
                                        <strong>{{ $contact->name }}</strong>
                                        @if($contact->is_primary)
                                            <br><small class="badge badge-primary">CONTACTO PRINCIPAL</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('educational-entities.show', $contact->educational_entity_id) }}">
                                            {{ $contact->educationalEntity->name }}
                                        </a>
                                        <br><small class="text-muted">{{ $contact->educationalEntity->code }}</small>
                                    </td>
                                    <td>{{ $contact->position ?: '-' }}</td>
                                    <td>
                                        @if($contact->email)
                                            <i class="fas fa-envelope text-muted"></i> {{ $contact->email }}<br>
                                        @endif
                                        @if($contact->preferred_phone)
                                            <i class="fas fa-phone text-muted"></i> {{ $contact->preferred_phone }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('entity-contacts.show', $contact) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('entity-contacts.edit', $contact) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No hay contactos registrados.</p>
                                            <a href="{{ route('entity-contacts.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Crear Primer Contacto
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($contacts->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $contacts->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection