<?php

// database/create-database.php
// Crea la base de datos si no existe, leyendo las credenciales del .env.
// Así quien evalúa el proyecto no necesita abrir phpMyAdmin ni escribir SQL a mano.
// Requisito único: que XAMPP tenga Apache y MySQL encendidos (Panel de Control > Start).

$env = parse_ini_file(__DIR__.'/../.env');

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? 'mini_library';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de datos '{$database}' creada/verificada correctamente.\n";
} catch (PDOException $e) {
    echo "❌ No se pudo conectar a MySQL. Verifica que en el Panel de Control de XAMPP\n";
    echo "   los servicios 'MySQL' digan 'Running' (botón Start si no).\n";
    echo '   Detalle técnico: '.$e->getMessage()."\n";
    exit(1);
}
