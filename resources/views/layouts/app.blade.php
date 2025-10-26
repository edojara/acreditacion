<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    @yield('styles')
</head>
<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container-fluid">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="{{ route('educational-entities.index') }}" class="navbar-brand">
                            <i class="fas fa-university mr-2"></i>
                            <strong>Sistema de Acreditación</strong>
                        </a>
                    </li>

                    <!-- Navigation Menu -->
                    <li class="nav-item">
                        <a href="{{ route('educational-entities.index') }}" class="nav-link {{ request()->routeIs('educational-entities.*') ? 'active' : '' }}">
                            <i class="fas fa-university"></i> Instituciones
                        </a>
                    </li>

                    @if(auth()->user()->role->name === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('entity-contacts.index') }}" class="nav-link {{ request()->routeIs('entity-contacts.*') ? 'active' : '' }}">
                                <i class="fas fa-address-book"></i> Contactos
                            </a>
                        </li>
                    @endif

                    @if(in_array(auth()->user()->role->name, ['admin', 'report']))
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                    @endif

                    @if(in_array(auth()->user()->role->name, ['admin', 'enroller']))
                        <li class="nav-item">
                            <a href="{{ route('enrollments.index') }}" class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap"></i> Inscripciones
                            </a>
                        </li>
                    @endif
                </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user"></i>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-angle-down ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                            <i class="fas fa-user-edit mr-2"></i> Mi Perfil
                        </a>
                        @if(auth()->user()->role->name === 'admin')
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('users.index') }}" class="dropdown-item">
                                <i class="fas fa-users mr-2"></i> Gestionar Usuarios
                            </a>
                            <a href="{{ route('educational-entities.index') }}" class="dropdown-item">
                                <i class="fas fa-university mr-2"></i> Instituciones
                            </a>
                            <a href="{{ route('entity-contacts.index') }}" class="dropdown-item">
                                <i class="fas fa-address-book mr-2"></i> Contactos
                            </a>
                            <a href="{{ route('audit-logs.index') }}" class="dropdown-item">
                                <i class="fas fa-history mr-2"></i> Logs de Auditoría
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('educational-entities.index') }}">Inicio</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>&copy; 2025 <a href="#">Sistema de Acreditación</a>.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    @yield('scripts')
</body>
</html>