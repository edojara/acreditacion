@extends('layouts.app')

@section('title', 'Importar Integrantes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('participants.index') }}">Integrantes</a></li>
<li class="breadcrumb-item active">Importar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload mr-2"></i>
                        Importar Integrantes desde CSV
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-ban"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h5>Instrucciones para la importación:</h5>
                            <ol>
                                <li>El archivo debe estar en formato CSV (valores separados por comas)</li>
                                <li>La primera fila debe contener los encabezados de las columnas</li>
                                <li>Los campos obligatorios son: institución y nombre completo</li>
                                <li>El tamaño máximo del archivo es de 2MB</li>
                            </ol>

                            <h6>Formato esperado del CSV:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Columna</th>
                                            <th>Nombre alternativo</th>
                                            <th>Obligatorio</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>institucion</code></td>
                                            <td><code>institución, educational_entity, entidad_educativa</code></td>
                                            <td><span class="badge badge-danger">Sí</span></td>
                                            <td>Nombre de la institución educativa</td>
                                        </tr>
                                        <tr>
                                            <td><code>nombre_completo</code></td>
                                            <td><code>full_name, nombre</code></td>
                                            <td><span class="badge badge-danger">Sí</span></td>
                                            <td>Nombre completo del participante</td>
                                        </tr>
                                        <tr>
                                            <td><code>telefono</code></td>
                                            <td><code>celular, phone, mobile</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Número de teléfono</td>
                                        </tr>
                                        <tr>
                                            <td><code>cargo</code></td>
                                            <td><code>position, puesto</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Cargo o posición del participante</td>
                                        </tr>
                                        <tr>
                                            <td><code>fecha_registro</code></td>
                                            <td><code>registration_date, fecha</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Fecha de registro (formato: YYYY-MM-DD)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Ejemplo de archivo CSV:</h6>
                            <pre class="bg-light p-3"><code>institucion,nombre_completo,telefono,cargo,fecha_registro
Universidad de Chile,María González,+56912345678,Profesora,2024-01-15
Pontificia Universidad Católica,Juan Pérez,+56987654321,Estudiante,2024-02-20
Universidad de Santiago,Carmen Rodríguez,+56911223344,Investigadora,2024-03-10</code></pre>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-upload mr-2"></i>
                                        Subir Archivo
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('participants.import.post') }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="form-group">
                                            <label for="csv_file">Archivo CSV:</label>
                                            <input type="file" class="form-control-file" id="csv_file" name="csv_file"
                                                   accept=".csv,.txt" required>
                                            <small class="form-text text-muted">
                                                Solo archivos CSV o TXT. Máximo 2MB.
                                            </small>
                                        </div>

                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-upload mr-2"></i>
                                                Importar Integrantes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('participants.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver a Integrantes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Validación del archivo antes de enviar
    $('form').on('submit', function(e) {
        var fileInput = $('#csv_file')[0];
        var file = fileInput.files[0];

        if (!file) {
            e.preventDefault();
            alert('Por favor selecciona un archivo CSV.');
            return false;
        }

        // Verificar extensión
        var allowedExtensions = ['csv', 'txt'];
        var fileExtension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(fileExtension)) {
            e.preventDefault();
            alert('Solo se permiten archivos con extensión .csv o .txt');
            return false;
        }

        // Verificar tamaño (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            e.preventDefault();
            alert('El archivo no puede ser mayor a 2MB.');
            return false;
        }
    });
});
</script>
@endsection