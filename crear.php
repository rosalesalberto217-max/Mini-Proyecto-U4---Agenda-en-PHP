<?php include("includes/header.php"); include("includes/menu.php"); ?>

<form action="guardar.php" method="POST" enctype="multipart/form-data">
<input name="nombre" required placeholder="Nombre"><br>
<input name="apellido" required placeholder="Apellido"><br>
<input name="telefono" required placeholder="Teléfono"><br>
<input type="file" name="foto" required><br>
<input name="email" type="email" placeholder="Email"><br>
<textarea name="direccion"></textarea><br>
<textarea name="notas"></textarea><br>
<button>Guardar</button>
</form>

<?php include("includes/footer.php"); ?>