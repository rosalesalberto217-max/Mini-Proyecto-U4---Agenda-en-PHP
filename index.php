<?php
require_once("config/db.php");
include("includes/header.php");
include("includes/menu.php");

$busqueda = $_GET['buscar'] ?? '';

if($busqueda){
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE nombre LIKE ? OR apellido LIKE ?");
    $stmt->execute(["%$busqueda%", "%$busqueda%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM contactos");
    $stmt->execute();
}

$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
<h1>Agenda</h1>

<form method="GET">
    <input type="text" name="buscar" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
    <button>Buscar</button>
</form>

<?php foreach($contactos as $c): ?>
<div class="card">
    <img src="<?= htmlspecialchars($c['foto']) ?>" width="100"><br><br>
    <strong><?= htmlspecialchars($c['nombre']) ?> <?= htmlspecialchars($c['apellido']) ?></strong><br>
    📞 <?= htmlspecialchars($c['telefono']) ?><br><br>

    <a href="ver.php?id=<?= $c['id'] ?>"><button>Ver</button></a>
    <a href="editar.php?id=<?= $c['id'] ?>"><button>Editar</button></a>
    <a href="eliminar.php?id=<?= $c['id'] ?>"><button>Eliminar</button></a>
</div>
<?php endforeach; ?>

</div>

<?php include("includes/footer.php"); ?>