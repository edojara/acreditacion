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
                        <a href="{{ route('educational-entities.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Entidad
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('educational-entities.index') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="type" class="mr-2">Tipo:</label>
                            <select name="type" id="type" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="universidad" {{ request('type') === 'universidad' ? 'selected' : '' }}>Universidad</option>
                                <option value="instituto" {{ request('type') === 'instituto' ? 'selected' : '' }}>Instituto</option>
                                <option value="colegio" {{ request('type') === 'colegio' ? 'selected' : '' }}>Colegio</option>
                                <option value="centro_educativo" {{ request('type') === 'centro_educativo' ? 'selected' : '' }}>Centro Educativo</option>
                                <option value="otro" {{ request('type') === 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>


                        <div class="form-group mr-3">
                            <label for="region" class="mr-2">Región:</label>
                            <select name="region" id="region" class="form-control form-control-sm">
                                <option value="">Todas</option>
                                <option value="Metropolitana" {{ request('region') === 'Metropolitana' ? 'selected' : '' }}>Metropolitana</option>
                                <option value="Valparaíso" {{ request('region') === 'Valparaíso' ? 'selected' : '' }}>Valparaíso</option>
                                <option value="Biobío" {{ request('region') === 'Biobío' ? 'selected' : '' }}>Biobío</option>
                                <option value="Maule" {{ request('region') === 'Maule' ? 'selected' : '' }}>Maule</option>
                                <option value="Ñuble" {{ request('region') === 'Ñuble' ? 'selected' : '' }}>Ñuble</option>
                                <option value="Araucanía" {{ request('region') === 'Araucanía' ? 'selected' : '' }}>Araucanía</option>
                                <option value="Los Ríos" {{ request('region') === 'Los Ríos' ? 'selected' : '' }}>Los Ríos</option>
                                <option value="Los Lagos" {{ request('region') === 'Los Lagos' ? 'selected' : '' }}>Los Lagos</option>
                                <option value="Aysén" {{ request('region') === 'Aysén' ? 'selected' : '' }}>Aysén</option>
                                <option value="Magallanes" {{ request('region') === 'Magallanes' ? 'selected' : '' }}>Magallanes</option>
                                <option value="Arica y Parinacota" {{ request('region') === 'Arica y Parinacota' ? 'selected' : '' }}>Arica y Parinacota</option>
                                <option value="Tarapacá" {{ request('region') === 'Tarapacá' ? 'selected' : '' }}>Tarapacá</option>
                                <option value="Antofagasta" {{ request('region') === 'Antofagasta' ? 'selected' : '' }}>Antofagasta</option>
                                <option value="Atacama" {{ request('region') === 'Atacama' ? 'selected' : '' }}>Atacama</option>
                                <option value="Coquimbo" {{ request('region') === 'Coquimbo' ? 'selected' : '' }}>Coquimbo</option>
                            </select>
                        </div>

                        <div class="form-group mr-3">
                            <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                    placeholder="Buscar por nombre o código..."
                                    value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn btn-secondary btn-sm mr-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>

                        @if(request()->hasAny(['type', 'status', 'region', 'search']))
                        <a href="{{ route('educational-entities.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        @endif
                    </form>
                </div>

                <!-- Tabla Estilo Excel -->
                <div class="card-body table-responsive p-0">
                    @if($entities->count() > 0)
                    <table class="table table-bordered table-striped table-hover excel-table" id="entitiesTable">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Ciudad</th>
                                <th>Región</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Contactos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $index => $entity)
                            <tr class="clickable-row" data-href="{{ route('educational-entities.show', $entity) }}" style="cursor: pointer;">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $entity->code }}</code>
                                </td>
                                <td class="font-weight-bold">{{ $entity->name }}</td>
                                <td>{{ ucfirst($entity->type) }}</td>
                                <td>{{ $entity->city ?? '-' }}</td>
                                <td>{{ $entity->region ?? '-' }}</td>
                                <td>{{ $entity->phone ?? '-' }}</td>
                                <td>
                                    @if($entity->email)
                                        <a href="mailto:{{ $entity->email }}" class="text-decoration-none">{{ $entity->email }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $entity->contacts_count ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('educational-entities.show', $entity) }}"
                                           class="btn btn-outline-info btn-sm" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-warning btn-sm edit-entity-btn"
                                                data-bs-toggle="modal" data-bs-target="#editEntityModal"
                                                data-entity-id="{{ $entity->id }}"
                                                data-entity-name="{{ $entity->name }}"
                                                data-entity-type="{{ $entity->type }}"
                                                data-entity-address="{{ $entity->address }}"
                                                data-entity-city="{{ $entity->city }}"
                                                data-entity-region="{{ $entity->region }}"
                                                data-entity-country="{{ $entity->country }}"
                                                data-entity-phone="{{ $entity->phone }}"
                                                data-entity-email="{{ $entity->email }}"
                                                data-entity-website="{{ $entity->website }}"
                                                title="Editar"
                                                onclick="console.log('Botón editar clickeado para entidad:', {{ $entity->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('educational-entities.destroy', $entity) }}"
                                              class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta entidad?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-university fa-4x text-muted mb-3"></i>
                        <h4>No hay entidades educativas registradas</h4>
                        <p class="text-muted">Comience creando la primera entidad educativa del sistema.</p>
                        <a href="{{ route('educational-entities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Entidad
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Paginación -->
                @if($entities->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        @if($entities->onFirstPage())
                            <span class="btn btn-outline-secondary btn-sm disabled">Anterior</span>
                        @else
                            <a href="{{ $entities->appends(request()->query())->previousPageUrl() }}" class="btn btn-outline-primary btn-sm">Anterior</a>
                        @endif

                        <span class="mx-2 align-self-center text-muted">
                            Página {{ $entities->currentPage() }} de {{ $entities->lastPage() }}
                        </span>

                        @if($entities->hasMorePages())
                            <a href="{{ $entities->appends(request()->query())->nextPageUrl() }}" class="btn btn-outline-primary btn-sm">Siguiente</a>
                        @else
                            <span class="btn btn-outline-secondary btn-sm disabled">Siguiente</span>
                        @endif
                    </div>
                    <small class="text-muted d-block text-center mt-2">
                        @if($entities->total() > 0)
                            Mostrando {{ $entities->firstItem() }}-{{ $entities->lastItem() }} de {{ $entities->total() }} entidades
                        @else
                            No hay entidades para mostrar
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: mostrar que el script se cargó
    console.log('Script de doble click cargado - Versión 3 (Vanilla JS)');

    // Verificar que las filas existen
    const clickableRows = document.querySelectorAll('.clickable-row');
    console.log('Encontradas', clickableRows.length, 'filas clickables');

    // Doble click en fila para ver detalles
    clickableRows.forEach(function(row) {
        row.addEventListener('dblclick', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const url = this.getAttribute('data-href');
            console.log('Doble click detectado en fila:', this);
            console.log('URL destino:', url);

            if (url) {
                console.log('Navegando a:', url);
                window.location.href = url;
            } else {
                console.error('No se encontró URL en data-href');
            }
        });

        // Click simple para feedback visual
        row.addEventListener('click', function(e) {
            // Solo si no se hizo click en botones de acción
            if (!e.target.closest('.btn')) {
                console.log('Click simple en fila');
                this.style.backgroundColor = '#e3f2fd';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 150);
            }
        });

        // Hover effects
        row.addEventListener('mouseenter', function() {
            this.classList.add('table-active');
        });

        row.addEventListener('mouseleave', function() {
            this.classList.remove('table-active');
        });
    });

    // Auto-submit de filtros al cambiar select (usando vanilla JS)
    const filterSelects = document.querySelectorAll('#type, #region');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Búsqueda en tiempo real con debounce
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Guardar estado inicial
        searchInput.addEventListener('focus', function() {
            sessionStorage.setItem('searchInputFocused', 'true');
        });

        searchInput.addEventListener('blur', function() {
            sessionStorage.setItem('searchInputFocused', 'false');
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const inputElement = this; // Guardar referencia al elemento
            searchTimeout = setTimeout(() => {
                // Guardar estado antes de enviar
                sessionStorage.setItem('searchInputValue', inputElement.value);
                sessionStorage.setItem('searchInputFocused', 'true');
                sessionStorage.setItem('searchTimestamp', Date.now());

                // Enviar el formulario
                inputElement.closest('form').submit();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }

    // Restaurar foco y estado después de cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const wasFocused = sessionStorage.getItem('searchInputFocused') === 'true';
        const savedValue = sessionStorage.getItem('searchInputValue');
        const timestamp = sessionStorage.getItem('searchTimestamp');
        const now = Date.now();

        // Solo restaurar si es reciente (últimos 5 segundos)
        if (searchInput && wasFocused && timestamp && (now - parseInt(timestamp)) < 5000) {
            // Restaurar valor si existe
            if (savedValue !== null) {
                searchInput.value = savedValue;
            }

            // Pequeño delay para asegurar que el DOM esté listo
            setTimeout(() => {
                searchInput.focus();
                // Mover cursor al final del texto
                const len = searchInput.value.length;
                searchInput.setSelectionRange(len, len);
            }, 100);
        }

        // Limpiar sessionStorage después de usar
        sessionStorage.removeItem('searchInputFocused');
        sessionStorage.removeItem('searchInputValue');
        sessionStorage.removeItem('searchTimestamp');
    });

    // Test: mostrar URLs de las primeras filas
    clickableRows.forEach(function(row, index) {
        if (index < 3) { // Solo las primeras 3
            console.log('Fila', index + 1, '- URL:', row.getAttribute('data-href'));
        }
    });

    // Verificar que Bootstrap tooltips funcionen (si jQuery está disponible)
    if (typeof $ !== 'undefined') {
        $('[title]').tooltip();
    }
});
</script>

<style>
/* Estilo Excel-like Table */
.excel-table {
    font-size: 0.875em;
    border-collapse: collapse;
}

.excel-table th {
    background: #343a40 !important;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 8px;
    border: 1px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 10;
}

.excel-table td {
    padding: 8px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
}

.excel-table tbody tr:nth-child(odd) {
    background-color: #f8f9fa;
}

.excel-table tbody tr:nth-child(even) {
    background-color: #ffffff;
}

.excel-table tbody tr:hover {
    background-color: #e3f2fd !important;
    cursor: pointer;
}

/* Celdas especiales */
.excel-table .text-center {
    text-align: center;
}

.excel-table code {
    font-family: 'Courier New', monospace;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 3px;
}

/* Botones de acción */
.excel-table .btn-group .btn {
    margin: 0 1px;
    border-radius: 3px !important;
    padding: 4px 8px;
}

.excel-table .btn-group .btn i {
    font-size: 0.8em;
}

/* Badges */
.excel-table .badge {
    font-size: 0.75em;
    padding: 4px 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .excel-table {
        font-size: 0.75em;
    }

    .excel-table th,
    .excel-table td {
        padding: 4px;
    }

    .excel-table .btn-group {
        flex-direction: column;
    }

    .excel-table .btn-group .btn {
        margin: 1px 0;
    }
}

/* Scroll horizontal en móviles */
@media (max-width: 576px) {
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .excel-table {
        min-width: 800px;
    }
}

/* Paginación minimalista - prácticamente invisible */
.pagination {
    margin-bottom: 0;
    font-size: 0.6rem;
    transform: scale(0.6);
    transform-origin: center;
    opacity: 0.7;
}
.pagination .page-link {
    padding: 0.15rem 0.3rem;
    font-size: 0.55rem;
    line-height: 1;
    border-radius: 0.1rem;
    min-width: auto;
    border-width: 1px;
    color: #6c757d;
}
.pagination .page-item {
    margin: 0 0.3px;
}
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    font-weight: 600;
}
.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
}
</style>

<!-- Modal para Editar Entidad Educativa -->
<div class="modal fade" id="editEntityModal" tabindex="-1" role="dialog" aria-labelledby="editEntityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEntityModalLabel">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Entidad Educativa
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="POST" action="" id="editEntityForm">
                @csrf
                @method('PUT')
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
                                            <label for="edit_name">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="edit_name" name="name" value="" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="edit_type">Tipo de Entidad <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('type') is-invalid @enderror"
                                                   id="edit_type" name="type" list="editTypeList" required
                                                   placeholder="Escribe o selecciona un tipo...">
                                            <datalist id="editTypeList">
                                                @foreach($existingTypes ?? [] as $type)
                                                    <option value="{{ $type }}">
                                                @endforeach
                                            </datalist>
                                            @error('type')
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
                                            <label for="edit_address">Dirección</label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                   id="edit_address" name="address" value="">
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_city">Ciudad</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                   id="edit_city" name="city" value="">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_region">Región</label>
                                            <select class="form-control @error('region') is-invalid @enderror" id="edit_region" name="region">
                                                <option value="">Seleccionar región...</option>
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

                                        <div class="form-group">
                                            <label for="edit_country">País</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                                   id="edit_country" name="country" value="Chile">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_phone">Teléfono</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   id="edit_phone" name="phone" value="">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="edit_email" name="email" value="">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit_website">Sitio Web</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="edit_website" name="website" value=""
                                                   placeholder="https://ejemplo.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                        <i class="fas fa-save"></i> Actualizar Entidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para manejar el modal de edición -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script del modal de edición cargado');

    // Verificar que el modal existe
    var editModal = document.getElementById('editEntityModal');
    console.log('Modal encontrado:', !!editModal);

    if (!editModal) {
        console.error('Modal de edición no encontrado!');
        return;
    }

    // Manejar apertura del modal de edición
    editModal.addEventListener('show.bs.modal', function (event) {
        console.log('✅ Evento show.bs.modal disparado correctamente!');
        console.log('Evento completo:', event);
        console.log('Related target:', event.relatedTarget);
        console.log('Evento show.bs.modal disparado');

        var button = event.relatedTarget;
        console.log('Botón relacionado:', button);

        if (!button) {
            console.error('No se encontró el botón relacionado');
            return;
        }

        var entityId = button.getAttribute('data-entity-id');
        var entityName = button.getAttribute('data-entity-name') || '';
        var entityType = button.getAttribute('data-entity-type') || '';
        var entityAddress = button.getAttribute('data-entity-address') || '';
        var entityCity = button.getAttribute('data-entity-city') || '';
        var entityRegion = button.getAttribute('data-entity-region') || '';
        var entityCountry = button.getAttribute('data-entity-country') || 'Chile';
        var entityPhone = button.getAttribute('data-entity-phone') || '';
        var entityEmail = button.getAttribute('data-entity-email') || '';
        var entityWebsite = button.getAttribute('data-entity-website') || '';

        console.log('Abriendo modal de edición para entidad:', entityId, entityName);
        console.log('Datos del botón:', {
            id: entityId,
            name: entityName,
            type: entityType,
            address: entityAddress,
            city: entityCity,
            region: entityRegion,
            country: entityCountry,
            phone: entityPhone,
            email: entityEmail,
            website: entityWebsite
        });

        var modal = this;
        modal.querySelector('#editEntityModalLabel').textContent = 'Editar Entidad Educativa: ' + entityName;
        modal.querySelector('#editEntityForm').setAttribute('action', '/educational-entities/' + entityId);

        // Llenar los campos del formulario con timeout para asegurar que el modal esté completamente abierto
        setTimeout(function() {
            console.log('Ejecutando setTimeout para llenar campos');

            // Verificar que los elementos existen antes de asignar valores
            const nameField = modal.querySelector('#edit_name');
            const typeField = modal.querySelector('#edit_type');
            const addressField = modal.querySelector('#edit_address');
            const cityField = modal.querySelector('#edit_city');
            const regionField = modal.querySelector('#edit_region');
            const countryField = modal.querySelector('#edit_country');
            const phoneField = modal.querySelector('#edit_phone');
            const emailField = modal.querySelector('#edit_email');
            const websiteField = modal.querySelector('#edit_website');

            console.log('Elementos encontrados:', {
                nameField: !!nameField,
                typeField: !!typeField,
                addressField: !!addressField,
                cityField: !!cityField,
                regionField: !!regionField,
                countryField: !!countryField,
                phoneField: !!phoneField,
                emailField: !!emailField,
                websiteField: !!websiteField
            });

            if (nameField) nameField.value = entityName;
            if (typeField) typeField.value = entityType;
            if (addressField) addressField.value = entityAddress;
            if (cityField) cityField.value = entityCity;
            if (regionField) regionField.value = entityRegion;
            if (countryField) countryField.value = entityCountry;
            if (phoneField) phoneField.value = entityPhone;
            if (emailField) emailField.value = entityEmail;
            if (websiteField) websiteField.value = entityWebsite;

            console.log('Campos llenados exitosamente - valores asignados');
        }, 200); // Aumentar timeout a 200ms
    });

    // Manejar envío del formulario de edición
    var editForm = document.getElementById('editEntityForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(errorData => {
                    throw new Error(JSON.stringify(errorData));
                });
            }
        })
        .then(data => {
            // Cerrar modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('editEntityModal'));
            modal.hide();

            // Resetear formulario
            editForm.reset();

            // Mostrar mensaje de éxito (usando vanilla JS alert por ahora)
            alert('Entidad educativa actualizada exitosamente');

            // Recargar la página para mostrar los cambios
            location.reload();
        })
        .catch(error => {
            try {
                const errorData = JSON.parse(error.message);
                if (errorData.errors) {
                    // Errores de validación
                    const errors = errorData.errors;
                    let errorMessages = [];

                    for (let field in errors) {
                        errorMessages.push(errors[field][0]);
                    }

                    alert('Errores de validación:\n' + errorMessages.join('\n'));
                } else {
                    alert('Error al actualizar la entidad educativa');
                }
            } catch (e) {
                alert('Error al actualizar la entidad educativa');
            }
        });
    });
});
</script>
@endsection