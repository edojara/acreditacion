@extends('layouts.app')

@section('title', 'Crear Integrante')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('participants.index') }}">Integrantes</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Nuevo Integrante
                    </h3>
                </div>

                <form method="POST" action="{{ route('participants.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                           id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}" placeholder="+56 9 XXXX XXXX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Cargo</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror"
                                           id="position" name="position" value="{{ old('position') }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="educational_entity_id">Institución Educativa <span class="text-danger">*</span></label>
                                    <select class="form-control @error('educational_entity_id') is-invalid @enderror"
                                            id="educational_entity_id" name="educational_entity_id" required>
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
                                    <label for="registration_date">Fecha de Registro</label>
                                    <input type="date" class="form-control @error('registration_date') is-invalid @enderror"
                                           id="registration_date" name="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}">
                                    @error('registration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('participants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Integrante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection