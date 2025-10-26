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

<!-- Modal para Crear Entidad Educativa -->
<div class="modal fade" id="createEntityModal" tabindex="-1" role="dialog" aria-labelledby="createEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEntityModalLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Nueva Entidad Educativa
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('educational-entities.store') }}" id="createEntityForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Información Básica
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="type">Tipo de Entidad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('type') is-invalid @enderror"
                                               id="type" name="type" value="{{ old('type') }}" required
                                               list="entity-types" autocomplete="off">
                                        <datalist id="entity-types">
                                            @foreach(\App\Models\EducationalEntity::distinct('type')->pluck('type')->filter() as $existingType)
                                                <option value="{{ $existingType }}">
                                            @endforeach
                                            <option value="Universidad">
                                            <option value="Instituto">
                                            <option value="Colegio">
                                            <option value="Centro Educativo">
                                            <option value="Otro">
                                        </datalist>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Escribe o selecciona un tipo de entidad existente</small>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Ubicación y Contacto -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Ubicación y Contacto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror"
                                                  id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email Institucional</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_name">Nombre del Contacto</label>
                                        <input type="text" class="form-control @error('contact_name') is-invalid @enderror"
                                               id="contact_name" name="contact_name" value="{{ old('contact_name') }}">
                                        @error('contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Persona de contacto principal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Entidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para manejar el modal -->
<script>
$(document).ready(function() {
    // Manejar envío del formulario
    $('#createEntityForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#createEntityModal').modal('hide');
                $('#createEntityForm')[0].reset();

                // Mostrar mensaje de éxito
                toastr.success('Entidad educativa creada exitosamente');

                // Recargar la página o actualizar la tabla
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Errores de validación
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = [];

                    for (let field in errors) {
                        errorMessages.push(errors[field][0]);
                    }

                    toastr.error(errorMessages.join('<br>'));
                } else {
                    toastr.error('Error al crear la entidad educativa');
                }
            }
        });
    });
});
</script>
@endsection