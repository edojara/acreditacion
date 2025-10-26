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

                                        <div class="form-group">
                                            <label for="status">Estado <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="activo" {{ old('status', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                <option value="suspendido" {{ old('status') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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

                        <!-- Información Adicional -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-globe mr-2"></i>
                                            Información Adicional
                                        </h5>
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