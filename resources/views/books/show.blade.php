@extends('layouts.app')

@section('title', 'Detalle del Libro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- BOTÓN VOLVER ARRIBA A LA DERECHA -->
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                ← Volver
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="h4 mb-0">{{ $book->title }}</h2>
                <p class="text-muted mb-0">por {{ $book->author }}</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- IMAGEN DEL LIBRO -->
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <img src="{{ $book->getCoverImageUrl() }}"
                                    alt="{{ $book->title }}"
                                    class="img-fluid rounded-top"
                                    style="width: 100%; min-height: 400px; max-height: 550px; object-fit: cover; border-radius: 12px 12px 0 0;">
                            </div>
                            <div class="card-footer bg-white text-center border-0">
                                <span class="badge bg-secondary">{{ $book->copies->count() }} copias</span>
                                <span class="badge bg-success">{{ $book->availableCopiesCount() }} disponibles</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <p><strong>ISBN:</strong> {{ $book->isbn ?? 'N/A' }}</p>
                        <p><strong>Género:</strong> {{ $book->genre ?? 'N/A' }}</p>
                        <p><strong>Año:</strong> {{ $book->published_at ?? 'N/A' }}</p>
                        <p><strong>Tags:</strong> {{ implode(', ', $book->tags ?? []) }}</p>
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
                        @php
                        $activeLoan = $copy->loans->where('status', 'active')->first();
                        $isBorrowedByMe = $activeLoan && auth()->id() === $activeLoan->user_id;
                        $isBorrowedByOther = $activeLoan && auth()->id() !== $activeLoan->user_id;
                        @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong>Código:</strong> {{ $copy->barcode }}
                            </span>
                            <span>
                                @if($isBorrowedByMe)
                                <span class="badge bg-warning">Prestado a ti</span>
                                <span class="badge bg-info">Devuelve antes: {{ $activeLoan->due_date->format('d/m/Y') }}</span>
                                @elseif($isBorrowedByOther)
                                <span class="badge bg-danger">Prestado a {{ $activeLoan->user->name }}</span>
                                @else
                                <span class="badge bg-success">Disponible</span>
                                @endif
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection