<?php
// =====================================================
// editar.php - Formulario de edición de contacto (UPDATE vista)
// Carga los datos existentes del contacto para editarlos
// =====================================================

require_once 'config/db.php';

// Validar ID
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
    error_log("Error al cargar contacto para edición: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

$titulo = 'Editar: ' . htmlspecialchars($contacto['nombre']) . ' - Agenda de Contactos';

include 'includes/header.php';
include 'includes/menu.php';
?>

<div class="main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <!-- Encabezado -->
                <div class="mb-4 fade-in-up">
                    <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-outline-glass mb-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver al contacto
                    </a>
                    <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i>Editar Contacto</h1>
                    <p class="page-subtitle">
                        Editando: <strong><?php echo htmlspecialchars($contacto['nombre'] . ' ' . $contacto['apellido']); ?></strong>
                    </p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-glass alert-glass-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Edición -->
                <div class="glass-form fade-in-up fade-in-up-delay-1">
                    <form action="actualizar.php" method="POST" enctype="multipart/form-data" novalidate id="formEditar">
                        <!-- ID oculto -->
                        <input type="hidden" name="id" value="<?php echo (int)$contacto['id']; ?>">

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       required minlength="2" maxlength="100"
                                       value="<?php echo htmlspecialchars($contacto['nombre']); ?>">
                                <div class="invalid-feedback">El nombre es obligatorio (mín. 2 caracteres).</div>
                            </div>

                            <!-- Apellido -->
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">
                                    Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="apellido" name="apellido" 
                                       required minlength="2" maxlength="100"
                                       value="<?php echo htmlspecialchars($contacto['apellido']); ?>">
                                <div class="invalid-feedback">El apellido es obligatorio (mín. 2 caracteres).</div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">
                                    Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       required minlength="7" maxlength="20"
                                       pattern="[\+\d\s\-\(\)]{7,20}"
                                       value="<?php echo htmlspecialchars($contacto['telefono']); ?>">
                                <div class="invalid-feedback">Ingresa un teléfono válido (mín. 7 caracteres).</div>
                            </div>

                            <!-- Email (opcional) -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="badge-glass">Opcional</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       maxlength="150"
                                       value="<?php echo htmlspecialchars($contacto['email'] ?? ''); ?>">
                                <div class="invalid-feedback">Ingresa un email válido.</div>
                            </div>

                            <!-- Foto -->
                            <div class="col-12">
                                <label for="foto" class="form-label">
                                    Foto <span class="badge-glass">Opcional - Solo si desea cambiarla</span>
                                </label>
                                
                                <!-- Foto actual -->
                                <div class="mb-3 d-flex align-items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($contacto['foto']); ?>" 
                                         alt="Foto actual" class="current-photo"
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($contacto['nombre']); ?>&size=120&background=667eea&color=fff'">
                                    <div>
                                        <span class="form-text d-block">Foto actual</span>
                                        <small class="text-muted"><?php echo htmlspecialchars(basename($contacto['foto'])); ?></small>
                                    </div>
                                </div>

                                <input type="file" class="form-control" id="foto" name="foto" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">
                                    Deje vacío para mantener la foto actual. Formatos: JPG, PNG, GIF, WEBP. Máx: 5 MB.
                                </div>

                                <!-- Previsualización nueva foto -->
                                <div class="mt-3">
                                    <div class="img-preview-container" id="previewContainer" style="display:none;">
                                    </div>
                                </div>
                            </div>

                            <!-- Dirección (opcional) -->
                            <div class="col-12">
                                <label for="direccion" class="form-label">
                                    Dirección <span class="badge-glass">Opcional</span>
                                </label>
                                <input type="text" class="form-control" id="direccion" name="direccion" 
                                       maxlength="500"
                                       value="<?php echo htmlspecialchars($contacto['direccion'] ?? ''); ?>">
                            </div>

                            <!-- Notas (opcional) -->
                            <div class="col-12">
                                <label for="notas" class="form-label">
                                    Notas <span class="badge-glass">Opcional</span>
                                </label>
                                <textarea class="form-control" id="notas" name="notas" rows="3" 
                                          maxlength="1000"><?php echo htmlspecialchars($contacto['notas'] ?? ''); ?></textarea>
                            </div>

                            <!-- Botones -->
                            <div class="col-12 d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-glow-success">
                                    <i class="bi bi-check-lg me-1"></i>Actualizar Contacto
                                </button>
                                <a href="ver.php?id=<?php echo (int)$contacto['id']; ?>" class="btn btn-outline-glass">
                                    <i class="bi bi-x-lg me-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de validación y previsualización -->
<script>
(function () {
    'use strict';
    const form = document.getElementById('formEditar');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();

// Previsualización de nueva imagen
document.getElementById('foto').addEventListener('change', function(e) {
    const container = document.getElementById('previewContainer');
    const file = e.target.files[0];
    
    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            container.style.display = 'flex';
            container.innerHTML = '<div class="img-preview-placeholder"><i class="bi bi-exclamation-triangle text-danger"></i>Formato no válido</div>';
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            container.style.display = 'flex';
            container.innerHTML = '<div class="img-preview-placeholder"><i class="bi bi-exclamation-triangle text-danger"></i>Archivo muy grande</div>';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            container.style.display = 'flex';
            container.innerHTML = '<img src="' + event.target.result + '" alt="Nueva foto">';
        };
        reader.readAsDataURL(file);
    } else {
        container.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
