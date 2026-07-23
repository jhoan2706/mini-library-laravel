@extends('layouts.app')

@section('title', 'Dashboard - Mini Library')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-2">Dashboard</h1>
        <p>Bienvenido, {{ auth()->user()->name }}</p>
        <p>Rol: {{ auth()->user()->roles->pluck('name')->join(', ') }}</p>
    </div>
</div>

<div class="mb-4 rounded-4 bg-white p-4 shadow-sm">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
        <div>
            <p class="text-uppercase text-secondary small mb-2">Dashboard</p>
            <h1 class="h2 mb-2">Panel de gestión</h1>
            <p class="mb-0 text-muted">Aquí puedes ver tus libros activos, crear nuevas entradas y revisar estadísticas de préstamos.</p>
        </div>
        <span class="badge rounded-pill bg-primary fs-6">Usuario autenticado</span>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 card-title">Agregar libro</h2>
                <p class="text-muted small">Registra un nuevo libro y crea sus copias automáticamente.</p>

                @can('books.create')
                    <form method="POST" action="{{ route('books.store') }}" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input name="title" value="{{ old('title') }}" required class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Autor</label>
                            <input name="author" value="{{ old('author') }}" required class="form-control" />
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Género</label>
                                <input name="genre" value="{{ old('genre') }}" class="form-control" />
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Copias</label>
                                <input type="number" name="copies_count" value="{{ old('copies_count', 1) }}" min="1" max="20" class="form-control" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ISBN</label>
                            <input name="isbn" value="{{ old('isbn') }}" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sinopsis</label>
                            <textarea name="synopsis" rows="4" class="form-control">{{ old('synopsis') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                @else
                    <div class="alert alert-secondary p-3 mb-0">
                        No tienes permiso para crear libros.
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-3">
                    <div>
                        <h2 class="h5 mb-0">Libros recientes</h2>
                        <p class="text-muted small mb-0">Revisa los títulos y el estado de las copias registradas.</p>
                    </div>
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
                        <input name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Buscar título, autor o género">
                        <button type="submit" class="btn btn-secondary btn-sm">Buscar</button>
                    </form>
                </div>

                @if ($books->isEmpty())
                    <div class="border rounded-3 border-dashed border-secondary p-4 text-center text-secondary">
                        No hay libros registrados todavía.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($books as $book)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                                            <div>
                                                <h3 class="h6 mb-1">{{ $book->title }}</h3>
                                                <p class="mb-1 text-muted">{{ $book->author }} · {{ $book->genre ?? 'Sin género' }}</p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-secondary">{{ $book->copies->count() }} copias</span>
                                                <p class="mb-0 small text-muted">Disponibles: {{ $book->availableCopiesCount() }}</p>
                                            </div>
                                        </div>
                                        <p class="mb-3 text-muted">{{ $book->synopsis ?: 'Sin sinopsis.' }}</p>

                                        <div class="d-flex flex-wrap gap-2">
                                            @can('books.update')
                                                <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                            @endcan

                                            @can('books.delete')
                                                <form method="POST" action="{{ route('books.destroy', $book) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                                </form>
                                            @endcan

                                            @can('loans.checkout')
                                                <form method="POST" action="{{ route('books.checkout', $book) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm">Prestar copia</button>
                                                </form>
                                            @endcan

                                            @can('loans.checkin')
                                                @foreach ($book->copies as $copy)
                                                    @foreach ($copy->loans->where('status', 'active') as $loan)
                                                        <form method="POST" action="{{ route('loans.checkin', $loan) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning btn-sm">Devolver {{ $copy->barcode }}</button>
                                                        </form>
                                                    @endforeach
                                                @endforeach
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $books->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
