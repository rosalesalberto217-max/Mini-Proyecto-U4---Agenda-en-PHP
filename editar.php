<?php
require_once("config/db.php");
include("includes/header.php");
include("includes/menu.php");

$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id=?");
$stmt->execute([$_GET['id']]);
$c = $stmt->fetch();
?>

<form action="actualizar.php" method="POST">
<input type="hidden" name="id" value="<?= $c['id'] ?>">
<input name="nombre" value="<?= $c['nombre'] ?>"><br>
<input name="apellido" value="<?= $c['apellido'] ?>"><br>
<input name="telefono" value="<?= $c['telefono'] ?>"><br>
<button>Actualizar</button>
</form>

<?php include("includes/footer.php"); ?>