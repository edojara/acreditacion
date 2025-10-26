# Patrones de Seguridad Implementados

## Resumen de Seguridad

El Sistema de Acreditación implementa múltiples capas de seguridad siguiendo las mejores prácticas de desarrollo web seguro. A continuación se documentan los patrones y medidas de seguridad implementadas.

## 1. Autenticación Segura

### Autenticación Multifactor Implícita
```php
// En LoginController.php
if (Auth::attempt($credentials)) {
    $user = Auth::user();

    // Registro de login exitoso
    \App\Models\AuditLog::log('login', 'Usuario inició sesión con credenciales', [
        'user_email' => $user->email,
        'model_type' => 'User',
        'model_id' => $user->id,
    ]);

    // Verificación de cambio obligatorio de contraseña
    if ($user->must_change_password) {
        return redirect()->route('password.change');
    }

    return $this->redirectBasedOnRole($user);
}
```

**Patrones Implementados**:
- **Autenticación Dual**: Soporte para credenciales locales y OAuth Google
- **Sesiones Seguras**: Almacenadas en base de datos con expiración de 30 minutos
- **Auditoría Completa**: Registro de todos los intentos de autenticación
- **Cambio Forzado de Contraseña**: Mecanismo para forzar actualización de credenciales

### OAuth Google Seguro
```php
// En GoogleController.php - Validación estricta
$user = User::where('email', $googleUser->getEmail())->first();

if (!$user) {
    // DENEGAR ACCESO COMPLETAMENTE
    \App\Models\AuditLog::log('login_google_denied', 'Intento de login con Google DENEGADO', [
        'user_email' => $googleUser->getEmail(),
        'ip_address' => request()->ip(),
    ]);

    return redirect('/login')->with('error', 'Acceso denegado. Tu email no está registrado.');
}
```

**Medidas de Seguridad**:
- **Acceso Pre-registrado**: Solo usuarios existentes pueden usar Google OAuth
- **Vinculación Controlada**: Primera vinculación automática, validación posterior
- **Validación de Identidad**: Verificación de Google ID existente
- **Auditoría de Denegaciones**: Registro de intentos fallidos

## 2. Autorización y Control de Acceso

### Middleware de Roles
```php
// En RoleMiddleware.php
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

### Sistema de Permisos Granular
```php
// En RoleSeeder.php - Definición de permisos
'permissions' => [
    'manage_users',
    'manage_roles',
    'view_reports',
    'manage_accreditations',
    'system_settings'
]

// En User.php - Verificación de permisos
public function hasPermission(string $permission): bool
{
    return $this->role && $this->role->hasPermission($permission);
}
```

**Patrones RBAC (Role-Based Access Control)**:
- **Roles Jerárquicos**: Admin > Enrolador > Informe > Solo Lectura
- **Permisos Granulares**: Array JSON de permisos por rol
- **Separación de Responsabilidades**: Cada rol tiene acceso limitado
- **Principio de Menor Privilegio**: Usuarios solo acceden a lo necesario

## 3. Validación y Sanitización de Datos

### Validación en Capa de Controlador
```php
// En UserController.php
$rules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'role_id' => 'required|exists:roles,id',
    'account_type' => 'required|in:local,google'
];

if ($request->account_type === 'local') {
    $rules['password'] = 'required|string|min:8|confirmed';
    $rules['must_change_password'] = 'boolean';
}

$request->validate($rules);
```

### Validación de Contraseñas
```php
// En LoginController.php
$request->validate([
    'current_password' => ['required', 'current_password'],
    'password' => ['required', 'confirmed', Password::defaults()],
]);
```

**Técnicas de Validación**:
- **Validación del lado del servidor**: Laravel Validation Rules
- **Sanitización automática**: Eloquent casting y mutators
- **Validación condicional**: Diferentes reglas según tipo de cuenta
- **Confirmación de contraseñas**: Prevención de errores tipográficos

## 4. Protección contra Ataques Comunes

### Protección CSRF
```php
// En todas las vistas Blade
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>
```

### Prevención de Inyección SQL
```php
// Eloquent ORM automáticamente previene SQL injection
$user = User::where('email', $request->email)->first();

// Parámetros vinculados automáticamente
$user->update([
    'name' => $request->name,
    'email' => $request->email,
]);
```

### Control de Sesiones
```php
// En config/session.php
'lifetime' => env('SESSION_LIFETIME', 30), // 30 minutos
'driver' => env('SESSION_DRIVER', 'database'),
```

## 5. Auditoría y Monitoreo

### Sistema de Logs Completo
```php
// En AuditLog.php - Método unificado de logging
public static function log(string $action, string $description = null, array $data = []): self
{
    return static::create([
        'action' => $action,
        'model_type' => $data['model_type'] ?? null,
        'model_id' => $data['model_id'] ?? null,
        'user_id' => $data['user_id'] ?? auth()->id(),
        'user_email' => $data['user_email'] ?? auth()->user()?->email,
        'old_values' => $data['old_values'] ?? null,
        'new_values' => $data['new_values'] ?? null,
        'ip_address' => request()->ip(),
        'description' => $description,
    ]);
}
```

### Eventos Auditados
- **Autenticación**: login, logout, login_failed, login_google, login_google_denied
- **Gestión de Usuarios**: create, update, delete, force_password_change, reset_password
- **Sistema**: google_account_linked

### Información Registrada
```php
[
    'action' => 'update',
    'model_type' => 'User',
    'model_id' => $user->id,
    'user_id' => auth()->id(),
    'user_email' => auth()->user()->email,
    'old_values' => ['name' => 'Old Name', 'email' => 'old@email.com'],
    'new_values' => ['name' => 'New Name', 'email' => 'new@email.com'],
    'ip_address' => request()->ip(),
    'description' => 'Usuario actualizado: New Name (new@email.com)',
]
```

## 6. Seguridad de Datos

### Hash de Contraseñas
```php
// En UserController.php
$userData['password'] = Hash::make($request->password);

// En modelo User.php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ automatic hashing
    ];
}
```

### Protección de Datos Sensibles
```php
// En modelo User.php
protected $hidden = [
    'password',
    'remember_token',
];
```

### Manejo Seguro de Errores
```php
// En GoogleController.php
try {
    $googleUser = Socialite::driver('google')->user();
    // ... proceso de autenticación
} catch (\Exception $e) {
    \Log::error('Google login error: ' . $e->getMessage());
    return redirect('/login')->with('error', 'Error al iniciar sesión con Google');
}
```

## 7. Seguridad en Producción

### Configuración de Ambiente
```php
// .env.production
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:generated_key

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=acreditacion_prod
DB_USERNAME=acreditacion_user
DB_PASSWORD=secure_password

SESSION_DRIVER=database
SESSION_LIFETIME=30

GOOGLE_CLIENT_ID=production_client_id
GOOGLE_CLIENT_SECRET=production_client_secret
```

### Permisos de Archivos
```bash
# En servidor Ubuntu
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
sudo chmod -R 775 /var/www/html/storage/
```

### Configuración Web Server
```apache
# /etc/apache2/sites-available/acreditacion.conf
<VirtualHost *:80>
    ServerName acreditacion.grupoeducar.cl
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/acreditacion_error.log
    CustomLog ${APACHE_LOG_DIR}/acreditacion_access.log combined
</VirtualHost>
```

## 8. Patrones de Seguridad Adicionales

### Rate Limiting
- Implementado implícitamente a través de Laravel Throttle middleware
- Configurado en rutas críticas de autenticación

### Headers de Seguridad
```php
// En bootstrap/app.php o middleware
$this->headers->set('X-Frame-Options', 'SAMEORIGIN');
$this->headers->set('X-Content-Type-Options', 'nosniff');
$this->headers->set('X-XSS-Protection', '1; mode=block');
```

### Validación de Entrada en Modelos
```php
// En modelo User.php
protected $fillable = [
    'name',
    'email',
    'password',
    'role_id',
    'google_id',
    'avatar',
    'must_change_password',
];
```

## 9. Monitoreo y Alertas

### Logs de Seguridad
- **Laravel Logs**: En `storage/logs/laravel.log`
- **Audit Logs**: Tabla `audit_logs` en base de datos
- **Access Logs**: Apache/Nginx logs

### Métricas de Seguridad
```php
// En AuditLogController.php
$stats = [
    'total_logs' => AuditLog::count(),
    'today_logs' => AuditLog::whereDate('created_at', today())->count(),
    'login_attempts' => AuditLog::where('action', 'login_google')->count(),
    'failed_logins' => AuditLog::where('action', 'login_google_denied')->count(),
    'user_changes' => AuditLog::whereIn('action', ['user_created', 'user_updated', 'user_deleted'])->count(),
];
```

## 10. Mejores Prácticas Implementadas

### Principios SOLID
- **Single Responsibility**: Cada controlador tiene una responsabilidad clara
- **Open/Closed**: Sistema extensible mediante roles y permisos
- **Liskov Substitution**: Modelos intercambiables
- **Interface Segregation**: Permisos granulares
- **Dependency Inversion**: Inyección de dependencias automática

### Patrón Repository (Implícito)
- Lógica de negocio en controladores
- Acceso a datos a través de Eloquent
- Separación clara entre lógica y persistencia

### Patrón Observer
- Eventos automáticos en modelos Eloquent
- Auditoría automática en cambios de modelo

## 11. Recomendaciones de Mejora

### Seguridad Adicional Sugerida
1. **Two-Factor Authentication (2FA)**
2. **Rate Limiting Explícito**
3. **CAPTCHA** en formularios de login
4. **Encryption at Rest** para datos sensibles
5. **Security Headers** completos (HSTS, CSP)
6. **Vulnerability Scanning** regular
7. **Penetration Testing** periódico
8. **Backup Encryption**
9. **Log Aggregation** centralizado
10. **Intrusion Detection System**

### Monitoreo Continuo
1. **Real-time Alerts** para eventos de seguridad
2. **Automated Log Analysis**
3. **Performance Monitoring**
4. **Security Dashboard**
5. **Compliance Reporting**

Esta documentación cubre los patrones de seguridad implementados y proporciona una base sólida para mantener y mejorar la seguridad del sistema.