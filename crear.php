<?php
// =====================================================
// crear.php - Formulario de creación de contacto (CREATE vista)
// Formulario con validación HTML5 en el cliente
// =====================================================

$titulo = 'Nuevo Contacto - Agenda de Contactos';

include 'includes/header.php';
include 'includes/menu.php';
?>

<div class="main-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <!-- Encabezado -->
                <div class="mb-4 fade-in-up">
                    <a href="index.php" class="btn btn-outline-glass mb-3">
                        <i class="bi bi-arrow-left me-1"></i>Volver al listado
                    </a>
                    <h1 class="page-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Contacto</h1>
                    <p class="page-subtitle">Completa el formulario para agregar un nuevo contacto</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-glass alert-glass-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Creación -->
                <div class="glass-form fade-in-up fade-in-up-delay-1">
                    <form action="guardar.php" method="POST" enctype="multipart/form-data" novalidate id="formCrear">
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       required minlength="2" maxlength="100"
                                       placeholder="Ej: Juan"
                                       value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
                                <div class="invalid-feedback">El nombre es obligatorio (mín. 2 caracteres).</div>
                            </div>

                            <!-- Apellido -->
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">
                                    Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="apellido" name="apellido" 
                                       required minlength="2" maxlength="100"
                                       placeholder="Ej: Pérez"
                                       value="<?php echo isset($_GET['apellido']) ? htmlspecialchars($_GET['apellido']) : ''; ?>">
                                <div class="invalid-feedback">El apellido es obligatorio (mín. 2 caracteres).</div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">
                                    Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       required minlength="7" maxlength="20"
                                       placeholder="Ej: +52 123 456 7890"
                                       pattern="[\+\d\s\-\(\)]{7,20}"
                                       value="<?php echo isset($_GET['telefono']) ? htmlspecialchars($_GET['telefono']) : ''; ?>">
                                <div class="invalid-feedback">Ingresa un teléfono válido (mín. 7 caracteres).</div>
                            </div>

                            <!-- Email (opcional) -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email <span class="badge-glass">Opcional</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       maxlength="150"
                                       placeholder="Ej: juan@correo.com"
                                       value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                                <div class="invalid-feedback">Ingresa un email válido.</div>
                            </div>

                            <!-- Foto -->
                            <div class="col-12">
                                <label for="foto" class="form-label">
                                    Foto <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="foto" name="foto" 
                                       required accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">
                                    Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5 MB.
                                </div>
                                <div class="invalid-feedback">La foto es obligatoria.</div>
                                <!-- Previsualización -->
                                <div class="mt-3">
                                    <div class="img-preview-container" id="previewContainer">
                                        <div class="img-preview-placeholder">
                                            <i class="bi bi-camera"></i>
                                            Vista previa
                                        </div>
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
                                       placeholder="Ej: Calle Principal #123, Col. Centro"
                                       value="<?php echo isset($_GET['direccion']) ? htmlspecialchars($_GET['direccion']) : ''; ?>">
                            </div>

                            <!-- Notas (opcional) -->
                            <div class="col-12">
                                <label for="notas" class="form-label">
                                    Notas <span class="badge-glass">Opcional</span>
                                </label>
                                <textarea class="form-control" id="notas" name="notas" rows="3" 
                                          maxlength="1000"
                                          placeholder="Notas adicionales sobre este contacto..."><?php echo isset($_GET['notas']) ? htmlspecialchars($_GET['notas']) : ''; ?></textarea>
                            </div>

                            <!-- Botones -->
                            <div class="col-12 d-flex gap-3 mt-4">
                                <button type="submit" class="btn btn-glow-success">
                                    <i class="bi bi-check-lg me-1"></i>Guardar Contacto
                                </button>
                                <a href="index.php" class="btn btn-outline-glass">
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

<!-- Script de validación del cliente y previsualización de imagen -->
<script>
// Validación del formulario en el cliente (HTML5 + Bootstrap)
(function () {
    'use strict';
    const form = document.getElementById('formCrear');
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();

// Previsualización de imagen seleccionada
document.getElementById('foto').addEventListener('change', function(e) {
    const container = document.getElementById('previewContainer');
    const file = e.target.files[0];
    
    if (file) {
        // Validar tipo de archivo en el cliente
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            container.innerHTML = '<div class="img-preview-placeholder"><i class="bi bi-exclamation-triangle text-danger"></i>Formato no válido</div>';
            return;
        }
        
        // Validar tamaño (5 MB)
        if (file.size > 5 * 1024 * 1024) {
            container.innerHTML = '<div class="img-preview-placeholder"><i class="bi bi-exclamation-triangle text-danger"></i>Archivo muy grande</div>';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            container.innerHTML = '<img src="' + event.target.result + '" alt="Vista previa">';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
