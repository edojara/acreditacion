@extends('layouts.app')

@section('title', 'Detalles de Entidad Educativa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Entidades Educativas</a></li>
    <li class="breadcrumb-item active">{{ $educationalEntity->name }}</li>
@endsection

@section('scripts')
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

                // Actualizar contador de contactos en el header
                const currentCount = parseInt($('.card-title:contains("Contactos")').text().match(/\d+/)[0]);
                $('.card-title:contains("Contactos")').html('<i class="fas fa-address-book mr-2"></i> Contactos (' + (currentCount + 1) + ')');

                // Agregar el nuevo contacto a la lista sin recargar página
                if (response.contact) {
                    const contactHtml = `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${response.contact.name}</strong>
                                    <br>
                                    <small class="text-muted">${response.contact.position || ''}</small>
                                    ${response.contact.email ? '<br><small><a href="mailto:' + response.contact.email + '">' + response.contact.email + '</a></small>' : ''}
                                    ${response.contact.phone ? '<br><small>' + response.contact.phone + '</small>' : ''}
                                </div>
                                <div>
                                    ${response.contact.is_primary ? '<span class="badge badge-primary">Principal</span>' : ''}
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-contact-btn"
                                                title="Eliminar"
                                                data-contact-id="${response.contact.id}"
                                                data-contact-name="${response.contact.name}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;

                    if ($('.list-group-flush li').length > 0) {
                        $('.list-group-flush').append(contactHtml);
                    } else {
                        // Si no hay contactos, reemplazar el mensaje vacío
                        $('.card-body.p-0').html('<ul class="list-group list-group-flush">' + contactHtml + '</ul>');
                    }
                } else {
                    // Fallback: recargar página si no hay respuesta estructurada
                    location.reload();
                }
            },
            error: function(xhr) {
                console.log('Error en envío del formulario:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText);

                if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    console.log('Errores de validación:', response.errors);

                    if (response.errors) {
                        let errorMessages = [];

                        for (let field in response.errors) {
                            errorMessages.push(response.errors[field][0]);
                        }

                        // Mostrar errores en alert en lugar de toastr
                        alert('Errores de validación:\n' + errorMessages.join('\n'));
                    } else {
                        alert('Error de validación desconocido');
                    }
                } else {
                    alert('Error al agregar el contacto: ' + xhr.status + ' - ' + xhr.statusText);
                }
            }
        });
    });

    // Manejar eliminación de contactos con AJAX
    $(document).on('click', '.delete-contact-btn', function(e) {
        e.preventDefault();

        const contactId = $(this).data('contact-id');
        const contactName = $(this).data('contact-name');

        if (confirm('¿Está seguro de eliminar el contacto "' + contactName + '"?')) {
            $.ajax({
                url: '/entity-contacts/' + contactId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Contacto eliminado exitosamente');

                    // Actualizar contador de contactos en el header
                    const currentCount = parseInt($('.card-title:contains("Contactos")').text().match(/\d+/)[0]);
                    $('.card-title:contains("Contactos")').html('<i class="fas fa-address-book mr-2"></i> Contactos (' + (currentCount - 1) + ')');

                    // Remover el contacto de la lista
                    $('button[data-contact-id="' + contactId + '"]').closest('.list-group-item').remove();

                    // Si no quedan contactos, mostrar mensaje vacío
                    if ($('.list-group-flush li').length === 0) {
                        $('.card-body.p-0').html(`
                            <div class="text-center py-4">
                                <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay contactos registrados</p>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                                    <i class="fas fa-plus"></i> Agregar Primer Contacto
                                </button>
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    console.log('Error al eliminar contacto:', xhr);
                    alert('Error al eliminar el contacto: ' + xhr.status + ' - ' + xhr.statusText);
                }
            });
        }
    });

    // Sistema de edición modal (igual que en index.blade.php)
    const editModal = document.getElementById('editEntityModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            console.log('Modal de edición abierto desde show.blade.php');

            const button = event.relatedTarget;
            const entityId = button.getAttribute('data-entity-id');
            const entityName = button.getAttribute('data-entity-name');
            const entityType = button.getAttribute('data-entity-type');
            const entityAddress = button.getAttribute('data-entity-address');
            const entityCity = button.getAttribute('data-entity-city');
            const entityRegion = button.getAttribute('data-entity-region');
            const entityCountry = button.getAttribute('data-entity-country');
            const entityPhone = button.getAttribute('data-entity-phone');
            const entityEmail = button.getAttribute('data-entity-email');
            const entityWebsite = button.getAttribute('data-entity-website');

            console.log('Datos del botón:', {
                entityId, entityName, entityType, entityAddress, entityCity,
                entityRegion, entityCountry, entityPhone, entityEmail, entityWebsite
            });

            // Llenar el formulario después de un pequeño delay
            setTimeout(() => {
                const nameField = document.getElementById('edit_name');
                const typeField = document.getElementById('edit_type');
                const addressField = document.getElementById('edit_address');
                const cityField = document.getElementById('edit_city');
                const regionField = document.getElementById('edit_region');
                const countryField = document.getElementById('edit_country');
                const phoneField = document.getElementById('edit_phone');
                const emailField = document.getElementById('edit_email');
                const websiteField = document.getElementById('edit_website');

                if (nameField) nameField.value = entityName || '';
                if (typeField) typeField.value = entityType || '';
                if (addressField) addressField.value = entityAddress || '';
                if (cityField) cityField.value = entityCity || '';
                if (regionField) regionField.value = entityRegion || '';
                if (countryField) countryField.value = entityCountry || 'Chile';
                if (phoneField) phoneField.value = entityPhone || '';
                if (emailField) emailField.value = entityEmail || '';
                if (websiteField) websiteField.value = entityWebsite || '';

                // Actualizar action del formulario
                const editForm = document.getElementById('editEntityForm');
                if (editForm && entityId) {
                    editForm.action = `/educational-entities/${entityId}`;
                    console.log('Action del formulario actualizado:', editForm.action);
                }

                console.log('Campos llenados exitosamente - valores asignados');
            }, 200);
        });

        // Manejar envío del formulario de edición
        const editForm = document.getElementById('editEntityForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Formulario enviado - previniendo default');

                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                console.log('URL de acción:', actionUrl);
                console.log('Token CSRF:', csrfToken ? 'Presente' : 'Faltante');
                console.log('Datos del formulario:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ':', value);
                }

                fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Respuesta HTTP:', response.status, response.statusText);

                    if (response.ok) {
                        return response.text().then(text => {
                            console.log('Respuesta exitosa - contenido:', text.substring(0, 200) + '...');

                            if (text.includes('<!DOCTYPE html>')) {
                                console.log('✅ Respuesta HTML exitosa - redirección de Laravel');
                                return { success: true, html: text };
                            }

                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.log('Contenido no es JSON, pero respuesta OK - tratando como éxito');
                                return { success: true, content: text };
                            }
                        });
                    } else {
                        return response.text().then(text => {
                            console.log('Respuesta de error - contenido:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                throw new Error('Respuesta de error no es JSON válido: ' + text);
                            }
                        });
                    }
                })
                .then(data => {
                    console.log('Datos procesados:', data);

                    if (data.success || data.html) {
                        console.log('✅ Respuesta exitosa detectada');

                        const entityId = actionUrl.split('/').pop();
                        const editButton = document.querySelector(`button[data-entity-id="${entityId}"]`);

                        if (editButton) {
                            console.log('Actualizando atributos del botón para entidad:', entityId);

                            const formData = new FormData(editForm);
                            const updatedData = {
                                name: formData.get('name'),
                                type: formData.get('type'),
                                address: formData.get('address'),
                                city: formData.get('city'),
                                region: formData.get('region'),
                                country: formData.get('country'),
                                phone: formData.get('phone'),
                                email: formData.get('email'),
                                website: formData.get('website')
                            };

                            editButton.setAttribute('data-entity-name', updatedData.name || '');
                            editButton.setAttribute('data-entity-type', updatedData.type || '');
                            editButton.setAttribute('data-entity-address', updatedData.address || '');
                            editButton.setAttribute('data-entity-city', updatedData.city || '');
                            editButton.setAttribute('data-entity-region', updatedData.region || '');
                            editButton.setAttribute('data-entity-country', updatedData.country || 'Chile');
                            editButton.setAttribute('data-entity-phone', updatedData.phone || '');
                            editButton.setAttribute('data-entity-email', updatedData.email || '');
                            editButton.setAttribute('data-entity-website', updatedData.website || '');

                            console.log('Atributos del botón actualizados:', updatedData);
                        }

                        const modal = bootstrap.Modal.getInstance(editModal);
                        modal.hide();

                        editForm.reset();

                        alert('Entidad educativa actualizada exitosamente');

                        location.reload();
                    } else {
                        console.log('Respuesta JSON procesada:', data);
                    }
                })
                .catch(error => {
                    console.error('Error en fetch:', error);

                    console.error('Error completo:', error);
                    console.error('Mensaje de error:', error.message);

                    try {
                        const errorData = JSON.parse(error.message);
                        console.error('Datos de error parseados:', errorData);

                        if (errorData.errors) {
                            const errors = errorData.errors;
                            let errorMessages = [];

                            for (let field in errors) {
                                errorMessages.push(errors[field][0]);
                            }

                            alert('Errores de validación:\n' + errorMessages.join('\n'));
                        } else {
                            alert('Error al actualizar la entidad educativa: ' + (errorData.message || 'Error desconocido'));
                        }
                    } catch (parseError) {
                        console.error('Error al parsear respuesta de error:', parseError);
                        alert('Error al actualizar la entidad educativa: ' + error.message + '\n\nDetalles en consola del navegador (F12)');
                    }
                });
            });
        }
    }
});
</script>
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

                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editEntityModal"
                            data-entity-id="{{ $educationalEntity->id }}"
                            data-entity-name="{{ $educationalEntity->name }}"
                            data-entity-type="{{ $educationalEntity->type }}"
                            data-entity-address="{{ $educationalEntity->address }}"
                            data-entity-city="{{ $educationalEntity->city }}"
                            data-entity-region="{{ $educationalEntity->region }}"
                            data-entity-country="{{ $educationalEntity->country }}"
                            data-entity-phone="{{ $educationalEntity->phone }}"
                            data-entity-email="{{ $educationalEntity->email }}"
                            data-entity-website="{{ $educationalEntity->website }}">
                        <i class="fas fa-edit"></i> Editar
                    </button>
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
                        Contactos ({{ $educationalEntity->contacts->count() }})
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal">
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
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-contact-btn"
                                                    title="Eliminar"
                                                    data-contact-id="{{ $contact->id }}"
                                                    data-contact-name="{{ $contact->name }}">
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
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
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

<!-- Modal para Editar Entidad Educativa -->
<div class="modal fade" id="editEntityModal" tabindex="-1" aria-labelledby="editEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEntityModalLabel">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Entidad Educativa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" id="editEntityForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="edit_name" name="name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror"
                                        id="edit_type" name="type" required>
                                    <option value="universidad">Universidad</option>
                                    <option value="instituto">Instituto</option>
                                    <option value="colegio">Colegio</option>
                                    <option value="centro_educativo">Centro Educativo</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_address" class="form-label">Dirección</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                       id="edit_address" name="address">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_city" class="form-label">Ciudad</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       id="edit_city" name="city">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_region" class="form-label">Región</label>
                                <select class="form-control @error('region') is-invalid @enderror"
                                        id="edit_region" name="region">
                                    <option value="">Seleccionar región</option>
                                    <option value="Metropolitana">Metropolitana</option>
                                    <option value="Valparaíso">Valparaíso</option>
                                    <option value="Biobío">Biobío</option>
                                    <option value="Maule">Maule</option>
                                    <option value="Ñuble">Ñuble</option>
                                    <option value="Araucanía">Araucanía</option>
                                    <option value="Los Ríos">Los Ríos</option>
                                    <option value="Los Lagos">Los Lagos</option>
                                    <option value="Aysén">Aysén</option>
                                    <option value="Magallanes">Magallanes</option>
                                    <option value="Arica y Parinacota">Arica y Parinacota</option>
                                    <option value="Tarapacá">Tarapacá</option>
                                    <option value="Antofagasta">Antofagasta</option>
                                    <option value="Atacama">Atacama</option>
                                    <option value="Coquimbo">Coquimbo</option>
                                </select>
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_country" class="form-label">País</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="edit_country" name="country" value="Chile">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_phone" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="edit_phone" name="phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="edit_email" name="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="edit_website" class="form-label">Sitio Web</label>
                                <input type="text" class="form-control @error('website') is-invalid @enderror"
                                       id="edit_website" name="website" placeholder="Ej: www.lun.cl o cualquier texto">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Agregar Contacto -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Agregar Contacto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('entity-contacts.store') }}" id="addContactForm">
                @csrf
                <input type="hidden" name="educational_entity_id" value="{{ $educationalEntity->id }}">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="contact_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="contact_name" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="contact_position" class="form-label">Cargo</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror"
                               id="contact_position" name="position">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="contact_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="contact_email" name="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="contact_phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="contact_phone" name="phone">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="contact_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror"
                                id="contact_type" name="type" required>
                            <option value="principal">Principal</option>
                            <option value="academico">Académico</option>
                            <option value="administrativo">Administrativo</option>
                            <option value="tecnico">Técnico</option>
                            <option value="otro">Otro</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="contact_status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror"
                                id="contact_status" name="status" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Marcar como contacto principal
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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

@endsection