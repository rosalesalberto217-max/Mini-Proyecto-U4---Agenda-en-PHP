<?php
$host = "sql308.infinityfree.com";
$db   = "if0_41791646_if0_41791646_agenda"; // ← ESTE ES EL BUENO
$user = "if0_41791646";
$pass = "Agenda20262";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión");
}
?>