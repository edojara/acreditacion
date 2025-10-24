@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus mr-2"></i>
                        Crear Nuevo Usuario
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de cuenta -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tipo de Cuenta <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="account_type_local"
                                                       name="account_type" value="local" checked>
                                                <label for="account_type_local" class="custom-control-label">
                                                    <i class="fas fa-key mr-2"></i>
                                                    <strong>Cuenta Local</strong><br>
                                                    <small>Usuario con contraseña local</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="account_type_google"
                                                       name="account_type" value="google">
                                                <label for="account_type_google" class="custom-control-label">
                                                    <i class="fab fa-google mr-2"></i>
                                                    <strong>Cuenta Google</strong><br>
                                                    <small>Usuario con acceso Google OAuth</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email (común para ambos tipos) -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted" id="email_help">
                                        Para cuentas locales: email para login. Para cuentas Google: debe coincidir con la cuenta Google del usuario.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Campos de contraseña (solo para cuentas locales) -->
                        <div class="row" id="password_fields">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Mínimo 8 caracteres</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role_id">Rol <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role_id') is-invalid @enderror"
                                            id="role_id" name="role_id" required>
                                        <option value="">Seleccionar rol...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" id="force_password_change_field">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input"
                                               id="must_change_password" name="must_change_password"
                                               {{ old('must_change_password') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="must_change_password">
                                            Forzar cambio de contraseña en primer login
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si se marca, el usuario deberá cambiar su contraseña en el próximo inicio de sesión.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountTypeLocal = document.getElementById('account_type_local');
    const accountTypeGoogle = document.getElementById('account_type_google');
    const passwordFields = document.getElementById('password_fields');
    const forcePasswordChangeField = document.getElementById('force_password_change_field');
    const emailHelp = document.getElementById('email_help');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const mustChangePasswordInput = document.getElementById('must_change_password');

    function toggleAccountType() {
        if (accountTypeLocal.checked) {
            // Cuenta local
            passwordFields.style.display = 'block';
            forcePasswordChangeField.style.display = 'block';
            passwordInput.required = true;
            confirmPasswordInput.required = true;
            emailHelp.textContent = 'Para cuentas locales: email para login.';
        } else {
            // Cuenta Google
            passwordFields.style.display = 'none';
            forcePasswordChangeField.style.display = 'none';
            passwordInput.required = false;
            confirmPasswordInput.required = false;
            passwordInput.value = '';
            confirmPasswordInput.value = '';
            mustChangePasswordInput.checked = false;
            emailHelp.textContent = 'Para cuentas Google: debe coincidir con la cuenta Google del usuario.';
        }
    }

    // Event listeners para cambio de tipo de cuenta
    accountTypeLocal.addEventListener('change', toggleAccountType);
    accountTypeGoogle.addEventListener('change', toggleAccountType);

    // Validación de contraseñas en tiempo real (solo para cuentas locales)
    function validatePassword() {
        if (accountTypeLocal.checked && passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    }

    passwordInput.addEventListener('change', validatePassword);
    confirmPasswordInput.addEventListener('keyup', validatePassword);

    // Inicializar estado
    toggleAccountType();
});
</script>
@endsection