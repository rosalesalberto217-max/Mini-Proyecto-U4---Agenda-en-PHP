<?php
// =====================================================
// eliminar.php - Confirmación y proceso de eliminación (DELETE)
// La eliminación se realiza por POST con confirmación previa
// para evitar borrados accidentales o ataques por URL
// =====================================================

require_once 'config/db.php';

// ---- PROCESAR ELIMINACIÓN (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        header('Location: index.php');
        exit;
    }

    try {
        // Obtener la ruta de la foto antes de eliminar
        $stmt = $pdo->prepare("SELECT foto FROM contactos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $contacto = $stmt->fetch();

        if (!$contacto) {
            header('Location: index.php');
            exit;
        }

        // Eliminar el contacto de la base de datos
        $stmtDelete = $pdo->prepare("DELETE FROM contactos WHERE id = :id");
        $stmtDelete->execute(['id' => $id]);

        // Eliminar la foto del servidor si existe
        if (!empty($contacto['foto']) && file_exists($contacto['foto'])) {
            unlink($contacto['foto']);
        }

        header('Location: index.php?msg=eliminado');
        exit;

    } catch (PDOException $e) {
        error_log("Error al eliminar contacto: " . $e->getMessage());
        header('Location: index.php');
        exit;
    }
}

// ---- MOSTRAR CONFIRMACIÓN (GET) ----

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener datos del contacto para mostrar en la confirmación
try {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $contacto = $stmt->fetch();

    if (!$contacto) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al cargar contacto para eliminar: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

$titulo = 'Eliminar Contacto - Agenda de Contactos';

include 'includes/header.php';
include 'includes/menu.php';
?>

<div class="main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <!-- Botón de regreso -->
                <div class="mb-4 fade-in-up">
                    <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-outline-glass">
                        <i class="bi bi-arrow-left me-1"></i>Volver al contacto
                    </a>
                </div>

                <!-- Confirmación de Eliminación -->
                <div class="delete-confirm-card fade-in-up fade-in-up-delay-1">
                    <div class="delete-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    
                    <h3>¿Eliminar este contacto?</h3>
                    <p>Esta acción no se puede deshacer. Se eliminará permanentemente el contacto y su foto asociada.</p>

                    <!-- Info del contacto a eliminar -->
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <img src="<?php echo htmlspecialchars($contacto['foto']); ?>" 
                             alt="Foto" class="contact-avatar" style="width:60px;height:60px;"
                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($contacto['nombre']); ?>&size=60&background=667eea&color=fff'">
                        <div class="text-start">
                            <strong><?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellido']); ?></strong>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($contacto['telefono']); ?>
                            </small>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-3 justify-content-center">
                        <!-- Formulario POST para la eliminación segura -->
                        <form method="POST" action="eliminar.php">
                            <input type="hidden" name="id" value="<?php echo (int)$contacto['id']; ?>">
                            <button type="submit" class="btn btn-glow-danger">
                                <i class="bi bi-trash me-1"></i>Sí, eliminar
                            </button>
                        </form>
                        <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-outline-glass">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
