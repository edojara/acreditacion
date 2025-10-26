# Guía de Desarrollo - Sistema de Acreditación

## Introducción

Esta guía proporciona las mejores prácticas y estándares para el desarrollo continuo del Sistema de Acreditación. Está diseñada para mantener la calidad del código, la seguridad y la consistencia del proyecto.

## Estructura del Proyecto

### Organización de Archivos
```
acreditacion/
├── app/                          # Código de aplicación
│   ├── Http/Controllers/         # Controladores
│   │   ├── Auth/                # Controladores de autenticación
│   │   ├── UserController.php   # Gestión de usuarios
│   │   └── AuditLogController.php # Logs de auditoría
│   ├── Models/                  # Modelos Eloquent
│   ├── Middleware/              # Middleware personalizado
│   └── Providers/               # Service Providers
├── resources/views/             # Vistas Blade
│   ├── layouts/                 # Layouts principales
│   ├── admin/                   # Vistas de administración
│   ├── auth/                    # Vistas de autenticación
│   └── users/                   # Vistas de gestión de usuarios
├── database/                    # Migraciones y seeders
│   ├── migrations/              # Migraciones de BD
│   └── seeders/                 # Datos iniciales
├── routes/                      # Definición de rutas
├── config/                      # Configuraciones
├── public/                      # Assets públicos
├── storage/                     # Archivos temporales
├── tests/                       # Tests automatizados
└── docs/                        # Documentación
```

## Estándares de Codificación

### PHP/Laravel Standards

#### Nomenclatura
```php
// Clases: PascalCase
class UserController extends Controller

// Métodos: camelCase
public function store(Request $request)

// Variables: camelCase
$userName = $request->input('name');

// Constantes: UPPER_SNAKE_CASE
const DEFAULT_PASSWORD = 'Abcd.1234';

// Archivos: PascalCase para clases
UserController.php
RoleMiddleware.php
```

#### Estructura de Controladores
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lógica aquí
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        // Lógica de negocio
        $user = User::create($request->validated());

        // Auditoría
        \App\Models\AuditLog::log('create', 'Usuario creado', [
            'model_type' => 'User',
            'model_id' => $user->id,
        ]);

        // Respuesta
        return redirect()->route('users.index')
                        ->with('success', 'Usuario creado exitosamente.');
    }
}
```

### Blade Templates Standards

#### Estructura de Vistas
```blade
{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usuarios del Sistema</h3>
                </div>
                <div class="card-body">
                    {{-- Contenido aquí --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### Formularios Seguros
```blade
<form method="POST" action="{{ route('users.store') }}">
    @csrf
    @method('POST')

    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
               id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Crear Usuario</button>
</form>
```

## Seguridad en el Desarrollo

### Validación de Datos
```php
// Validación completa en controladores
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|min:2',
        'email' => 'required|email:rfc,dns|unique:users,email',
        'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        'role_id' => 'required|exists:roles,id',
    ]);

    // Usar datos validados
    $user = User::create($validated);
}
```

### Auditoría Obligatoria
```php
// Registrar todas las operaciones importantes
public function update(Request $request, User $user)
{
    $oldValues = $user->only(['name', 'email', 'role_id']);

    $user->update($request->validated());

    $newValues = $user->only(['name', 'email', 'role_id']);

    \App\Models\AuditLog::log('update', 'Usuario actualizado: ' . $user->name, [
        'model_type' => 'User',
        'model_id' => $user->id,
        'old_values' => $oldValues,
        'new_values' => $newValues,
    ]);
}
```

### Manejo de Errores Seguro
```php
try {
    // Operación que puede fallar
    $user = User::create($data);

    \App\Models\AuditLog::log('create', 'Usuario creado exitosamente', [
        'model_type' => 'User',
        'model_id' => $user->id,
    ]);

} catch (\Exception $e) {
    \Log::error('Error creando usuario: ' . $e->getMessage(), [
        'data' => $data,
        'user_id' => auth()->id(),
    ]);

    return back()->withErrors(['error' => 'Error al crear el usuario.']);
}
```

## Base de Datos

### Migraciones
```php
// database/migrations/2025_01_01_000000_create_example_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Índices adicionales si son necesarios
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examples');
    }
};
```

### Modelos Eloquent
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Example extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'description',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes útiles
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors/Mutators si son necesarios
    public function getNameAttribute($value)
    {
        return ucwords($value);
    }
}
```

### Seeders
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso completo al sistema',
                'permissions' => ['manage_users', 'manage_roles', 'view_reports'],
            ],
            // ... más roles
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
```

## Rutas y Middleware

### Definición de Rutas
```php
// routes/web.php
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas protegidas por roles
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
    });

    Route::middleware('role:report')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});
```

### Middleware Personalizado
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
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
}
```

## Testing

### Tests de Características
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);

        $response = $this->actingAs($admin)
                        ->post(route('users.store'), [
                            'name' => 'Test User',
                            'email' => 'test@example.com',
                            'password' => 'password123',
                            'password_confirmation' => 'password123',
                            'role_id' => Role::where('slug', 'report')->first()->id,
                        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_non_admin_cannot_create_user()
    {
        $user = User::factory()->create(['role_id' => Role::where('slug', 'report')->first()->id]);

        $response = $this->actingAs($user)
                        ->post(route('users.store'), [
                            'name' => 'Test User',
                            'email' => 'test@example.com',
                        ]);

        $response->assertForbidden();
    }
}
```

### Tests Unitarios
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserTest extends TestCase
{
    public function test_user_has_role()
    {
        $role = Role::factory()->create(['slug' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('report'));
    }

    public function test_user_has_permission()
    {
        $role = Role::factory()->create([
            'slug' => 'admin',
            'permissions' => ['manage_users', 'view_reports']
        ]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->hasPermission('manage_users'));
        $this->assertFalse($user->hasPermission('delete_system'));
    }
}
```

## Control de Versiones

### Flujo de Trabajo Git
```bash
# Desarrollo local
git checkout -b feature/nueva-funcionalidad
# Hacer cambios
git add .
git commit -m "feat: agregar nueva funcionalidad

- Descripción del cambio
- Impacto en seguridad
- Tests realizados"

# Push a rama
git push origin feature/nueva-funcionalidad

# Crear Pull Request en GitHub
# Revisión por pares
# Merge a master

# En servidor de producción
git pull origin master
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Commits Semánticos
- `feat:` Nueva funcionalidad
- `fix:` Corrección de bug
- `docs:` Cambios en documentación
- `style:` Cambios de formato
- `refactor:` Refactorización de código
- `test:` Agregar o modificar tests
- `chore:` Cambios en configuración

## Despliegue

### Checklist de Despliegue
- [ ] Tests pasan en local
- [ ] Código revisado por pares
- [ ] Migraciones probadas
- [ ] Variables de entorno configuradas
- [ ] Backups realizados
- [ ] Notificación a usuarios sobre mantenimiento

### Comandos de Despliegue
```bash
# En servidor de producción
cd /var/www/html

# Backup de base de datos
mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql

# Actualizar código
git pull origin master

# Instalar dependencias
composer install --no-dev --optimize-autoloader

# Ejecutar migraciones
php artisan migrate

# Limpiar y optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
sudo systemctl restart apache2
sudo systemctl restart php8.3-fpm

# Verificar funcionamiento
curl -I https://acreditacion.grupoeducar.cl
```

## Monitoreo y Mantenimiento

### Logs a Revisar
```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de Apache
tail -f /var/log/apache2/acreditacion_access.log
tail -f /var/log/apache2/acreditacion_error.log

# Logs de sistema
tail -f /var/log/syslog
```

### Métricas a Monitorear
- Uso de CPU y memoria
- Conexiones a base de datos activas
- Tiempo de respuesta de endpoints
- Errores 500 y 403
- Intentos de login fallidos
- Cambios en usuarios y roles

### Tareas Programadas
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Backup diario
    $schedule->command('backup:run')
             ->dailyAt('02:00');

    // Limpiar logs antiguos
    $schedule->command('audit-logs:cleanup')
             ->weekly();

    // Reportes semanales
    $schedule->command('reports:generate')
             ->weeklyOn(1, '08:00'); // Lunes 8 AM
}
```

## Mejores Prácticas Generales

### Principios SOLID
1. **Single Responsibility**: Cada clase tiene una responsabilidad única
2. **Open/Closed**: Código abierto a extensión, cerrado a modificación
3. **Liskov Substitution**: Subtipos pueden reemplazar a sus tipos base
4. **Interface Segregation**: Interfaces específicas mejor que generales
5. **Dependency Inversion**: Depender de abstracciones, no concretos

### Patrón Repository (Futuro)
```php
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): bool;
    public function delete(User $user): bool;
}

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    // ... otros métodos
}
```

### Manejo de Excepciones
```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($exception instanceof AuthorizationException) {
        return response()->view('errors.403', [], 403);
    }

    if ($exception instanceof ModelNotFoundException) {
        return response()->view('errors.404', [], 404);
    }

    // Loggear errores no manejados
    \Log::error('Unhandled exception', [
        'exception' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'user_id' => auth()->id(),
        'ip' => request()->ip(),
    ]);

    return parent::render($request, $exception);
}
```

### Optimización de Rendimiento
```php
// Eager loading para evitar N+1 queries
$users = User::with('role')->paginate(15);

// Cache de configuraciones
php artisan config:cache

// Cache de rutas
php artisan route:cache

// Cache de vistas
php artisan view:cache

// Optimización de autoloader
composer install --optimize-autoloader
```

Esta guía debe ser seguida por todos los desarrolladores que trabajen en el proyecto para mantener la calidad, seguridad y consistencia del código.