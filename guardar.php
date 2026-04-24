<?php
// =====================================================
// guardar.php - Procesa la creación de un contacto (CREATE lógica)
// Validación robusta del servidor + subida segura de imagen
// =====================================================

// Solo acepta método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: crear.php');
    exit;
}

require_once 'config/db.php';

// ---- VALIDACIÓN DEL SERVIDOR ----

$errores = [];

// Validar nombre (obligatorio, mín. 2 caracteres)
$nombre = trim($_POST['nombre'] ?? '');
if (empty($nombre) || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
    $errores[] = 'El nombre es obligatorio y debe tener entre 2 y 100 caracteres.';
}

// Validar apellido (obligatorio, mín. 2 caracteres)
$apellido = trim($_POST['apellido'] ?? '');
if (empty($apellido) || mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
    $errores[] = 'El apellido es obligatorio y debe tener entre 2 y 100 caracteres.';
}

// Validar teléfono (obligatorio, mín. 7 caracteres)
$telefono = trim($_POST['telefono'] ?? '');
if (empty($telefono) || mb_strlen($telefono) < 7 || mb_strlen($telefono) > 20) {
    $errores[] = 'El teléfono es obligatorio y debe tener entre 7 y 20 caracteres.';
}

// Validar email (opcional, pero si se ingresa debe ser válido)
$email = trim($_POST['email'] ?? '');
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El email ingresado no es válido.';
}
if (mb_strlen($email) > 150) {
    $errores[] = 'El email no puede exceder 150 caracteres.';
}

// Dirección y notas (opcionales, solo limitar longitud)
$direccion = trim($_POST['direccion'] ?? '');
if (mb_strlen($direccion) > 500) {
    $errores[] = 'La dirección no puede exceder 500 caracteres.';
}

$notas = trim($_POST['notas'] ?? '');
if (mb_strlen($notas) > 1000) {
    $errores[] = 'Las notas no pueden exceder 1000 caracteres.';
}

// ---- VALIDACIÓN DE LA FOTO (obligatoria) ----

$fotoRuta = '';

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
    $errores[] = 'La foto es obligatoria.';
} elseif ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $errores[] = 'Error al subir la foto. Intente nuevamente.';
} else {
    $foto = $_FILES['foto'];

    // Validar extensión permitida
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensionesPermitidas)) {
        $errores[] = 'Formato de imagen no permitido. Use: JPG, PNG, GIF o WEBP.';
    }

    // Validar tipo MIME real del archivo
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoReal = $finfo->file($foto['tmp_name']);
    
    if (!in_array($tipoReal, $tiposPermitidos)) {
        $errores[] = 'El archivo no es una imagen válida.';
    }

    // Validar tamaño máximo (5 MB)
    $tamanoMaximo = 5 * 1024 * 1024; // 5 MB en bytes
    if ($foto['size'] > $tamanoMaximo) {
        $errores[] = 'La foto no debe exceder 5 MB.';
    }

    // Si no hay errores de foto, proceder a guardar
    if (empty($errores)) {
        // Crear directorio uploads si no existe
        $dirUploads = 'uploads/';
        if (!is_dir($dirUploads)) {
            mkdir($dirUploads, 0755, true);
        }

        // Generar nombre único para evitar colisiones y ataques
        $nombreArchivo = uniqid('contact_', true) . '.' . $extension;
        $fotoRuta = $dirUploads . $nombreArchivo;

        // Mover archivo al directorio de uploads
        if (!move_uploaded_file($foto['tmp_name'], $fotoRuta)) {
            $errores[] = 'No se pudo guardar la foto. Verifique los permisos del servidor.';
        }
    }
}

// ---- SI HAY ERRORES, REDIRIGIR CON MENSAJE ----

if (!empty($errores)) {
    $errorMsg = implode(' | ', $errores);
    // Redirigir al formulario preservando los datos ingresados
    $params = http_build_query([
        'error'     => $errorMsg,
        'nombre'    => $nombre,
        'apellido'  => $apellido,
        'telefono'  => $telefono,
        'email'     => $email,
        'direccion' => $direccion,
        'notas'     => $notas,
    ]);
    header("Location: crear.php?$params");
    exit;
}

// ---- INSERTAR EN BASE DE DATOS ----

try {
    $sql = "INSERT INTO contactos (nombre, apellido, telefono, foto, email, direccion, notas) 
            VALUES (:nombre, :apellido, :telefono, :foto, :email, :direccion, :notas)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre'    => $nombre,
        'apellido'  => $apellido,
        'telefono'  => $telefono,
        'foto'      => $fotoRuta,
        'email'     => !empty($email) ? $email : null,
        'direccion' => !empty($direccion) ? $direccion : null,
        'notas'     => !empty($notas) ? $notas : null,
    ]);

    // Redirigir con mensaje de éxito
    header('Location: index.php?msg=creado');
    exit;

} catch (PDOException $e) {
    // No mostrar error de SQL al usuario
    error_log("Error al insertar contacto: " . $e->getMessage());
    
    // Eliminar la foto subida si falla la inserción
    if (file_exists($fotoRuta)) {
        unlink($fotoRuta);
    }

    header('Location: crear.php?error=Error al guardar el contacto. Intente nuevamente.');
    exit;
}
