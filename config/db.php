<?php
// =====================================================
// Conexión a la base de datos usando PDO
// Se usa exclusivamente PDO con sentencias preparadas
// para prevenir SQL Injection.
// =====================================================

$host     = 'localhost';
$dbname   = 'agenda_contactos';
$username = 'root';
$password = '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Devolver arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Desactivar emulación para mayor seguridad
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // No mostrar errores internos de SQL al usuario final
    error_log("Error de conexión: " . $e->getMessage());
    die("Error al conectar con la base de datos. Por favor, inténtelo más tarde.");
}
