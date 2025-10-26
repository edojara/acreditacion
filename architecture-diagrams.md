# Diagramas de Arquitectura - Sistema de Acreditación

## Diagrama de Arquitectura General

```mermaid
graph TB
    subgraph "Cliente Web"
        Browser[Browser/Web Client]
    end

    subgraph "Servidor Web - Ubuntu"
        Apache[Apache/Nginx]
        PHP_FPM[PHP-FPM 8.3]
    end

    subgraph "Aplicación Laravel"
        Routes[Routes<br/>web.php]
        Middleware[Middleware<br/>Auth, Role]
        Controllers[Controllers<br/>User, Auth, AuditLog]

        subgraph "Modelos Eloquent"
            User[User Model]
            Role[Role Model]
            AuditLog[AuditLog Model]
        end

        Views[Blade Views<br/>AdminLTE + Bootstrap]
    end

    subgraph "Base de Datos"
        MySQL[(MySQL/MariaDB)]
    end

    subgraph "Servicios Externos"
        Google[Google OAuth 2.0]
        GitHub[GitHub<br/>Repository]
    end

    Browser --> Apache
    Apache --> PHP_FPM
    PHP_FPM --> Routes
    Routes --> Middleware
    Middleware --> Controllers
    Controllers --> User
    Controllers --> Role
    Controllers --> AuditLog
    User --> MySQL
    Role --> MySQL
    AuditLog --> MySQL
    Controllers --> Views
    Views --> Browser

    Controllers --> Google
    Google --> Controllers

    GitHub --> Apache
```

## Diagrama de Flujo de Autenticación

```mermaid
flowchart TD
    Start([Usuario accede al sistema]) --> CheckAuth{¿Está autenticado?}

    CheckAuth -->|No| LoginForm[Mostrar formulario de login]
    CheckAuth -->|Sí| CheckPassword{¿Debe cambiar<br/>contraseña?}

    LoginForm --> AuthChoice{¿Tipo de autenticación?}

    AuthChoice -->|Local| ValidateCredentials[Validar email + contraseña]
    AuthChoice -->|Google| RedirectGoogle[Redirigir a Google OAuth]

    ValidateCredentials --> ValidCreds{¿Credenciales válidas?}
    ValidCreds -->|Sí| LoginSuccess[Login exitoso - Registrar en audit log]
    ValidCreds -->|No| LoginFailed[Login fallido - Registrar en audit log]

    RedirectGoogle --> GoogleCallback[Callback de Google OAuth]
    GoogleCallback --> CheckPreRegistered{¿Usuario pre-registrado<br/>con este email?}

    CheckPreRegistered -->|No| AccessDenied[Acceso DENEGADO<br/>Email no registrado]
    CheckPreRegistered -->|Sí| CheckGoogleLinked{¿Cuenta Google<br/>ya vinculada?}

    CheckGoogleLinked -->|No| LinkGoogle[Vincular cuenta Google<br/>al usuario]
    CheckGoogleLinked -->|Sí| ValidateGoogleId{¿Google ID coincide?}

    ValidateGoogleId -->|No| AccessDenied
    ValidateGoogleId -->|Sí| LoginSuccess

    LinkGoogle --> LoginSuccess
    LoginSuccess --> CheckPassword

    CheckPassword -->|Sí| ForcePasswordChange[Forzar cambio de contraseña]
    CheckPassword -->|No| RedirectByRole[Redirigir según rol]

    ForcePasswordChange --> RedirectByRole

    RedirectByRole --> Admin{¿Es Admin?}
    Admin -->|Sí| AdminDashboard[Panel Administrativo]
    Admin -->|No| Report{¿Es Report?}
    Report -->|Sí| ReportsDashboard[Dashboard de Reportes]
    Report -->|No| Enroller{¿Es Enrolador?}
    Enroller -->|Sí| EnrollmentsDashboard[Dashboard de Inscripciones]
    Enroller -->|No| ReadOnlyDashboard[Dashboard Solo Lectura]

    LoginFailed --> LoginForm
    AccessDenied --> LoginForm
```

## Diagrama de Roles y Permisos

```mermaid
flowchart TD
    subgraph "Roles del Sistema"
        Admin[Administrador<br/>admin]
        ReadOnly[Solo Lectura<br/>solo-lectura]
        Report[Informe<br/>informe]
        Enroller[Enrolador<br/>enrolador]
    end

    subgraph "Permisos Administrador"
        ManageUsers[manage_users]
        ManageRoles[manage_roles]
        ViewReports[view_reports]
        ManageAccreditations[manage_accreditations]
        SystemSettings[system_settings]
    end

    subgraph "Permisos Solo Lectura"
        ViewData[view_data]
        ViewReports_RO[view_reports]
    end

    subgraph "Permisos Informe"
        ViewReports_R[view_reports]
        ExportReports[export_reports]
        GenerateCharts[generate_charts]
    end

    subgraph "Permisos Enrolador"
        ManageUsers_E[manage_users]
        ManageAccreditations_E[manage_accreditations]
        ViewReports_E[view_reports]
    end

    Admin --> ManageUsers
    Admin --> ManageRoles
    Admin --> ViewReports
    Admin --> ManageAccreditations
    Admin --> SystemSettings

    ReadOnly --> ViewData
    ReadOnly --> ViewReports_RO

    Report --> ViewReports_R
    Report --> ExportReports
    Report --> GenerateCharts

    Enroller --> ManageUsers_E
    Enroller --> ManageAccreditations_E
    Enroller --> ViewReports_E
```

## Diagrama de Base de Datos - Relaciones

```mermaid
erDiagram
    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar password
        bigint role_id FK
        varchar google_id UK
        varchar avatar
        boolean must_change_password
        timestamp email_verified_at
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    roles {
        bigint id PK
        varchar name UK
        varchar slug UK
        text description
        json permissions
        timestamp created_at
        timestamp updated_at
    }

    audit_logs {
        bigint id PK
        varchar action
        varchar model_type
        bigint model_id
        bigint user_id FK
        varchar user_email
        json old_values
        json new_values
        varchar ip_address
        text description
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ audit_logs : "generates"
    roles ||--o{ users : "assigned_to"
```

## Diagrama de Secuencia - CRUD Usuario

```mermaid
sequenceDiagram
    participant U as Usuario
    participant C as UserController
    participant M as User Model
    participant DB as Base de Datos
    participant AL as AuditLog

    U->>C: POST /users (store)
    C->>C: Validar datos
    C->>M: User::create(data)
    M->>DB: INSERT INTO users
    DB-->>M: Usuario creado
    M-->>C: Usuario creado

    C->>AL: AuditLog::log('create', ...)
    AL->>DB: INSERT INTO audit_logs
    DB-->>AL: Log registrado

    C-->>U: Redirección con éxito

    Note over C,AL: Proceso similar para update/delete
```

## Diagrama de Secuencia - Login Google OAuth

```mermaid
sequenceDiagram
    participant U as Usuario
    participant App as Aplicación
    participant G as Google OAuth
    participant DB as Base de Datos
    participant AL as AuditLog

    U->>App: Click "Login con Google"
    App->>G: Redirect to Google OAuth
    G->>U: Google Login Form
    U->>G: Credenciales Google
    G->>App: Callback con token
    App->>G: Exchange token por user info
    G-->>App: Google User Data

    App->>DB: SELECT * FROM users WHERE email = ?
    DB-->>App: Usuario encontrado/no encontrado

    alt Usuario no pre-registrado
        App->>AL: AuditLog::log('login_google_denied')
        AL->>DB: INSERT audit log
        App-->>U: Acceso denegado
    else Usuario pre-registrado
        alt Primera vez con Google
            App->>DB: UPDATE users SET google_id = ?
            DB-->>App: Usuario actualizado
            App->>AL: AuditLog::log('google_account_linked')
        else Google ID ya vinculado
            App->>App: Validar Google ID coincide
        end

        App->>App: Auth::login(user)
        App->>AL: AuditLog::log('login_google')
        AL->>DB: INSERT audit log
        App-->>U: Redirección según rol
    end
```

## Diagrama de Componentes - Controladores

```mermaid
classDiagram
    class Controller {
        +middleware()
        +validate()
    }

    class UserController {
        +index()
        +create()
        +store()
        +show()
        +edit()
        +update()
        +destroy()
        +forcePasswordChange()
        +resetPassword()
    }

    class AuditLogController {
        +index()
        +show()
    }

    class LoginController {
        +login()
        +showChangePasswordForm()
        +changePassword()
        +redirectBasedOnRole()
    }

    class GoogleController {
        +redirectToGoogle()
        +handleGoogleCallback()
        +redirectBasedOnRole()
    }

    Controller <|-- UserController
    Controller <|-- AuditLogController
    Controller <|-- LoginController
    Controller <|-- GoogleController
```

## Diagrama de Estados - Usuario

```mermaid
stateDiagram-v2
    [*] --> NoRegistrado

    NoRegistrado --> RegistradoLocal: Crear cuenta local
    NoRegistrado --> RegistradoGoogle: Crear cuenta Google

    RegistradoLocal --> AutenticadoLocal: Login exitoso
    RegistradoGoogle --> AutenticadoGoogle: Login exitoso

    AutenticadoLocal --> CambioPasswordObligatorio: must_change_password = true
    AutenticadoGoogle --> CambioPasswordObligatorio: must_change_password = true

    CambioPasswordObligatorio --> PasswordCambiada: Cambio exitoso

    AutenticadoLocal --> Activo: Sesión válida
    AutenticadoGoogle --> Activo: Sesión válida
    PasswordCambiada --> Activo: Sesión válida

    Activo --> SesionExpirada: 30 minutos inactivo
    SesionExpirada --> [*]

    Activo --> Logout: Usuario logout
    Logout --> [*]

    Activo --> Bloqueado: Acceso denegado
    Bloqueado --> [*]
```

## Diagrama de Despliegue

```mermaid
graph TB
    subgraph "Desarrollo Local"
        VSCode[VSCode<br/>Windows]
        PHP_Local[PHP 8.3<br/>Local]
        MySQL_Local[MySQL/MariaDB<br/>Local]
        Git_Local[Git Local]
    end

    subgraph "Control de Versiones"
        GitHub[GitHub<br/>Repository]
    end

    subgraph "Producción - Ubuntu Server"
        Apache[Apache/Nginx]
        PHP_Prod[PHP-FPM 8.3]
        MySQL_Prod[MySQL/MariaDB]
        Laravel_Prod[Laravel App<br/>/var/www/html/]
        User_Web[www-data]
    end

    VSCode --> PHP_Local
    PHP_Local --> MySQL_Local
    VSCode --> Git_Local
    Git_Local --> GitHub
    GitHub --> Apache
    Apache --> PHP_Prod
    PHP_Prod --> Laravel_Prod
    Laravel_Prod --> MySQL_Prod
    Laravel_Prod --> User_Web
```

## Diagrama de Seguridad - Capas de Protección

```mermaid
flowchart TD
    subgraph "Capa de Red"
        HTTPS[HTTPS/SSL]
        Firewall[Firewall]
        RateLimit[Rate Limiting]
    end

    subgraph "Capa de Aplicación"
        CSRF[CSRF Protection]
        Validation[Input Validation]
        Sanitization[Data Sanitization]
    end

    subgraph "Capa de Autenticación"
        Session[Session Management<br/>30 min timeout]
        OAuth[Google OAuth<br/>Pre-registered only]
        Password[Password Hashing<br/>bcrypt]
    end

    subgraph "Capa de Autorización"
        Middleware[Role Middleware]
        Permissions[Permission Checks]
        ACL[Access Control Lists]
    end

    subgraph "Capa de Auditoría"
        AuditLogs[Complete Audit Trail]
        IPTracking[IP Address Logging]
        ChangeTracking[Before/After Values]
    end

    HTTPS --> CSRF
    Firewall --> Validation
    RateLimit --> Sanitization

    CSRF --> Session
    Validation --> OAuth
    Sanitization --> Password

    Session --> Middleware
    OAuth --> Permissions
    Password --> ACL

    Middleware --> AuditLogs
    Permissions --> IPTracking
    ACL --> ChangeTracking