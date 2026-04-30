<?php
require_once("config/db.php");
include("includes/header.php");
include("includes/menu.php");

$stmt = $pdo->prepare("SELECT * FROM contactos WHERE id=?");
$stmt->execute([$_GET['id']]);
$c = $stmt->fetch();
?>

<h2><?= htmlspecialchars($c['nombre']) ?></h2>
<img src="<?= $c['foto'] ?>" width="150"><br>
<p><?= htmlspecialchars($c['telefono']) ?></p>

<?php include("includes/footer.php"); ?>