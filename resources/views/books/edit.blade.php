@extends('layouts.app')

@section('title', 'Edit Book - Mini Library')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                ← Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Editar libro</h1>

                <!-- FORMULARIO PRINCIPAL -->
                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" id="edit-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input name="title" value="{{ old('title', $book->title) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autor</label>
                        <input name="author" value="{{ old('author', $book->author) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Género</label>
                        <input name="genre" value="{{ old('genre', $book->genre) }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Año de publicación</label>
                        <input type="number" name="published_at" value="{{ old('published_at', $book->published_at) }}" class="form-control" placeholder="Ej: 2020">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input name="isbn" value="{{ old('isbn', $book->isbn) }}" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sinopsis</label>
                        <textarea name="synopsis" rows="4" class="form-control">{{ old('synopsis', $book->synopsis) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tags (separados por coma)</label>
                        <input type="text" name="tags"
                            value="{{ old('tags', is_array($book->tags) ? implode(', ', $book->tags) : '') }}"
                            class="form-control"
                            placeholder="Ej: clásico, best-seller">
                    </div>

                    <!-- AGREGAR COPIAS ADICIONALES -->
                    <div class="mb-3">
                        <label class="form-label">Agregar copias adicionales</label>
                        <input type="number" name="additional_copies" value="0" class="form-control" min="0" max="20">
                        <small class="text-muted">Cantidad de nuevas copias a añadir</small>
                    </div>

                    <!-- IMAGEN -->
                    <div class="mb-3">
                        <label class="form-label">Portada</label>
                        @if($book->cover_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" style="max-height: 100px; border-radius: 4px;">
                        </div>
                        @endif
                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, WEBP. Máximo 2MB</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </form>

                <!-- SECCIÓN DE COPIAS (FUERA DEL FORMULARIO PRINCIPAL) -->
                <div class="mt-4">
                    <h5>Copias existentes</h5>
                    <ul class="list-group">
                        @foreach($book->copies as $copy)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Código: {{ $copy->barcode }}</span>
                            <span>
                                @if($copy->loans->where('status', 'active')->isEmpty())
                                    <span class="badge bg-success">Disponible</span>
                                    <form method="POST" action="{{ route('copies.destroy', $copy) }}" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta copia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-danger">Prestado</span>
                                @endif
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    <small class="text-muted">Total: {{ $book->copies->count() }} copias</small>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection