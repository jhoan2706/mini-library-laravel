# Mini Library Management System (Laravel)

Sistema de gestión de biblioteca construido con Laravel 11, con un enfoque práctico para cubrir los requisitos mínimos de la prueba: gestión de libros, préstamos/devoluciones, búsqueda y un panel simple para uso diario.

## ✅ Funcionalidades implementadas

### Requeridas
- ✅ CRUD de libros con título, autor, ISBN, género, sinopsis y metadatos básicos
- ✅ Gestión de copias físicas por libro
- ✅ Check-out y check-in de ejemplares con control de disponibilidad
- ✅ Búsqueda por título, autor y género

### Extras incluidos
- ✅ Autenticación y roles con permisos básicos para distinguir usuarios de la biblioteca
- ✅ API REST versionada en `/api/v1`
- ✅ Integración de IA para sugerir metadatos de libros
- ✅ Pruebas automatizadas con Pest

## ▶️ Cómo correrlo localmente

1. Clona el repositorio.
2. Ejecuta el setup de Composer:

```bash
composer setup
```

3. Inicia la aplicación:

```bash
php artisan serve
```

4. Abre la URL:

```text
http://localhost:8000
```

## 🧪 Tests

```bash
php artisan test
```

## 🧱 Stack

- Laravel 11
- PHP
- MySQL
- Pest
- Sanctum / Fortify
- Spatie Permission
- IA para sugerencias de metadatos

## 📝 Notas

El proyecto incluye un panel web simple para registrar libros y una API REST para las operaciones principales de la biblioteca.
