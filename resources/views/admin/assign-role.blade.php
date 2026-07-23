@extends('layouts.app')

@section('title', 'Administración de roles')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h1 class="h4 mb-1">Asignar rol a usuario</h1>
                        <p class="text-muted mb-0">Busca un usuario por email o nombre y asigna el rol correcto.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="GET" action="{{ route('admin.roles') }}" class="row g-3 mb-4">
                    <div class="col-md-9">
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Buscar usuario por email o nombre">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">Buscar usuario</button>
                    </div>
                </form>

                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol actual</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->getRoleNames()->join(', ') ?: 'Sin rol' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.roles', ['search' => $user->email]) }}" class="btn btn-sm btn-outline-primary">Seleccionar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay usuarios para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $users->links() }}

                @if(request('search') && $users->count() === 1)
                    <div class="border rounded-3 p-4 mb-4 bg-light">
                        <h2 class="h5">Usuario encontrado</h2>
                        <p class="mb-1"><strong>Nombre:</strong> {{ $users->first()->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $users->first()->email }}</p>
                        <p class="mb-0"><strong>Rol actual:</strong> {{ $users->first()->getRoleNames()->join(', ') ?: 'Sin rol' }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.assign-role') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $users->first()->email }}">

                        <div class="mb-3">
                            <label class="form-label">Seleccionar rol</label>
                            <select name="role" class="form-select" required>
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Asignar rol</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
