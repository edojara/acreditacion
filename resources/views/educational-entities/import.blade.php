@extends('layouts.app')

@section('title', 'Importar Instituciones Educativas')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Instituciones</a></li>
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
                        Importar Instituciones Educativas desde CSV
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
                                <li>Los campos obligatorios son: nombre de la institución y tipo</li>
                                <li>El tamaño máximo del archivo es de 2MB</li>
                            </ol>

                            <h6>Tipos de institución válidos:</h6>
                            <ul>
                                <li><strong>universidad</strong> - Universidades</li>
                                <li><strong>instituto</strong> - Institutos profesionales</li>
                                <li><strong>colegio</strong> - Colegios y liceos</li>
                                <li><strong>centro_educativo</strong> - Centros educativos</li>
                                <li><strong>otro</strong> - Otros tipos de instituciones</li>
                            </ul>

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
                                            <td><code>nombre</code></td>
                                            <td><code>name, institucion, institución</code></td>
                                            <td><span class="badge badge-danger">Sí</span></td>
                                            <td>Nombre completo de la institución</td>
                                        </tr>
                                        <tr>
                                            <td><code>tipo</code></td>
                                            <td><code>type</code></td>
                                            <td><span class="badge badge-danger">Sí</span></td>
                                            <td>Tipo de institución (ver lista arriba)</td>
                                        </tr>
                                        <tr>
                                            <td><code>direccion</code></td>
                                            <td><code>address, dirección</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Dirección de la institución</td>
                                        </tr>
                                        <tr>
                                            <td><code>ciudad</code></td>
                                            <td><code>city, ciudad</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Ciudad donde se ubica</td>
                                        </tr>
                                        <tr>
                                            <td><code>region</code></td>
                                            <td><code>región</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Región de Chile</td>
                                        </tr>
                                        <tr>
                                            <td><code>pais</code></td>
                                            <td><code>country, país</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>País (por defecto: Chile)</td>
                                        </tr>
                                        <tr>
                                            <td><code>telefono</code></td>
                                            <td><code>phone, teléfono</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Número de teléfono</td>
                                        </tr>
                                        <tr>
                                            <td><code>email</code></td>
                                            <td><code>correo</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Correo electrónico</td>
                                        </tr>
                                        <tr>
                                            <td><code>sitio_web</code></td>
                                            <td><code>website, web</code></td>
                                            <td><span class="badge badge-secondary">No</span></td>
                                            <td>Sitio web de la institución</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h6>Ejemplo de archivo CSV:</h6>
                            <pre class="bg-light p-3"><code>nombre,tipo,direccion,ciudad,region,telefono,email,sitio_web
Universidad de Chile,universidad,Av. Libertador Bernardo O'Higgins 1058,Santiago,Metropolitana,+56 2 2978 4000,uchile@uchile.cl,www.uchile.cl
Pontificia Universidad Católica,instituto,Av. Libertador Bernardo O'Higgins 340,Santiago,Metropolitana,+56 2 2354 2000,uc@uc.cl,www.uc.cl
Instituto Profesional AIEP,centro_educativo,Calle Los Militares 6191,Santiago,Metropolitana,+56 2 2820 5000,contacto@aiep.cl,www.aiep.cl</code></pre>
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
                                    <form action="{{ route('educational-entities.import.post') }}" method="POST" enctype="multipart/form-data">
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
                                                Importar Instituciones
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('educational-entities.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Volver a Instituciones
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