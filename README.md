```markdown
# 📚 Mini Library Management System

[![GitHub repo](https://img.shields.io/badge/GitHub-Repo-blue?logo=github)](https://github.com/jhoan2706/mini-library-laravel)
[![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-orange?logo=mysql)](https://mysql.com)

Sistema de gestión bibliotecaria construido con **Laravel 12**, diseñado para cubrir los requisitos del desafío: gestión de libros, préstamos/devoluciones, búsqueda y control de usuarios con roles y permisos.

---

## ✨ Características

### 📌 Mínimas (requeridas)
- ✅ **CRUD de libros**: Título, autor, género, ISBN, sinopsis, año de publicación y tags
- ✅ **Copias físicas**: Cada libro puede tener múltiples copias con código de barras único
- ✅ **Check-out / Check-in**: Préstamo y devolución de ejemplares con control de disponibilidad
- ✅ **Búsqueda**: Por título, autor o género en el dashboard
- ✅ **Autenticación**: Login/Register manual con roles y permisos

### 🚀 Extras implementados
- ✅ **API REST versionada** (`/api/v1`) con endpoints para libros y préstamos
- ✅ **Imágenes de portada**: Subida y visualización de portadas para cada libro
- ✅ **Historial de préstamos**: Vista con filtros por estado y búsqueda
- ✅ **Reseñas y calificaciones**: Los miembros pueden calificar y reseñar libros al devolverlos
- ✅ **Roles y permisos granulares**: Admin, Librarian, Member
- ✅ **Tests automatizados**: Suite de pruebas con Pest
- ✅ **UI responsive**: Diseño limpio con Bootstrap 5

---

## 🛠️ Requisitos previos

| Herramienta | Versión | Instalación |
|-------------|---------|-------------|
| **PHP** | 8.2+ | [Descargar](https://www.php.net/downloads) |
| **Composer** | Última | [Descargar](https://getcomposer.org/) |
| **MySQL** | 8.0+ | Ver opciones abajo |
| **Node.js** | 18+ | [Descargar](https://nodejs.org/) |
| **Git** | Última | [Descargar](https://git-scm.com/) |

### 🗄️ Opciones para MySQL

| Sistema operativo | Recomendación | Comando |
|-------------------|---------------|---------|
| **Windows** | [XAMPP](https://www.apachefriends.org/) | Instalador gráfico |
| **macOS** | MAMP / Homebrew | `brew install mysql` |
| **Linux (Ubuntu/Debian)** | MySQL Server | `sudo apt install mysql-server` |
| **Linux (Fedora/RHEL)** | MySQL Server | `sudo dnf install mysql-server` |

> **ℹ️ No es necesario crear la base de datos manualmente.** El script `database/create-database.php` la creará automáticamente durante la instalación.

---

## 🚀 Instalación

### 1. Clona el repositorio

```bash
git clone https://github.com/jhoan2706/mini-library-laravel.git
cd mini-library-laravel
```

### 2. Configura el archivo `.env`

```bash
cp .env.example .env
```

**Variables principales (ajústalas según tu entorno):**

| Variable | Valor | Descripción |
|----------|-------|-------------|
| `DB_DATABASE` | `mini_library` | Nombre de la base de datos |
| `DB_USERNAME` | `root` | Usuario de MySQL |
| `DB_PASSWORD` | (vacío en XAMPP) | Contraseña de MySQL |
| `HOME` | `/dashboard` | Redirección post-login |
| `QUEUE_CONNECTION` | `database` | Driver de colas |

### 3. ⚡ Instalación automática (UN SOLO COMANDO)

```bash
composer setup
```

**Este comando ejecuta automáticamente:**

| Paso | Acción |
|------|--------|
| 1️⃣ | `composer install` - Instala dependencias PHP |
| 2️⃣ | Crea `.env` si no existe |
| 3️⃣ | Genera `APP_KEY` |
| 4️⃣ | **Crea la base de datos** (si no existe) |
| 5️⃣ | Publica configuración de Spatie/Permission |
| 6️⃣ | Ejecuta migraciones (crea tablas) |
| 7️⃣ | Crea roles y permisos |
| 8️⃣ | Crea 15 libros de prueba con copias |
| 9️⃣ | Crea enlace simbólico para imágenes |
| 🔟 | Instala dependencias NPM |
| 1️⃣1️⃣ | Compila assets con Vite |

### 4. Inicia el servidor

```bash
php artisan serve
```

### 5. Accede a la aplicación

```
http://localhost:8000
```

---

## 👤 Primer usuario

| Usuario | Rol | Cómo obtenerlo |
|---------|-----|----------------|
| **Primer registro** | 👑 **Administrador** | El primer usuario que se registre será admin automáticamente |
| **Registros posteriores** | 👤 **Miembro** | Los siguientes usuarios tendrán rol member |

**Flujo de registro:**
1. Ve a `/register`
2. Completa el formulario
3. El primer usuario registrado tendrá todos los permisos

---

## 👥 Matriz de permisos

| Acción | Admin | Librarian | Member |
|--------|-------|-----------|--------|
| Ver catálogo / buscar | ✅ | ✅ | ✅ |
| Crear / editar / eliminar libros | ✅ | ✅ | ❌ |
| Prestar (checkout) | ✅ (a cualquiera) | ✅ (a cualquiera) | ✅ (solo para sí mismo) |
| Devolver (checkin) | ✅ (cualquiera) | ✅ (cualquiera) | ✅ (solo sus préstamos) |
| Ver historial de préstamos | ✅ (todos) | ✅ (todos) | ✅ (solo los suyos) |
| Gestionar roles | ✅ | ❌ | ❌ |

---

## 🧪 Tests

```bash
php artisan test
```

**Resultado esperado:** 9 tests pasando ✅

---

## 📡 API REST

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/books` | Listar libros |
| POST | `/api/v1/books` | Crear libro |
| GET | `/api/v1/books/{id}` | Ver libro |
| PUT | `/api/v1/books/{id}` | Actualizar libro |
| DELETE | `/api/v1/books/{id}` | Eliminar libro |
| POST | `/api/v1/copies/{copy}/check-out` | Prestar copia |
| POST | `/api/v1/loans/{loan}/check-in` | Devolver préstamo |

> ℹ️ **Autenticación requerida:** Todas las rutas API requieren token de Sanctum.

---

## 🧱 Stack tecnológico

| Capa | Tecnología |
|------|------------|
| **Framework** | Laravel 12 |
| **PHP** | 8.2+ |
| **Base de datos** | MySQL 8 / MariaDB |
| **Testing** | Pest |
| **Roles/Permisos** | Spatie Permission |
| **Frontend** | Bootstrap 5, Livewire, Vite |
| **Colas** | Database driver (sin Redis) |
| **Autenticación** | Manual (AuthController) |

---

## 🐛 Solución de problemas

### ❌ Error de conexión a MySQL

```bash
# Verificar que MySQL está corriendo
# Windows (XAMPP): Panel de Control → Start MySQL
# Linux: sudo systemctl status mysql
# macOS: brew services list | grep mysql
```

### ❌ Error `storage:link already exists`

```bash
# Eliminar el enlace existente y recrearlo
rm -rf public/storage
php artisan storage:link
```

### ❌ Error de permisos en Linux/macOS

```bash
chmod -R 777 storage bootstrap/cache
```

### ❌ Error al ejecutar `composer setup`

```bash
# Verificar que MySQL está corriendo
# Verificar credenciales en .env
cat .env | grep DB_
```

---

## 📝 Notas adicionales

- La autenticación es manual y utiliza `AuthController`
- Las imágenes se guardan en `storage/app/public/books`
- La aplicación incluye endpoints API versionados
- El primer usuario registrado siempre será administrador

---

## 📄 Licencia

MIT License

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request.

---

**Desarrollado por** [Gonzalo Gutierrez](https://github.com/jhoan2706)
```