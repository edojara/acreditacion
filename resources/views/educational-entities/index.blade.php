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

                <!-- Tabla de Entidades Moderna -->
                <div class="card-body p-0">
                    @if($entities->count() > 0)
                    <div class="entities-grid">
                        @foreach($entities as $index => $entity)
                        <div class="entity-card {{ $index % 2 === 0 ? 'card-primary' : 'card-secondary' }}"
                             data-href="{{ route('educational-entities.show', $entity) }}"
                             title="Doble click para ver detalles">
                            <div class="card-header-custom">
                                <div class="entity-code">
                                    <code>{{ $entity->code }}</code>
                                </div>
                                <div class="entity-status">
                                    <span class="status-badge {{ $entity->status }}">
                                        {{ ucfirst($entity->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body-custom">
                                <div class="entity-name">
                                    <h5 class="mb-1">{{ $entity->name }}</h5>
                                    <span class="entity-type badge-modern">{{ ucfirst($entity->type) }}</span>
                                </div>

                                <div class="entity-details">
                                    <div class="detail-row">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $entity->city ?? 'No especificada' }}, {{ $entity->region ?? 'No especificada' }}</span>
                                    </div>

                                    @if($entity->phone)
                                    <div class="detail-row">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $entity->phone }}</span>
                                    </div>
                                    @endif

                                    @if($entity->email)
                                    <div class="detail-row">
                                        <i class="fas fa-envelope"></i>
                                        <span>{{ $entity->email }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="entity-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">{{ $entity->contacts_count ?? 0 }}</span>
                                        <span class="stat-label">Contactos</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-actions">
                                <a href="{{ route('educational-entities.show', $entity) }}"
                                   class="action-btn view-btn" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver</span>
                                </a>
                                <a href="{{ route('educational-entities.edit', $entity) }}"
                                   class="action-btn edit-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar</span>
                                </a>
                                <form method="POST" action="{{ route('educational-entities.destroy', $entity) }}"
                                      class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta entidad?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
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
    const filterSelects = document.querySelectorAll('#type, #status, #region');
    filterSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
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
/* Grid Layout Moderno */
.entities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
    padding: 20px;
}

.entity-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: none;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
}

.entity-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
}

.entity-card.card-primary {
    border-left: 4px solid #007bff;
}

.entity-card.card-secondary {
    border-left: 4px solid #28a745;
}

/* Header de la Card */
.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.entity-code code {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
    font-weight: bold;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75em;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.activo {
    background: #28a745;
    color: white;
}

.status-badge.inactivo {
    background: #6c757d;
    color: white;
}

.status-badge.suspendido {
    background: #ffc107;
    color: #212529;
}

/* Body de la Card */
.card-body-custom {
    padding: 20px;
}

.entity-name h5 {
    color: #2c3e50;
    margin-bottom: 8px;
    font-weight: 600;
}

.badge-modern {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75em;
    font-weight: 500;
}

.entity-details {
    margin: 16px 0;
}

.detail-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    color: #6c757d;
    font-size: 0.9em;
}

.detail-row i {
    margin-right: 8px;
    width: 16px;
    color: #007bff;
}

/* Estadísticas */
.entity-stats {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e9ecef;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5em;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    color: #6c757d;
    font-size: 0.8em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Acciones */
.card-actions {
    padding: 16px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 8px;
    border-top: 1px solid #e9ecef;
}

.action-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 4px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 0.8em;
    color: white;
}

.action-btn span {
    margin-top: 4px;
    font-size: 0.75em;
}

.action-btn.view-btn {
    background: #17a2b8;
}

.action-btn.view-btn:hover {
    background: #138496;
    transform: translateY(-2px);
}

.action-btn.edit-btn {
    background: #ffc107;
    color: #212529;
}

.action-btn.edit-btn:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

.action-btn.delete-btn {
    background: #dc3545;
}

.action-btn.delete-btn:hover {
    background: #c82333;
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .entities-grid {
        grid-template-columns: 1fr;
        padding: 15px;
        gap: 15px;
    }

    .entity-card {
        margin: 0;
    }

    .card-actions {
        flex-direction: row;
        padding: 12px 15px;
    }

    .action-btn {
        flex-direction: row;
        padding: 6px 8px;
    }

    .action-btn span {
        margin-top: 0;
        margin-left: 6px;
    }
}

@media (max-width: 480px) {
    .entities-grid {
        padding: 10px;
        gap: 10px;
    }

    .card-header-custom {
        padding: 12px 15px;
    }

    .card-body-custom {
        padding: 15px;
    }

    .entity-name h5 {
        font-size: 1.1em;
    }
}
</style>
@endsection