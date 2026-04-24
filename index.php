<?php
// =====================================================
// index.php - Listado de contactos (READ)
// Muestra todos los contactos con búsqueda
// Todas las salidas se sanitizan con htmlspecialchars()
// =====================================================

require_once 'config/db.php';

$titulo = 'Inicio - Agenda de Contactos';

// Búsqueda de contactos (si se envía el parámetro)
$busqueda = '';
if (isset($_GET['buscar'])) {
    $busqueda = trim($_GET['buscar']);
}

try {
    if (!empty($busqueda)) {
        // Sentencia preparada para búsqueda segura
        $sql = "SELECT * FROM contactos 
                WHERE nombre LIKE :busqueda 
                OR apellido LIKE :busqueda 
                OR telefono LIKE :busqueda 
                OR email LIKE :busqueda 
                ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['busqueda' => "%$busqueda%"]);
    } else {
        $sql = "SELECT * FROM contactos ORDER BY nombre ASC";
        $stmt = $pdo->query($sql);
    }
    $contactos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al listar contactos: " . $e->getMessage());
    $contactos = [];
    $error = "Error al cargar los contactos.";
}

// Contar total de contactos
try {
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM contactos");
    $totalContactos = $totalStmt->fetch()['total'];
} catch (PDOException $e) {
    $totalContactos = 0;
}

include 'includes/header.php';
include 'includes/menu.php';
?>

<div class="main-container">
    <div class="container">
        <!-- Encabezado de página -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 fade-in-up">
            <div>
                <h1 class="page-title"><i class="bi bi-people-fill me-2"></i>Mis Contactos</h1>
                <p class="page-subtitle">Gestiona tu agenda de contactos de forma segura</p>
            </div>
            <a href="crear.php" class="btn btn-glow-primary mt-3 mt-md-0">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Contacto
            </a>
        </div>

        <!-- Barra de Estadísticas -->
        <div class="stats-bar fade-in-up fade-in-up-delay-1">
            <div class="stat-item">
                <div class="stat-number"><?php echo $totalContactos; ?></div>
                <div class="stat-label">Total Contactos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count($contactos); ?></div>
                <div class="stat-label">Mostrando</div>
            </div>
        </div>

        <!-- Buscador -->
        <div class="mb-4 fade-in-up fade-in-up-delay-2">
            <form method="GET" action="index.php">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="form-control" name="buscar" 
                           placeholder="Buscar por nombre, apellido, teléfono o email..."
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                </div>
            </form>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-glass alert-glass-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <?php
            $msg = $_GET['msg'];
            $alertClass = 'alert-glass-success';
            $icon = 'bi-check-circle';
            $mensaje = '';

            switch ($msg) {
                case 'creado':
                    $mensaje = '¡Contacto creado exitosamente!';
                    break;
                case 'actualizado':
                    $mensaje = '¡Contacto actualizado exitosamente!';
                    break;
                case 'eliminado':
                    $mensaje = '¡Contacto eliminado exitosamente!';
                    $alertClass = 'alert-glass-warning';
                    $icon = 'bi-trash';
                    break;
                default:
                    $mensaje = '';
            }
            ?>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-glass <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                    <i class="bi <?php echo $icon; ?> me-2"></i><?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Listado de Contactos -->
        <?php if (empty($contactos)): ?>
            <div class="empty-state fade-in-up fade-in-up-delay-3">
                <div class="empty-icon"><i class="bi bi-person-x"></i></div>
                <h4>No hay contactos</h4>
                <p>
                    <?php if (!empty($busqueda)): ?>
                        No se encontraron resultados para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
                    <?php else: ?>
                        Comienza agregando tu primer contacto
                    <?php endif; ?>
                </p>
                <?php if (!empty($busqueda)): ?>
                    <a href="index.php" class="btn btn-outline-glass">
                        <i class="bi bi-arrow-left me-1"></i>Ver todos
                    </a>
                <?php else: ?>
                    <a href="crear.php" class="btn btn-glow-primary">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Contacto
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($contactos as $index => $contacto): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 fade-in-up fade-in-up-delay-<?php echo min($index + 1, 4); ?>">
                        <div class="glass-card contact-card h-100">
                            <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="text-decoration-none">
                                <div class="card-img-wrapper">
                                    <?php
                                    // Validar que la foto existe, si no, mostrar placeholder
                                    $fotoPath = htmlspecialchars($contacto['foto']);
                                    ?>
                                    <img src="<?php echo $fotoPath; ?>" 
                                         alt="Foto de <?php echo htmlspecialchars($contacto['nombre']); ?>"
                                         class="card-img-top"
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($contacto['nombre'] . ' ' . $contacto['apellido']); ?>&size=200&background=667eea&color=fff&bold=true'">
                                </div>
                                <div class="card-body">
                                    <div class="contact-name">
                                        <?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellido']); ?>
                                    </div>
                                    <div class="contact-phone">
                                        <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($contacto['telefono']); ?>
                                    </div>
                                    <?php if (!empty($contacto['email'])): ?>
                                        <div class="contact-email mt-1">
                                            <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($contacto['email']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="card-actions">
                                <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-glow-info btn-sm">
                                    <i class="bi bi-eye me-1"></i>Ver
                                </a>
                                <a href="editar.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-glow-warning btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <a href="eliminar.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-glow-danger btn-sm">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
