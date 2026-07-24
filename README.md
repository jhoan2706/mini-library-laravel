<div align="center">

# 📚 Mini Library Management System

Sistema de gestión bibliotecaria desarrollado con **Laravel 12** que permite administrar libros, copias físicas y préstamos mediante una interfaz moderna y una API REST.

<p>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-success?style=for-the-badge)](LICENSE)

</p>

<p>
<a href="https://github.com/jhoan2706/mini-library-laravel">Repository</a>
·
<a href="#-installation">Installation</a>
·
<a href="#-api">API</a>
·
<a href="#-testing">Testing</a>
</p>

</div>

---

# ✨ Features

## Required Features

- 📚 Complete CRUD for books
- 📖 Multiple physical copies per book
- 🔄 Book check-out / check-in
- 🔍 Search by title, author and genre
- 👤 Authentication with roles and permissions

## Extra Features

- 🚀 REST API (`/api/v1`)
- 🖼️ Book cover uploads
- ⭐ Reviews and ratings
- 📜 Loan history
- 🔐 Role-based permissions (Spatie)
- ✅ Automated tests with Pest
- 📱 Responsive Bootstrap 5 interface

---

# 🛠️ Tech Stack

| Technology | Version |
|------------|---------|
| Laravel | 12 |
| PHP | 8.2+ |
| MySQL / MariaDB | 8+ |
| Bootstrap | 5 |
| Livewire | Latest |
| Pest | Latest |
| Spatie Permission | Latest |
| Vite | Latest |

---

# 📋 Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js 18+
- Git

---

# 🚀 Installation

Clone the repository

```bash
git clone https://github.com/jhoan2706/mini-library-laravel.git
cd mini-library-laravel
```

Copy the environment file

```bash
cp .env.example .env
```

Configure your database credentials inside `.env`

```env
DB_DATABASE=mini_library
DB_USERNAME=root
DB_PASSWORD=
```

Run the automatic setup

```bash
composer setup
```

Start the development server

```bash
php artisan serve
```

Open

```
http://localhost:8000
```

---

# ⚡ What does `composer setup` do?

- Installs Composer dependencies
- Creates `.env` if necessary
- Generates the application key
- Creates the database automatically
- Runs migrations
- Seeds demo data
- Creates roles and permissions
- Installs NPM packages
- Builds frontend assets
- Creates the storage symbolic link

Everything is ready with a single command.

---

# 👤 Default Users

The first registered account automatically becomes an **Administrator**.

Every subsequent user is registered as a **Member**.

---

# 🔐 Roles & Permissions

| Feature | Admin | Librarian | Member |
|---------|:----:|:---------:|:------:|
| Browse catalog | ✅ | ✅ | ✅ |
| Manage books | ✅ | ✅ | ❌ |
| Check-out books | ✅ | ✅ | Own only |
| Check-in books | ✅ | ✅ | Own only |
| View loan history | All | All | Own |
| Manage roles | ✅ | ❌ | ❌ |

---

# 📡 API

| Method | Endpoint |
|---------|----------|
| GET | `/api/v1/books` |
| POST | `/api/v1/books` |
| GET | `/api/v1/books/{id}` |
| PUT | `/api/v1/books/{id}` |
| DELETE | `/api/v1/books/{id}` |
| POST | `/api/v1/copies/{copy}/check-out` |
| POST | `/api/v1/loans/{loan}/check-in` |

Authentication is handled with **Laravel Sanctum**.

---

# 🧪 Testing

Run the test suite

```bash
php artisan test
```

Expected result

```
PASS

Tests: 9 passed
```

---

# 📁 Project Structure

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

# 🖼️ Screenshots

You can place screenshots here.

```
docs/images/dashboard.png
docs/images/books.png
docs/images/loans.png
```

---

# 🐞 Troubleshooting

### MySQL connection error

Verify MySQL is running.

### Storage link already exists

```bash
rm -rf public/storage
php artisan storage:link
```

### Linux permissions

```bash
chmod -R 777 storage bootstrap/cache
```

---

# 📄 License

This project is licensed under the MIT License.

---

<div align="center">

Developed by **Gonzalo Gutierrez**

⭐ If you liked this project, consider giving it a star.

</div>