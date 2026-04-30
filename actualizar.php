<?php
require_once("config/db.php");

$stmt = $pdo->prepare("UPDATE contactos SET nombre=?, apellido=?, telefono=? WHERE id=?");

$stmt->execute([
$_POST['nombre'],
$_POST['apellido'],
$_POST['telefono'],
$_POST['id']
]);

header("Location: index.php");