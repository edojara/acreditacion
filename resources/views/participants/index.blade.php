@extends('layouts.app')

@section('title', 'Integrantes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item active">Integrantes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>
                        Gestión de Integrantes
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createParticipantModal">
                            <i class="fas fa-plus"></i> Nuevo Integrante
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('participants.index') }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="educational_entity_id" class="mr-2">Institución:</label>
                            <select name="educational_entity_id" id="educational_entity_id" class="form-control form-control-sm">
                                <option value="">Todas las instituciones</option>
                                @foreach($educationalEntities as $entity)
                                    <option value="{{ $entity->id }}" {{ request('educational_entity_id') == $entity->id ? 'selected' : '' }}>
                                        {{ $entity->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mr-3">
                            <input type="text" name="search" id="searchInput" class="form-control form-control-sm"
                                    placeholder="Buscar por nombre, cargo o teléfono..."
                                    value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn btn-secondary btn-sm mr-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>

                        @if(request()->hasAny(['educational_entity_id', 'search']))
                        <a href="{{ route('participants.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                        @endif
                    </form>
                </div>

                <!-- Tabla -->
                <div class="card-body table-responsive p-0">
                    @if($participants->count() > 0)
                    <table class="table table-bordered table-striped table-hover excel-table" id="participantsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th>
                                    <a href="{{ route('participants.index', array_merge(request()->query(), ['sort_by' => 'full_name', 'sort_direction' => (request('sort_by') === 'full_name' && request('sort_direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-white text-decoration-none sortable-column">
                                        Nombre Completo
                                        @if(request('sort_by') === 'full_name')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Cargo</th>
                                <th>Teléfono</th>
                                <th>
                                    <a href="{{ route('participants.index', array_merge(request()->query(), ['sort_by' => 'educational_entity_id', 'sort_direction' => (request('sort_by') === 'educational_entity_id' && request('sort_direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-white text-decoration-none sortable-column">
                                        Institución
                                        @if(request('sort_by') === 'educational_entity_id')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('participants.index', array_merge(request()->query(), ['sort_by' => 'registration_date', 'sort_direction' => (request('sort_by') === 'registration_date' && request('sort_direction') === 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-white text-decoration-none sortable-column">
                                        Fecha de Registro
                                        @if(request('sort_by') === 'registration_date')
                                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach($participants as $index => $participant)
                             <tr class="clickable-row" data-href="{{ route('participants.show', $participant) }}" style="cursor: pointer;">
                                 <td class="text-center">{{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $participant->full_name }}</td>
                                <td>{{ $participant->position ?? '-' }}</td>
                                <td>
                                    @if($participant->phone)
                                        <a href="tel:{{ $participant->phone }}" class="text-decoration-none">{{ $participant->formatted_phone }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $participant->educationalEntity->name ?? '-' }}</td>
                                <td>{{ $participant->registration_date ? $participant->registration_date->format('d/m/Y') : '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('participants.show', $participant) }}"
                                           class="btn btn-outline-info btn-sm" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-warning btn-sm edit-participant-btn"
                                                data-bs-toggle="modal" data-bs-target="#editParticipantModal"
                                                data-participant-id="{{ $participant->id }}"
                                                data-participant-full-name="{{ $participant->full_name }}"
                                                data-participant-phone="{{ $participant->phone }}"
                                                data-participant-position="{{ $participant->position }}"
                                                data-participant-educational-entity-id="{{ $participant->educational_entity_id }}"
                                                data-participant-registration-date="{{ $participant->registration_date ? $participant->registration_date->format('Y-m-d') : '' }}"
                                                title="Editar"
                                                onclick="console.log('Botón editar clickeado para participante:', {{ $participant->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('participants.destroy', $participant) }}"
                                              class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este integrante?')">
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
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4>No hay integrantes registrados</h4>
                        <p class="text-muted">Comience creando el primer integrante del sistema.</p>
                        <a href="{{ route('participants.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Integrante
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label for="perPageSelect" class="mr-2 mb-0">Mostrar:</label>
                                <select id="perPageSelect" class="form-control form-control-lg" style="width: auto;">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Todos</option>
                                </select>
                                <span class="ml-2 text-muted">
                                    Mostrando {{ $participants->firstItem() ?? 0 }} a {{ $participants->lastItem() ?? 0 }} de {{ $participants->total() }} registros
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(request('per_page') !== 'all' && $participants->hasPages())
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-lg justify-content-end mb-0">
                                    {{-- Botón Anterior --}}
                                    @if ($participants->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">Anterior</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $participants->appends(request()->query())->previousPageUrl() }}">Anterior</a>
                                        </li>
                                    @endif

                                    {{-- Páginas --}}
                                    @foreach($participants->getUrlRange(1, $participants->lastPage()) as $page => $url)
                                        <li class="page-item {{ $page == $participants->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Botón Siguiente --}}
                                    @if ($participants->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $participants->appends(request()->query())->nextPageUrl() }}">Siguiente</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">Siguiente</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear Nuevo Integrante -->
<div class="modal fade" id="createParticipantModal" tabindex="-1" role="dialog" aria-labelledby="createParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createParticipantModalLabel">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Nuevo Integrante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('participants.store') }}" id="createParticipantForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_full_name">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="create_full_name" name="full_name" value="{{ old('full_name') }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="create_phone">Teléfono</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="create_phone" name="phone" value="{{ old('phone') }}" placeholder="+56 9 XXXX XXXX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_position">Cargo</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                       id="create_position" name="position" value="{{ old('position') }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="create_educational_entity_id">Institución Educativa <span class="text-danger">*</span></label>
                                <select class="form-control @error('educational_entity_id') is-invalid @enderror"
                                        id="create_educational_entity_id" name="educational_entity_id" required>
                                    <option value="">Seleccionar institución...</option>
                                    @foreach($educationalEntities as $entity)
                                        <option value="{{ $entity->id }}" {{ old('educational_entity_id') == $entity->id ? 'selected' : '' }}>
                                            {{ $entity->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('educational_entity_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="create_registration_date">Fecha de Registro</label>
                                <input type="date" class="form-control @error('registration_date') is-invalid @enderror"
                                       id="create_registration_date" name="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}">
                                @error('registration_date')
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
                        <i class="fas fa-save"></i> Crear Integrante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Integrante -->
<div class="modal fade" id="editParticipantModal" tabindex="-1" role="dialog" aria-labelledby="editParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editParticipantModalLabel">
                    <i class="fas fa-edit mr-2"></i>
                    Editar Integrante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="" id="editParticipantForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_full_name">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="edit_full_name" name="full_name" value="" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_phone">Teléfono</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="edit_phone" name="phone" value="" placeholder="+56 9 XXXX XXXX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_position">Cargo</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                       id="edit_position" name="position" value="">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_educational_entity_id">Institución Educativa <span class="text-danger">*</span></label>
                                <select class="form-control @error('educational_entity_id') is-invalid @enderror"
                                        id="edit_educational_entity_id" name="educational_entity_id" required>
                                    <option value="">Seleccionar institución...</option>
                                    @foreach($educationalEntities as $entity)
                                        <option value="{{ $entity->id }}">
                                            {{ $entity->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('educational_entity_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_registration_date">Fecha de Registro</label>
                                <input type="date" class="form-control @error('registration_date') is-invalid @enderror"
                                       id="edit_registration_date" name="registration_date" value="">
                                @error('registration_date')
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
                        <i class="fas fa-save"></i> Actualizar Integrante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de selección de filas
    const clickableRows = document.querySelectorAll('.clickable-row');
    clickableRows.forEach(function(row) {
        let selectedRow = null;

        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn')) {
                if (selectedRow && selectedRow !== this) {
                    selectedRow.classList.remove('table-primary', 'selected-row');
                    selectedRow.style.backgroundColor = '';
                }

                if (selectedRow === this) {
                    this.classList.remove('table-primary', 'selected-row');
                    this.style.backgroundColor = '';
                    selectedRow = null;
                } else {
                    this.classList.add('table-primary', 'selected-row');
                    this.style.backgroundColor = '#cce5ff';
                    selectedRow = this;
                }
            }
        });

        row.addEventListener('dblclick', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-href');
            if (url) {
                window.location.href = url;
            }
        });

        row.addEventListener('mouseenter', function() {
            if (this !== selectedRow) {
                this.classList.add('table-active');
            }
        });

        row.addEventListener('mouseleave', function() {
            if (this !== selectedRow) {
                this.classList.remove('table-active');
            }
        });
    });

    // Selector de cantidad de registros por página
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const form = this.closest('form');
            const url = new URL(window.location);

            if (this.value === 'all') {
                url.searchParams.set('per_page', 'all');
            } else {
                url.searchParams.set('per_page', this.value);
            }

            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }

    // Búsqueda en tiempo real
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    }

    // Modal de edición
    const editModal = document.getElementById('editParticipantModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const participantId = button.getAttribute('data-participant-id');
            const fullName = button.getAttribute('data-participant-full-name') || '';
            const phone = button.getAttribute('data-participant-phone') || '';
            const position = button.getAttribute('data-participant-position') || '';
            const educationalEntityId = button.getAttribute('data-participant-educational-entity-id') || '';
            const registrationDate = button.getAttribute('data-participant-registration-date') || '';

            const modal = this;
            modal.querySelector('#editParticipantModalLabel').textContent = 'Editar Integrante: ' + fullName;
            modal.querySelector('#editParticipantForm').setAttribute('action', '/participants/' + participantId);

            setTimeout(function() {
                const nameField = modal.querySelector('#edit_full_name');
                const phoneField = modal.querySelector('#edit_phone');
                const positionField = modal.querySelector('#edit_position');
                const entityField = modal.querySelector('#edit_educational_entity_id');
                const dateField = modal.querySelector('#edit_registration_date');

                if (nameField) nameField.value = fullName;
                if (phoneField) phoneField.value = phone;
                if (positionField) positionField.value = position;
                if (entityField) entityField.value = educationalEntityId;
                if (dateField) dateField.value = registrationDate;
            }, 200);
        });
    }
});
</script>

<style>
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

.excel-table .text-center {
    text-align: center;
}

.excel-table .btn-group .btn {
    margin: 0 1px;
    border-radius: 3px !important;
    padding: 4px 8px;
}

.excel-table .btn-group .btn i {
    font-size: 0.8em;
}

.sortable-column {
    display: inline-flex;
    align-items: center;
    transition: opacity 0.2s ease;
}

.sortable-column:hover {
    opacity: 0.8;
}

.sortable-column i {
    font-size: 0.8em;
    margin-left: 0.25rem;
}

.selected-row {
    background-color: #cce5ff !important;
    border-left: 3px solid #007bff;
}

.selected-row:hover {
    background-color: #b3d7ff !important;
}

.table-primary.selected-row {
    background-color: #cce5ff !important;
}

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

@media (max-width: 576px) {
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .excel-table {
        min-width: 800px;
    }
}

.pagination {
    margin-bottom: 0;
    font-size: 0.875rem;
    opacity: 1;
}
.pagination .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25;
    border-radius: 0.375rem;
    min-width: auto;
    border-width: 1px;
    color: #495057;
    font-weight: 500;
}
.pagination .page-item {
    margin: 0 0.125rem;
}
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    font-weight: 600;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.25);
}
.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #adb5bd;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endsection