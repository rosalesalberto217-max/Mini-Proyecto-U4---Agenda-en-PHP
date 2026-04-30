<?php
require_once("config/db.php");

if(empty($_POST['telefono'])) die("Teléfono obligatorio");

$ruta = "uploads/" . time() . "_" . $_FILES['foto']['name'];
move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);

$stmt = $pdo->prepare("INSERT INTO contactos 
(nombre, apellido, telefono, foto, email, direccion, notas)
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
$_POST['nombre'],
$_POST['apellido'],
$_POST['telefono'],
$ruta,
$_POST['email'],
$_POST['direccion'],
$_POST['notas']
]);

header("Location: index.php");