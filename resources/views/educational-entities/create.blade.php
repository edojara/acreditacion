@extends('layouts.app')

@section('title', 'Crear Entidad Educativa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Entidades</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Nueva Entidad Educativa
                    </h3>
                </div>

                <form method="POST" action="{{ route('educational-entities.store') }}">
                    @csrf

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
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="website">Sitio Web</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                   id="website" name="website" value="{{ old('website') }}"
                                                   placeholder="https://www.ejemplo.cl">
                                            @error('website')
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
                            <i class="fas fa-save"></i> Crear Entidad
                        </button>
                        <a href="{{ route('educational-entities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection