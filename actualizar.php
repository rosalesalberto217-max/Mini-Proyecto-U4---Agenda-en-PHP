<?php
// =====================================================
// actualizar.php - Procesa la actualización de un contacto (UPDATE lógica)
// Validación robusta del servidor + subida segura de imagen
// =====================================================

// Solo acepta método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once 'config/db.php';

// ---- VALIDACIÓN DEL ID ----
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Verificar que el contacto existe
try {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $contactoExistente = $stmt->fetch();

    if (!$contactoExistente) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al verificar contacto: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

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

// Dirección y notas (opcionales)
$direccion = trim($_POST['direccion'] ?? '');
if (mb_strlen($direccion) > 500) {
    $errores[] = 'La dirección no puede exceder 500 caracteres.';
}

$notas = trim($_POST['notas'] ?? '');
if (mb_strlen($notas) > 1000) {
    $errores[] = 'Las notas no pueden exceder 1000 caracteres.';
}

// ---- MANEJO DE LA FOTO ----

// Mantener la foto existente por defecto
$fotoRuta = $contactoExistente['foto'];
$fotoAntigua = null;

// Si se subió una nueva foto, validarla
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = 'Error al subir la nueva foto. Intente nuevamente.';
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
        $tamanoMaximo = 5 * 1024 * 1024;
        if ($foto['size'] > $tamanoMaximo) {
            $errores[] = 'La foto no debe exceder 5 MB.';
        }

        // Si no hay errores, guardar la nueva foto
        if (empty($errores)) {
            $dirUploads = 'uploads/';
            if (!is_dir($dirUploads)) {
                mkdir($dirUploads, 0755, true);
            }

            $nombreArchivo = uniqid('contact_', true) . '.' . $extension;
            $nuevaRuta = $dirUploads . $nombreArchivo;

            if (move_uploaded_file($foto['tmp_name'], $nuevaRuta)) {
                // Guardar referencia de la foto antigua para eliminarla después
                $fotoAntigua = $fotoRuta;
                $fotoRuta = $nuevaRuta;
            } else {
                $errores[] = 'No se pudo guardar la nueva foto.';
            }
        }
    }
}

// ---- SI HAY ERRORES, REDIRIGIR ----

if (!empty($errores)) {
    $errorMsg = implode(' | ', $errores);
    header("Location: editar.php?id=$id&error=" . urlencode($errorMsg));
    exit;
}

// ---- ACTUALIZAR EN BASE DE DATOS ----

try {
    $sql = "UPDATE contactos SET 
                nombre = :nombre, 
                apellido = :apellido, 
                telefono = :telefono, 
                foto = :foto, 
                email = :email, 
                direccion = :direccion, 
                notas = :notas 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre'    => $nombre,
        'apellido'  => $apellido,
        'telefono'  => $telefono,
        'foto'      => $fotoRuta,
        'email'     => !empty($email) ? $email : null,
        'direccion' => !empty($direccion) ? $direccion : null,
        'notas'     => !empty($notas) ? $notas : null,
        'id'        => $id,
    ]);

    // Eliminar la foto antigua si se reemplazó exitosamente
    if ($fotoAntigua && file_exists($fotoAntigua)) {
        unlink($fotoAntigua);
    }

    header('Location: index.php?msg=actualizado');
    exit;

} catch (PDOException $e) {
    error_log("Error al actualizar contacto: " . $e->getMessage());

    // Si se subió nueva foto pero falló el UPDATE, eliminarla
    if ($fotoAntigua && $fotoRuta !== $contactoExistente['foto'] && file_exists($fotoRuta)) {
        unlink($fotoRuta);
    }

    header("Location: editar.php?id=$id&error=" . urlencode('Error al actualizar el contacto. Intente nuevamente.'));
    exit;
}
