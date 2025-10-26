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
                            <label for="status" class="mr-2">Estado:</label>
                            <select name="status" id="status" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="activo" {{ request('status') === 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ request('status') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="suspendido" {{ request('status') === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
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
                            <input type="text" name="search" class="form-control form-control-sm"
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

                <!-- Tabla de Entidades -->
                <div class="card-body table-responsive p-0">
                    @if($entities->count() > 0)
                    <table class="table table-hover text-nowrap" id="entitiesTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Ciudad</th>
                                <th>Región</th>
                                <th>Estado</th>
                                <th>Contactos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities as $entity)
                            <tr class="entity-row" data-url="{{ route('educational-entities.show', $entity) }}" style="cursor: pointer;">
                                <td>
                                    <code>{{ $entity->code }}</code>
                                </td>
                                <td>
                                    <strong>{{ $entity->name }}</strong>
                                    @if($entity->phone)
                                    <br>
                                    <small class="text-muted">{{ $entity->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($entity->type) }}</span>
                                </td>
                                <td>{{ $entity->city ?? '-' }}</td>
                                <td>{{ $entity->region ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $entity->status === 'activo' ? 'success' : ($entity->status === 'inactivo' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($entity->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light">{{ $entity->contacts_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('educational-entities.show', $entity) }}"
                                           class="btn btn-info btn-sm" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('educational-entities.edit', $entity) }}"
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('educational-entities.destroy', $entity) }}"
                                              class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta entidad?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
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
                    {{ $entities->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Doble click en fila para ver detalles
    $('.entity-row').on('dblclick', function() {
        const url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

    // Tooltip para botones de acción
    $('[title]').tooltip();

    // Auto-submit de filtros al cambiar select
    $('#type, #status, #region').on('change', function() {
        $(this).closest('form').submit();
    });

    // Mejorar UX con hover en filas
    $('.entity-row').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // Mostrar mensaje de doble click
    $('.entity-row').first().attr('title', 'Doble click para ver detalles');
});
</script>

<style>
.entity-row:hover {
    background-color: #f8f9fa !important;
}

.table-hover .entity-row:hover {
    background-color: #e3f2fd !important;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection