@extends('layouts.app')

@section('title', 'Devolver libro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="h5 mb-0">📖 Devolver libro</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p><strong>Libro:</strong> {{ $loan->copy->book->title }}</p>
                    <p><strong>Autor:</strong> {{ $loan->copy->book->author }}</p>
                    <p><strong>Código:</strong> {{ $loan->copy->barcode }}</p>
                    <p><strong>Fecha préstamo:</strong> {{ $loan->checked_out_at->format('d/m/Y') }}</p>
                    <p><strong>Fecha devolución:</strong> {{ $loan->due_date->format('d/m/Y') }}</p>
                </div>

                <form method="POST" action="{{ route('loans.checkin.store', $loan) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Reseña <span class="text-muted">(opcional)</span></label>
                        <textarea name="review" rows="3" class="form-control" placeholder="¿Qué te pareció el libro?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observación <span class="text-muted">(opcional)</span></label>
                        <textarea name="observation" rows="2" class="form-control" placeholder="Estado del libro, observaciones..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calificación</label>
                        <select name="rating" class="form-select">
                            <option value="">Sin calificar</option>
                            <option value="1">⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="5">⭐⭐⭐⭐⭐</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Confirmar devolución</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection