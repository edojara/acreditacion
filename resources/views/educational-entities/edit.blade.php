@extends('layouts.app')

@section('title', 'Editar Entidad Educativa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Entidades Educativas</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Entidad Educativa
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('educational-entities.show', $educationalEntity) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <a href="{{ route('educational-entities.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('educational-entities.update', $educationalEntity) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
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
                                        <!-- Nombre -->
                                        <div class="form-group">
                                            <label for="name" class="required">Nombre de la Entidad</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name', $educationalEntity->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Código -->
                                        <div class="form-group">
                                            <label for="code" class="required">Código</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code', $educationalEntity->code) }}" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tipo -->
                                        <div class="form-group">
                                            <label for="type" class="required">Tipo de Entidad</label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="universidad" {{ old('type', $educationalEntity->type) === 'universidad' ? 'selected' : '' }}>Universidad</option>
                                                <option value="instituto" {{ old('type', $educationalEntity->type) === 'instituto' ? 'selected' : '' }}>Instituto</option>
                                                <option value="colegio" {{ old('type', $educationalEntity->type) === 'colegio' ? 'selected' : '' }}>Colegio</option>
                                                <option value="centro_educativo" {{ old('type', $educationalEntity->type) === 'centro_educativo' ? 'selected' : '' }}>Centro Educativo</option>
                                                <option value="otro" {{ old('type', $educationalEntity->type) === 'otro' ? 'selected' : '' }}>Otro</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Estado -->
                                        <div class="form-group">
                                            <label for="status" class="required">Estado</label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="">Seleccionar estado...</option>
                                                <option value="activo" {{ old('status', $educationalEntity->status) === 'activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="inactivo" {{ old('status', $educationalEntity->status) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                <option value="suspendido" {{ old('status', $educationalEntity->status) === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
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
                                        <!-- Dirección -->
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                   id="address" name="address" value="{{ old('address', $educationalEntity->address) }}">
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Ciudad -->
                                        <div class="form-group">
                                            <label for="city">Ciudad</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                   id="city" name="city" value="{{ old('city', $educationalEntity->city) }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Región -->
                                        <div class="form-group">
                                            <label for="region">Región</label>
                                            <select class="form-control @error('region') is-invalid @enderror" id="region" name="region">
                                                <option value="">Seleccionar región...</option>
                                                <option value="Metropolitana" {{ old('region', $educationalEntity->region) === 'Metropolitana' ? 'selected' : '' }}>Metropolitana</option>
                                                <option value="Valparaíso" {{ old('region', $educationalEntity->region) === 'Valparaíso' ? 'selected' : '' }}>Valparaíso</option>
                                                <option value="Biobío" {{ old('region', $educationalEntity->region) === 'Biobío' ? 'selected' : '' }}>Biobío</option>
                                                <option value="Maule" {{ old('region', $educationalEntity->region) === 'Maule' ? 'selected' : '' }}>Maule</option>
                                                <option value="Ñuble" {{ old('region', $educationalEntity->region) === 'Ñuble' ? 'selected' : '' }}>Ñuble</option>
                                                <option value="Araucanía" {{ old('region', $educationalEntity->region) === 'Araucanía' ? 'selected' : '' }}>Araucanía</option>
                                                <option value="Los Ríos" {{ old('region', $educationalEntity->region) === 'Los Ríos' ? 'selected' : '' }}>Los Ríos</option>
                                                <option value="Los Lagos" {{ old('region', $educationalEntity->region) === 'Los Lagos' ? 'selected' : '' }}>Los Lagos</option>
                                                <option value="Aysén" {{ old('region', $educationalEntity->region) === 'Aysén' ? 'selected' : '' }}>Aysén</option>
                                                <option value="Magallanes" {{ old('region', $educationalEntity->region) === 'Magallanes' ? 'selected' : '' }}>Magallanes</option>
                                                <option value="Arica y Parinacota" {{ old('region', $educationalEntity->region) === 'Arica y Parinacota' ? 'selected' : '' }}>Arica y Parinacota</option>
                                                <option value="Tarapacá" {{ old('region', $educationalEntity->region) === 'Tarapacá' ? 'selected' : '' }}>Tarapacá</option>
                                                <option value="Antofagasta" {{ old('region', $educationalEntity->region) === 'Antofagasta' ? 'selected' : '' }}>Antofagasta</option>
                                                <option value="Atacama" {{ old('region', $educationalEntity->region) === 'Atacama' ? 'selected' : '' }}>Atacama</option>
                                                <option value="Coquimbo" {{ old('region', $educationalEntity->region) === 'Coquimbo' ? 'selected' : '' }}>Coquimbo</option>
                                            </select>
                                            @error('region')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- País -->
                                        <div class="form-group">
                                            <label for="country">País</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror"
                                                   id="country" name="country" value="{{ old('country', $educationalEntity->country ?? 'Chile') }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Teléfono -->
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone', $educationalEntity->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $educationalEntity->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Sitio Web -->
                                        <div class="form-group">
                                            <label for="website">Sitio Web</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="website" name="website" value="{{ old('website', $educationalEntity->website) }}"
                                                   placeholder="https://ejemplo.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-file-alt mr-2"></i>
                                            Descripción
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="4"
                                                      placeholder="Descripción detallada de la entidad educativa...">{{ old('description', $educationalEntity->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Entidad
                        </button>
                        <a href="{{ route('educational-entities.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: ' *';
    color: red;
    font-weight: bold;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
@endsection