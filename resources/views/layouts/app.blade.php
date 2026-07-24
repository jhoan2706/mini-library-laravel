<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📚 @yield('title', 'Mini Library')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: #f8fafc;
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">📚 Mini Library</a> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">🏠 Dashboard</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('loans.index') }}">📋 Préstamos/Devoluciones</a>
                    </li>
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuButton">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @can('admin-only')
                            <li><a class="dropdown-item" href="{{ route('admin.roles') }}">⚙️ Asignar roles</a></li>
                            @endcan
                            <li>
                                <form method="POST" action="{{ url('/logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">🚪 Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ url('/login') }}" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
                    <a href="{{ url('/register') }}" class="btn btn-light btn-sm">Crear cuenta</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-top mt-5 py-3">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <span class="text-muted small">
                    &copy; {{ date('Y') }} <strong>Gonzalo Gutierrez</strong> - Mini Library
                </span>
                <div class="d-flex gap-3">
                    <a href="https://github.com/jhoan2706" target="_blank" class="text-muted text-decoration-none small">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                    <a href="mailto:gonzalo2706@gmail.com" class="text-muted text-decoration-none small">
                        <i class="bi bi-envelope"></i> Contacto
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>