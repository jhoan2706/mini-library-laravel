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
                <form method="POST" action="{{ url('/books') }}" class="mt-3"> @csrf
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
                                <!-- Título con enlace al detalle -->
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                                    <div>
                                        <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-dark">
                                            <h3 class="h6 mb-1">{{ $book->title }}</h3>
                                            <p class="mb-1 text-muted">{{ $book->author }} · {{ $book->genre ?? 'Sin género' }}</p>
                                        </a>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary">{{ $book->copies->count() }} copias</span>
                                        <p class="mb-0 small text-muted">Disponibles: {{ $book->availableCopiesCount() }}</p>
                                    </div>
                                </div>

                                <!-- Sinopsis -->
                                <p class="mb-3 text-muted">{{ $book->synopsis ?: 'Sin sinopsis.' }}</p>

                                <!-- ✅ MOSTRAR PRÉSTAMOS ACTIVOS -->
                                @php
                                $activeLoans = $book->copies->flatMap(fn($c) => $c->loans->where('status', 'active'));
                                $activeCount = $activeLoans->count();
                                @endphp

                                @if($activeCount > 0)
                                <div class="mt-2 mb-3">
                                    @if(auth()->user()->hasRole(['admin', 'librarian']))
                                    <span class="badge bg-warning">Prestado a:</span>
                                    @foreach($activeLoans as $loan)
                                    <span class="badge bg-info">{{ $loan->user->name }}</span>
                                    @endforeach
                                    @else
                                    <span class="badge bg-warning">Copias prestadas:</span>
                                    <span class="badge bg-info">{{ $activeCount }}</span>
                                    @endif
                                </div>
                                @endif

                                <!-- Botones de acción -->
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

                                    <!-- CHECKOUT (PRESTAR) -->
                                    @can('loans.checkout')
                                    @php
                                    $hasAvailableCopy = $book->copies->some(fn($c) => $c->loans->where('status', 'active')->isEmpty());

                                    // ✅ Verificar si el usuario ya tiene un préstamo activo de este libro
                                    $userHasLoanForThisBook = auth()->user()->loans()
                                    ->where('status', 'active')
                                    ->whereHas('copy', fn($q) => $q->where('book_id', $book->id))
                                    ->exists();
                                    @endphp

                                    @if($hasAvailableCopy && !$userHasLoanForThisBook)
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <form method="POST" action="{{ route('books.checkout', $book) }}" class="d-inline">
                                            @csrf
                                            @if(auth()->user()->hasRole(['admin', 'librarian']))
                                            <select name="user_id" class="form-select form-select-sm d-inline-block w-auto" required style="max-width: 150px;">
                                                <option value="">Seleccionar...</option>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-outline-success btn-sm">Prestar</button>
                                            @else
                                            <button type="submit" class="btn btn-outline-success btn-sm">Prestar para mí</button>
                                            @endif
                                        </form>
                                    </div>
                                    @elseif($userHasLoanForThisBook)
                                    <span class="badge bg-warning">Ya tienes este libro prestado</span>
                                    @elseif(!$hasAvailableCopy)
                                    <span class="badge bg-secondary">Sin copias disponibles</span>
                                    @endif
                                    @endcan

                                    <!-- CHECKIN (DEVOLVER) -->
                                    @foreach($activeLoans as $loan)
                                    @can('loans.checkin', $loan)
                                    <form method="POST" action="{{ route('loans.checkin', $loan) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning btn-sm">
                                            @if(auth()->user()->hasRole(['admin', 'librarian']))
                                            Devolver ({{ $loan->user->name }})
                                            @else
                                            Devolver
                                            @endif
                                        </button>
                                    </form>
                                    @endcan
                                    @endforeach

                                    <!-- BOTÓN VER DETALLE -->
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-info btn-sm">
                                        Ver detalle
                                    </a>
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