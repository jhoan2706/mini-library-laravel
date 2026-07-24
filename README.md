# Mini Library Management System (Laravel)

Sistema de gestión de biblioteca construido con Laravel 12 y autenticación manual, enfocado en cubrir los requisitos mínimos del desafío: gestión de libros, búsqueda, préstamos/devoluciones y gestión de roles.

## ✅ Funcionalidades implementadas

### Requeridas
- ✅ CRUD de libros con título, autor, género, ISBN, sinopsis y copias físicas
- ✅ Registro de nuevas copias al crear libros
- ✅ Check-out / check-in de ejemplares con control de disponibilidad
- ✅ Búsqueda por título, autor o género en el dashboard
- ✅ Autenticación web con login/register manual
- ✅ Roles y permisos con Spatie Permission

### Extras incluidos
- ✅ API REST versionada en `/api/v1`
- ✅ Pruebas automatizadas con Pest
- ✅ Dashboard Bootstrap 5 usable

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

## 🧪 Primer usuario

El primer usuario que se registre desde la interfaz será **administrador** con todos los permisos.

Los usuarios posteriores se registrarán con rol **member** (solo pueden ver catálogo y pedir préstamos).

## 🧪 Tests

```bash
php artisan test
```

## 🧱 Stack

- Laravel 12
- PHP 8.2
- MySQL
- Pest
- Spatie Permission
- Bootstrap 5

## 📝 Notas

- La autenticación es manual y utiliza `AuthController`.
- El dashboard muestra búsqueda, edición, borrado, creación y préstamos según permisos.
- La app incluye endpoints API para libros y préstamos.
