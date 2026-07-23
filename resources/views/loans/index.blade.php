@extends('layouts.app')

@section('title', 'Historial de Préstamos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">📋 Historial de Préstamos</h2>
                <span class="badge bg-secondary">{{ $loans->total() }} préstamos</span>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('loans.index') }}" class="row g-3 mb-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Devueltos</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Vencidos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Buscar libro o autor...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Libro</th>
                                <th>Autor</th>
                                <th>Usuario</th>
                                <th>Préstamo</th>
                                <th>Devolución</th>
                                <th>Estado</th>
                                <th>Calificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $loan->copy->book) }}" class="text-decoration-none">
                                        {{ $loan->copy->book->title }}
                                    </a>
                                </td>
                                <td>{{ $loan->copy->book->author }}</td>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->checked_out_at->format('d/m/Y') }}</td>
                                <td>{{ $loan->checked_in_at ? $loan->checked_in_at->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($loan->status === 'active')
                                        <span class="badge bg-warning">Activo</span>
                                    @elseif($loan->status === 'returned')
                                        <span class="badge bg-success">Devuelto</span>
                                    @else
                                        <span class="badge bg-danger">Vencido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->rating)
                                        {{ str_repeat('⭐', $loan->rating) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->review)
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $loan->id }}">
                                            Ver reseña
                                        </button>
                                    @else
                                        <span class="text-muted">Sin reseña</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay préstamos registrados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN CON BOOTSTRAP 5 -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $loans->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales para reseñas -->
@foreach($loans as $loan)
    @if($loan->review)
    <div class="modal fade" id="reviewModal{{ $loan->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📝 Reseña del libro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Libro:</strong> {{ $loan->copy->book->title }}</p>
                    <p><strong>Usuario:</strong> {{ $loan->user->name }}</p>
                    <p><strong>Calificación:</strong> {{ str_repeat('⭐', $loan->rating ?? 0) }}</p>
                    <hr>
                    <p><strong>Reseña:</strong></p>
                    <p class="text-muted">{{ $loan->review }}</p>
                    @if($loan->observation)
                        <p><strong>Observación:</strong></p>
                        <p class="text-muted">{{ $loan->observation }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection