@extends('layouts.app')

@section('title', 'Detalle del Libro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="h4 mb-0">{{ $book->title }}</h2>
                <p class="text-muted mb-0">por {{ $book->author }}</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ISBN:</strong> {{ $book->isbn ?? 'N/A' }}</p>
                        <p><strong>Género:</strong> {{ $book->genre ?? 'N/A' }}</p>
                        <p><strong>Año:</strong> {{ $book->published_at ?? 'N/A' }}</p>
                        <p><strong>Tags:</strong> {{ implode(', ', $book->tags ?? []) }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total de copias:</strong> {{ $book->copies->count() }}</p>
                        <p><strong>Copias disponibles:</strong> {{ $book->availableCopiesCount() }}</p>
                        <p><strong>Copias prestadas:</strong> {{ $book->copies->filter(fn($c) => $c->loans->where('status', 'active')->isNotEmpty())->count() }}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Sinopsis</h5>
                    <p class="text-muted">{{ $book->synopsis ?? 'Sin sinopsis disponible.' }}</p>
                </div>

                <div class="mt-3">
                    <h5>Copias</h5>
                    <ul class="list-group">
                        @foreach($book->copies as $copy)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Código: {{ $copy->barcode }}</span>
                                <span>
                                    Estado: 
                                    @if($copy->loans->where('status', 'active')->isEmpty())
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">Prestado</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection