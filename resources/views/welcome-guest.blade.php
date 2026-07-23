@extends('layouts.app')

@section('title', 'Bienvenido - Mini Library')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3 mb-3">Bienvenido a Mini Library</h1>
                <p class="text-muted mb-4">Para acceder al panel de gestión de libros, ingresa con tu cuenta o crea una nueva.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('/login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-secondary">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
