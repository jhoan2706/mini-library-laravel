@extends('layouts.app')

@section('title', 'Edit Book - Mini Library')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Editar libro</h1>

                <form method="POST" action="{{ route('books.update', $book) }}">
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
                        <label class="form-label">ISBN</label>
                        <input name="isbn" value="{{ old('isbn', $book->isbn) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sinopsis</label>
                        <textarea name="synopsis" rows="4" class="form-control">{{ old('synopsis', $book->synopsis) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
