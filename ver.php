<?php
// =====================================================
// ver.php - Detalle de un contacto (READ individual)
// Se sanitizan todas las salidas con htmlspecialchars()
// =====================================================

require_once 'config/db.php';

// Validar que se recibe un ID válido (entero positivo)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener el contacto con sentencia preparada
try {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $contacto = $stmt->fetch();

    if (!$contacto) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Error al ver contacto: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

$titulo = htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellido']) . ' - Agenda de Contactos';

include 'includes/header.php';
include 'includes/menu.php';
?>

<div class="main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Botón de regreso -->
                <div class="mb-4 fade-in-up">
                    <a href="index.php" class="btn btn-outline-glass">
                        <i class="bi bi-arrow-left me-1"></i>Volver al listado
                    </a>
                </div>

                <!-- Tarjeta de Detalle -->
                <div class="detail-card fade-in-up fade-in-up-delay-1">
                    <div class="row g-0">
                        <!-- Foto -->
                        <div class="col-md-5 p-4 d-flex align-items-center justify-content-center">
                            <img src="<?php echo htmlspecialchars($contacto['foto']); ?>" 
                                 alt="Foto de <?php echo htmlspecialchars($contacto['nombre']); ?>"
                                 class="detail-photo"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($contacto['nombre'] . ' ' . $contacto['apellido']); ?>&size=400&background=667eea&color=fff&bold=true&font-size=0.4'">
                        </div>

                        <!-- Información -->
                        <div class="col-md-7">
                            <div class="detail-info">
                                <h1 class="detail-name">
                                    <?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellido']); ?>
                                </h1>
                                
                                <span class="badge-glass mb-4 d-inline-block">
                                    <i class="bi bi-person me-1"></i>Contacto #<?php echo (int)$contacto['id']; ?>
                                </span>

                                <!-- Teléfono -->
                                <div class="mt-4">
                                    <div class="info-label">
                                        <i class="bi bi-telephone me-1"></i>Teléfono
                                    </div>
                                    <div class="info-value">
                                        <a href="tel:<?php echo htmlspecialchars($contacto['telefono']); ?>" 
                                           class="text-decoration-none" style="color: #667eea;">
                                            <?php echo htmlspecialchars($contacto['telefono']); ?>
                                        </a>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <div class="info-label">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </div>
                                    <div class="info-value">
                                        <?php if (!empty($contacto['email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($contacto['email']); ?>" 
                                               class="text-decoration-none" style="color: #667eea;">
                                                <?php echo htmlspecialchars($contacto['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">No especificado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Dirección -->
                                <div>
                                    <div class="info-label">
                                        <i class="bi bi-geo-alt me-1"></i>Dirección
                                    </div>
                                    <div class="info-value">
                                        <?php if (!empty($contacto['direccion'])): ?>
                                            <?php echo htmlspecialchars($contacto['direccion']); ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">No especificada</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div>
                                    <div class="info-label">
                                        <i class="bi bi-sticky me-1"></i>Notas
                                    </div>
                                    <div class="info-value">
                                        <?php if (!empty($contacto['notas'])): ?>
                                            <?php echo nl2br(htmlspecialchars($contacto['notas'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Sin notas</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Fechas -->
                                <div>
                                    <div class="info-label">
                                        <i class="bi bi-calendar me-1"></i>Registrado
                                    </div>
                                    <div class="info-value mb-0">
                                        <?php echo date('d/m/Y H:i', strtotime($contacto['created_at'])); ?>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex gap-2 mt-4">
                                    <a href="editar.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-glow-warning">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </a>
                                    <a href="eliminar.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-glow-danger">
                                        <i class="bi bi-trash me-1"></i>Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
