@extends('layouts.app')

@section('title', 'Detalles del Integrante')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ route('participants.index') }}">Integrantes</a></li>
    <li class="breadcrumb-item active">{{ $participant->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        Detalles del Integrante
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('participants.edit', $participant) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('participants.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre Completo:</label>
                                <p class="form-control-plaintext">{{ $participant->full_name }}</p>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Teléfono:</label>
                                <p class="form-control-plaintext">
                                    @if($participant->phone)
                                        <a href="tel:{{ $participant->phone }}" class="text-decoration-none">
                                            {{ $participant->formatted_phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Cargo:</label>
                                <p class="form-control-plaintext">
                                    {{ $participant->position ?? 'No especificado' }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Registro:</label>
                                <p class="form-control-plaintext">
                                    @if($participant->registration_date)
                                        {{ $participant->registration_date->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Institución Educativa:</label>
                                <p class="form-control-plaintext">
                                    <a href="{{ route('educational-entities.show', $participant->educationalEntity) }}" class="text-decoration-none">
                                        {{ $participant->educationalEntity->name }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Creado: {{ $participant->created_at->format('d/m/Y H:i') }}
                                @if($participant->updated_at != $participant->created_at)
                                    | Modificado: {{ $participant->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection