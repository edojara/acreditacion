@extends('layouts.app')

@section('title', 'Detalles de Entidad Educativa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Entidades Educativas</a></li>
    <li class="breadcrumb-item active">{{ $educationalEntity->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university mr-2"></i>
                        {{ $educationalEntity->name }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $educationalEntity->status === 'activo' ? 'success' : ($educationalEntity->status === 'inactivo' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($educationalEntity->status) }}
                        </span>
                        <span class="badge badge-info">{{ ucfirst($educationalEntity->type) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Código:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->code }}</dd>

                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">{{ ucfirst($educationalEntity->type) }}</dd>

                                <dt class="col-sm-4">Estado:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $educationalEntity->status === 'activo' ? 'success' : ($educationalEntity->status === 'inactivo' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($educationalEntity->status) }}
                                    </span>
                                </dd>

                                @if($educationalEntity->phone)
                                <dt class="col-sm-4">Teléfono:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->phone }}</dd>
                                @endif

                                @if($educationalEntity->email)
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">
                                    <a href="mailto:{{ $educationalEntity->email }}">{{ $educationalEntity->email }}</a>
                                </dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                @if($educationalEntity->address)
                                <dt class="col-sm-4">Dirección:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->address }}</dd>
                                @endif

                                @if($educationalEntity->city)
                                <dt class="col-sm-4">Ciudad:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->city }}</dd>
                                @endif

                                @if($educationalEntity->region)
                                <dt class="col-sm-4">Región:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->region }}</dd>
                                @endif

                                @if($educationalEntity->country)
                                <dt class="col-sm-4">País:</dt>
                                <dd class="col-sm-8">{{ $educationalEntity->country }}</dd>
                                @endif

                                @if($educationalEntity->website)
                                <dt class="col-sm-4">Sitio Web:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ $educationalEntity->website }}" target="_blank">{{ $educationalEntity->website }}</a>
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($educationalEntity->description)
                    <div class="mt-3">
                        <h5>Descripción</h5>
                        <p class="text-muted">{{ $educationalEntity->description }}</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('educational-entities.edit', $educationalEntity) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('educational-entities.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-lg-4">
            <!-- Contactos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book mr-2"></i>
                        Contactos ({{ $entity->contacts->count() }})
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addContactModal">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($educationalEntity->contacts->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($educationalEntity->contacts as $contact)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $contact->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $contact->position }}</small>
                                        @if($contact->email)
                                        <br>
                                        <small><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></small>
                                        @endif
                                        @if($contact->phone)
                                        <br>
                                        <small>{{ $contact->phone }}</small>
                                        @endif
                                    </div>
                                    <div>
                                        @if($contact->is_primary)
                                        <span class="badge badge-primary">Principal</span>
                                        @endif
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay contactos registrados</p>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addContactModal">
                                <i class="fas fa-plus"></i> Agregar Primer Contacto
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Estadísticas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted">Contactos</div>
                            <div class="h4">{{ $educationalEntity->contacts->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted">Creado</div>
                            <div class="h6">{{ $educationalEntity->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Contacto -->
<div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Agregar Contacto
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="{{ route('entity-contacts.store') }}" id="addContactForm">
                @csrf
                <input type="hidden" name="educational_entity_id" value="{{ $educationalEntity->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="contact_name">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="contact_name" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_position">Cargo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror"
                               id="contact_position" name="position" required>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="contact_email" name="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_phone">Teléfono</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="contact_phone" name="phone">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Marcar como contacto principal
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Agregar Contacto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Manejar envío del formulario de contacto
    $('#addContactForm').on('submit', function(e) {
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
                $('#addContactModal').modal('hide');
                $('#addContactForm')[0].reset();

                toastr.success('Contacto agregado exitosamente');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessages = [];

                    for (let field in errors) {
                        errorMessages.push(errors[field][0]);
                    }

                    toastr.error(errorMessages.join('<br>'));
                } else {
                    toastr.error('Error al agregar el contacto');
                }
            }
        });
    });
});
</script>
@endsection