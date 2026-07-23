@extends('layouts.app')

@section('title', 'Mini Library')

@section('content')
    <div class="mb-4 rounded-4 bg-white p-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
            <div>
                <p class="text-uppercase text-secondary small mb-2">Mini Library</p>
                <h1 class="h2 mb-2">Gestiona libros, copias y préstamos</h1>
                <p class="mb-0 text-muted">Un panel compacto para registrar libros, controlar disponibilidad y mostrar un catálogo simple con Bootstrap 5.</p>
            </div>
            <span class="badge rounded-pill bg-success fs-6">Demo listo</span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card library-card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Agregar nuevo libro</h2>
                    <form method="POST" action="/books" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input name="title" required class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Autor</label>
                            <input name="author" required class="form-control" />
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Género</label>
                                <input name="genre" class="form-control" />
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Copias</label>
                                <input type="number" name="copies_count" value="1" min="1" max="20" class="form-control" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ISBN</label>
                            <input name="isbn" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sinopsis</label>
                            <textarea name="synopsis" rows="4" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar libro</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h2 class="h5 mb-0">Catálogo actual</h2>
                            <p class="text-muted small mb-0">{{ isset($books) ? $books->count() : 0 }} libros registrados</p>
                        </div>
                    </div>

                    @if (isset($books) && $books->isEmpty())
                        <div class="border rounded-3 border-dashed border-secondary p-4 text-center text-secondary">
                            No hay libros registrados todavía.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($books as $book)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                                <div>
                                                    <h3 class="h6 mb-1">{{ $book->title }}</h3>
                                                    <p class="mb-1 text-muted">{{ $book->author }} · {{ $book->genre ?? 'Sin género' }}</p>
                                                </div>
                                                <span class="badge bg-secondary align-self-start">{{ $book->copies->count() }} copias</span>
                                            </div>
                                            <p class="mb-0 text-muted">{{ $book->synopsis ?: 'Sin sinopsis.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
