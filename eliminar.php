<?php
require_once("config/db.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $stmt = $pdo->prepare("DELETE FROM contactos WHERE id=?");
    $stmt->execute([$_POST['id']]);
    header("Location: index.php");
}
?>

<form method="POST" onsubmit="return confirm('¿Eliminar contacto?');">
<input type="hidden" name="id" value="<?= $_GET['id'] ?>">
<button>Eliminar</button>
</form>