@extends('layouts.app')

@section('title', 'Bienvenido - Mini Library')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Hero Section -->
        <div class="text-center mb-5">
            <div class="mb-3">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">📚 Gestión Bibliotecaria</span>
            </div>
            <h1 class="display-3 fw-bold text-dark">Mini Library</h1>
            <p class="lead text-muted mb-3">Organiza, presta y gestiona tus libros de forma sencilla.</p>
            <p class="text-muted mb-4">Sistema completo para bibliotecas personales o comunitarias.</p>
        </div>

        <!-- Características -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-3 hover-shadow transition">
                    <div class="card-body">
                        <div class="display-6 mb-3">📖</div>
                        <h3 class="h5 mb-2">Gestión de libros</h3>
                        <p class="text-muted small mb-0">Añade, edita y elimina libros con metadatos completos: título, autor, ISBN, género y más.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-3 hover-shadow transition">
                    <div class="card-body">
                        <div class="display-6 mb-3">🔄</div>
                        <h3 class="h5 mb-2">Préstamos y devoluciones</h3>
                        <p class="text-muted small mb-0">Controla el check-out y check-in de copias, con historial completo y reseñas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-center p-3 hover-shadow transition">
                    <div class="card-body">
                        <div class="display-6 mb-3">👥</div>
                        <h3 class="h5 mb-2">Roles y permisos</h3>
                        <p class="text-muted small mb-0">Administradores, bibliotecarios y miembros con permisos diferenciados.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        @php
        $totalBooks = \App\Models\Book::count();
        $totalCopies = \App\Models\Copy::count();
        $totalLoans = \App\Models\Loan::where('status', 'active')->count();
        @endphp

        <div class="row g-3 mb-5">
            <div class="col-4 text-center">
                <div class="bg-light rounded-3 p-3">
                    <span class="display-6 fw-bold text-primary">{{ $totalBooks }}</span>
                    <p class="text-muted small mb-0">Libros</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="bg-light rounded-3 p-3">
                    <span class="display-6 fw-bold text-success">{{ $totalCopies }}</span>
                    <p class="text-muted small mb-0">Copias</p>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="bg-light rounded-3 p-3">
                    <span class="display-6 fw-bold text-warning">{{ $totalLoans }}</span>
                    <p class="text-muted small mb-0">Préstamos activos</p>
                </div>
            </div>
        </div>

        <!-- Últimos libros agregados -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">📖 Últimos libros agregados</h2>
                <span class="badge bg-secondary">{{ $recentBooks->count() }} libros</span>
            </div>
            <div class="card-body">
                @php
                $recentBooks = $recentBooks ?? collect();
                @endphp

                @if($recentBooks->isEmpty())
                <div class="text-center text-muted py-4">
                    <p>No hay libros registrados todavía.</p>
                    <p class="small">Regístrate y comienza a agregar libros.</p>
                </div>
                @else
                <div class="row g-4">
                    @foreach($recentBooks as $book)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                            <div class="card-body">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $book->getCoverImageUrl() }}"
                                            alt="{{ $book->title }}"
                                            style="width: 80px; height: 110px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    </div>
                                    <div>
                                        <h3 class="h6 mb-1">{{ Str::limit($book->title, 30) }}</h3>
                                        <p class="text-muted small mb-1">{{ $book->author }}</p>
                                        <span class="badge bg-secondary">{{ $book->copies->count() }} copias</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Llamada a la acción -->
        <div class="text-center mt-5">
            <p class="text-muted">¿Quieres gestionar tu propia biblioteca?</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="bi bi-person-plus"></i> Crear cuenta
                </a>
            </div>
        </div>
    </div>
</div>
@endsection