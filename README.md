<div align="center">

# 📚 Mini Library Management System

Sistema de gestión bibliotecaria desarrollado con **Laravel 12** que permite administrar libros, copias físicas y préstamos mediante una interfaz moderna y una API REST.

<p>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/Licencia-MIT-success?style=for-the-badge)](LICENSE)

</p>

<p>
<a href="https://github.com/jhoan2706/mini-library-laravel">Repositorio</a>
·
<a href="#-instalación">Instalación</a>
·
<a href="#-api">API</a>
·
<a href="#-pruebas">Pruebas</a>
</p>

</div>

---

# ✨ Características

## Funcionalidades principales

- 📚 CRUD completo de libros
- 📖 Múltiples copias físicas por libro
- 🔄 Préstamo y devolución de libros (Check-out / Check-in)
- 🔍 Búsqueda por título, autor y género
- 👤 Autenticación con roles y permisos

## Funcionalidades adicionales

- 🚀 API REST (`/api/v1`)
- 🖼️ Carga de portadas para libros
- ⭐ Sistema de reseñas y calificaciones
- 📜 Historial de préstamos
- 🔐 Roles y permisos con Spatie
- ✅ Pruebas automatizadas con Pest
- 📱 Interfaz responsive con Bootstrap 5

---

# 🛠️ Tecnologías utilizadas

| Tecnología | Versión |
|------------|---------|
| Laravel | 12 |
| PHP | 8.2+ |
| MySQL / MariaDB | 8+ |
| Bootstrap | 5 |
| Livewire | Última |
| Pest | Última |
| Spatie Permission | Última |
| Vite | Última |

---

# 📋 Requisitos

- PHP 8.2 o superior
- Composer
- MySQL 8 o superior
- Node.js 18 o superior
- Git

---

# 🚀 Instalación

Clona el repositorio

```bash
git clone https://github.com/jhoan2706/mini-library-laravel.git
cd mini-library-laravel
```

Copia el archivo de configuración

```bash
cp .env.example .env
```

Configura las credenciales de la base de datos en el archivo `.env`

```env
DB_DATABASE=mini_library
DB_USERNAME=root
DB_PASSWORD=
```

Ejecuta la instalación automática

```bash
composer setup
```

Inicia el servidor de desarrollo

```bash
php artisan serve
```

Abre la aplicación en

```
http://localhost:8000
```

---

# ⚡ ¿Qué hace `composer setup`?

- Instala las dependencias de Composer
- Crea el archivo `.env` si no existe
- Genera la clave de la aplicación
- Crea automáticamente la base de datos
- Ejecuta las migraciones
- Inserta datos de prueba
- Crea los roles y permisos
- Instala las dependencias de NPM
- Compila los assets
- Crea el enlace simbólico para el almacenamiento

Todo queda listo con un solo comando.

---

# 👤 Usuarios

El **primer usuario** que se registre será automáticamente **Administrador**.

Todos los registros posteriores tendrán el rol de **Miembro**.

---

# 🔐 Roles y permisos

| Funcionalidad | Administrador | Bibliotecario | Miembro |
|--------------|:-------------:|:-------------:|:--------:|
| Ver catálogo | ✅ | ✅ | ✅ |
| Gestionar libros | ✅ | ✅ | ❌ |
| Realizar préstamos | ✅ | ✅ | Solo propios |
| Realizar devoluciones | ✅ | ✅ | Solo propios |
| Ver historial de préstamos | Todos | Todos | Solo propios |
| Administrar roles | ✅ | ❌ | ❌ |

---

# 📡 API

| Método | Endpoint |
|---------|----------|
| GET | `/api/v1/books` |
| POST | `/api/v1/books` |
| GET | `/api/v1/books/{id}` |
| PUT | `/api/v1/books/{id}` |
| DELETE | `/api/v1/books/{id}` |
| POST | `/api/v1/copies/{copy}/check-out` |
| POST | `/api/v1/loans/{loan}/check-in` |

La autenticación se realiza mediante **Laravel Sanctum**.

---

# 🧪 Pruebas

Ejecuta la suite de pruebas

```bash
php artisan test
```

Resultado esperado

```
PASS

Tests: 9 passed
```

---

# 📁 Estructura del proyecto

```
app/
├── Http/
├── Models/
├── Services/

database/
├── migrations/
├── seeders/

resources/
├── views/
├── js/
├── css/

routes/
├── web.php
├── api.php
```

---

# 🖼️ Capturas de pantalla

Puedes agregar imágenes del proyecto aquí.

```
docs/images/dashboard.png
docs/images/books.png
docs/images/loans.png
```

---

# 🐞 Solución de problemas

### Error de conexión con MySQL

Verifica que el servicio de MySQL esté en ejecución.

### Error `storage:link already exists`

```bash
rm -rf public/storage
php artisan storage:link
```

### Problemas de permisos en Linux

```bash
chmod -R 777 storage bootstrap/cache
```

---

# 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

<div align="center">

Desarrollado por **Gonzalo Gutierrez**

⭐ Si te gustó este proyecto, considera dejar una estrella en el repositorio.

</div>