# Documentación de Arquitectura - Sistema de Acreditación

## Resumen Ejecutivo

El Sistema de Acreditación es una aplicación web desarrollada en Laravel 11.x que implementa un sistema completo de gestión de usuarios con autenticación dual (local y Google OAuth), control de acceso basado en roles, y auditoría completa de actividades.

## Arquitectura General

### Patrón Arquitectónico
- **MVC (Model-View-Controller)** con separación clara de responsabilidades
- **Repository Pattern** implícito a través de Eloquent ORM
- **Middleware Pattern** para control de acceso y autenticación

### Tecnologías Principales
- **Backend**: Laravel 11.x, PHP 8.3
- **Frontend**: Blade Templates, AdminLTE 3, Bootstrap 4, jQuery
- **Base de Datos**: MySQL/MariaDB con Eloquent ORM
- **Autenticación**: Sesiones nativas + Google OAuth 2.0
- **Cache**: Sistema de archivos
- **Sesiones**: Almacenadas en base de datos

## Estructura de Base de Datos

### Tablas Principales

#### users
```sql
- id (BIGINT, PK, AUTO_INCREMENT)
- name (VARCHAR(255))
- email (VARCHAR(255), UNIQUE)
- password (VARCHAR(255), HASHED)
- role_id (BIGINT, FK -> roles.id)
- google_id (VARCHAR(255), NULLABLE, UNIQUE)
- avatar (VARCHAR(255), NULLABLE)
- must_change_password (BOOLEAN, DEFAULT FALSE)
- email_verified_at (TIMESTAMP, NULLABLE)
- remember_token (VARCHAR(100), NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### roles
```sql
- id (BIGINT, PK, AUTO_INCREMENT)
- name (VARCHAR(255), UNIQUE)
- slug (VARCHAR(255), UNIQUE)
- description (TEXT, NULLABLE)
- permissions (JSON, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### audit_logs
```sql
- id (BIGINT, PK, AUTO_INCREMENT)
- action (VARCHAR(255)) -- login, logout, create, update, delete, etc.
- model_type (VARCHAR(255), NULLABLE) -- User, Role, etc.
- model_id (BIGINT, NULLABLE)
- user_id (BIGINT, NULLABLE, FK -> users.id)
- user_email (VARCHAR(255), NULLABLE)
- old_values (JSON, NULLABLE)
- new_values (JSON, NULLABLE)
- ip_address (VARCHAR(45), NULLABLE)
- description (TEXT, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

-- Índices
- INDEX user_action (user_id, action)
- INDEX model (model_type, model_id)
```

## Sistema de Autenticación

### Autenticación Dual

#### 1. Autenticación Local
- **Controlador**: `LoginController@login()`
- **Validación**: Email + contraseña
- **Sesiones**: 30 minutos de duración
- **Flujo**:
  1. Validar credenciales
  2. Registrar login en audit log
  3. Verificar cambio obligatorio de contraseña
  4. Redirigir según rol

#### 2. Autenticación Google OAuth
- **Controlador**: `GoogleController@handleGoogleCallback()`
- **Proveedor**: Laravel Socialite
- **Restricciones**:
  - Solo usuarios pre-registrados pueden acceder
  - Vinculación automática de cuenta Google en primer login
  - Validación de cuenta Google existente

### Cambio Obligatorio de Contraseña
- Campo `must_change_password` en tabla users
- Activado en:
  - Creación de usuario administrador por defecto
  - Reset de contraseña por administrador
  - Usuarios locales nuevos (opcional)

## Sistema de Roles y Permisos

### Roles Definidos

#### 1. Administrador (admin)
**Permisos**: manage_users, manage_roles, view_reports, manage_accreditations, system_settings
**Acceso**: Panel completo de administración

#### 2. Solo Lectura (solo-lectura)
**Permisos**: view_data, view_reports
**Acceso**: Vista limitada de datos

#### 3. Informe (informe)
**Permisos**: view_reports, export_reports, generate_charts
**Acceso**: Módulo de reportes y estadísticas

#### 4. Enrolador (enrolador)
**Permisos**: manage_users, manage_accreditations, view_reports
**Acceso**: Gestión de usuarios y procesos de acreditación

### Middleware de Control de Acceso
- **RoleMiddleware**: Verifica permisos basados en slug de rol
- **Aplicación**: Rutas protegidas por middleware 'role:{slug}'

## Sistema de Auditoría

### Eventos Auditados
- **Autenticación**: login, login_failed, login_google, login_google_denied, logout
- **Gestión de Usuarios**: create, update, delete, force_password_change, reset_password
- **Sistema**: google_account_linked

### Información Registrada
- Usuario que realizó la acción
- Modelo y registro afectados
- Valores anteriores y nuevos
- Dirección IP
- Timestamp
- Descripción detallada

### Estadísticas Disponibles
- Total de logs
- Logs del día
- Intentos de login con Google
- Logins fallidos
- Cambios en usuarios

## Controladores Principales

### UserController
**Responsabilidades**:
- CRUD completo de usuarios
- Gestión de roles
- Reset y cambio forzado de contraseñas
- Validación de tipos de cuenta (local/Google)

**Métodos Clave**:
- `store()`: Creación con validación condicional
- `update()`: Actualización con auditoría
- `destroy()`: Eliminación con protección propia
- `forcePasswordChange()`: Cambio obligatorio
- `resetPassword()`: Reset a contraseña por defecto

### AuditLogController
**Responsabilidades**:
- Listado y filtrado de logs
- Visualización detallada de eventos
- Generación de estadísticas

**Filtros Disponibles**:
- Acción específica
- Usuario
- Rango de fechas

### Auth Controllers
**LoginController**:
- Autenticación tradicional
- Cambio de contraseña
- Redirección basada en roles

**GoogleController**:
- Manejo de OAuth callback
- Vinculación de cuentas Google
- Validación de acceso pre-registrado

## Middleware

### RoleMiddleware
```php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (!$user->hasRole($role)) {
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }

    return $next($request);
}
```

## Modelos y Relaciones

### User Model
**Relaciones**:
- `role()`: BelongsTo Role

**Métodos de Helper**:
- `hasRole(string $role)`: Verifica rol por slug
- `hasPermission(string $permission)`: Verifica permiso específico
- `isAdmin()`, `isReport()`, `isEnroller()`, `isReadOnly()`: Métodos booleanos

### Role Model
**Relaciones**:
- `users()`: HasMany User

**Métodos**:
- `hasPermission(string $permission)`: Verifica permiso en array JSON

### AuditLog Model
**Relaciones**:
- `user()`: BelongsTo User

**Método Estático**:
- `log(string $action, string $description, array $data)`: Registro unificado

## Vistas y UI

### Layout Principal (layouts/app.blade.php)
- **Framework**: AdminLTE 3 + Bootstrap 4
- **Navegación**: Sidebar dinámica basada en roles
- **Componentes**: Navbar, breadcrumbs, footer

### Dashboard Administrativo
- **Estadísticas**: Usuarios totales, pendientes, Google
- **Accesos Rápidos**: Gestión de usuarios, logs, configuración

### Sistema de Rutas

#### Rutas Públicas
```php
Route::get('/', ...); // Login form
Route::get('/login', ...);
Route::post('/login', ...);
Route::get('/auth/google', ...); // Google OAuth
Route::get('/auth/google/callback', ...);
```

#### Rutas Protegidas
```php
Route::middleware('auth')->group(function () {
    // Dashboard general
    Route::get('/dashboard', ...);

    // Cambio de contraseña
    Route::get('/password/change', ...);
    Route::post('/password/change', ...);

    // Rutas por rol
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
    });

    Route::middleware('role:report')->group(function () {
        Route::get('/reports', ...);
    });

    Route::middleware('role:enroller')->group(function () {
        Route::get('/enrollments', ...);
    });

    // Logout
    Route::post('/logout', ...);
});
```

## Seguridad Implementada

### Medidas de Seguridad
1. **Hash de Contraseñas**: bcrypt
2. **Protección CSRF**: Tokens en formularios
3. **Validación de Datos**: Laravel Validation Rules
4. **Sesiones Seguras**: Almacenadas en BD, expiración 30 min
5. **Auditoría Completa**: Registro de todas las acciones
6. **Control de Acceso**: Middleware basado en roles
7. **OAuth Seguro**: Solo usuarios pre-registrados

### Validaciones Específicas
- **Contraseñas**: Mínimo 8 caracteres, confirmación
- **Emails**: Únicos, formato válido
- **Roles**: Existencia en tabla roles
- **Google ID**: Único, vinculación controlada

## Flujo de Desarrollo

### Ambiente de Desarrollo
- **Local**: Windows + VSCode + PHP 8.3
- **Base de Datos**: Local MySQL/MariaDB
- **Control de Versiones**: Git + GitHub

### Ambiente de Producción
- **Servidor**: Ubuntu + Apache/Nginx + PHP-FPM
- **Base de Datos**: MySQL/MariaDB
- **Document Root**: /var/www/html/
- **Usuario Web**: www-data

### Proceso de Despliegue
1. Desarrollo local en VSCode
2. Commits a repositorio Git local
3. Push a GitHub (git push origin master)
4. Pull en servidor Ubuntu (git pull origin master)
5. Sincronización automática de archivos

## Configuración y Dependencias

### Composer Dependencies
```json
{
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/socialite": "^5.10",
    "laravel/tinker": "^2.9"
}
```

### Configuración OAuth Google
```php
// config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

## Próximas Mejoras Sugeridas

1. **API REST**: Para integración con otros sistemas
2. **Cache Avanzado**: Redis para mejor rendimiento
3. **Tests Automatizados**: Cobertura completa con PHPUnit
4. **Logging Estructurado**: Monolog con diferentes canales
5. **Backup Automático**: Estrategia de respaldo de BD
6. **Monitorización**: Métricas y alertas del sistema
7. **Documentación API**: Swagger/OpenAPI
8. **Internacionalización**: Soporte multi-idioma
9. **Two-Factor Authentication**: Autenticación de dos factores
10. **Rate Limiting**: Protección contra ataques de fuerza bruta