-- =====================================================
-- BASE DE DATOS: Agenda de Contactos
-- =====================================================

CREATE DATABASE IF NOT EXISTS agenda_contactos
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_contactos;

-- =====================================================
-- TABLA: contactos
-- =====================================================

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    foto VARCHAR(255) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    direccion TEXT DEFAULT NULL,
    notas TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- DATOS DE PRUEBA (OPCIONAL)
-- =====================================================

INSERT INTO contactos (nombre, apellido, telefono, foto, email, direccion, notas)
VALUES 
('Juan', 'Pérez', '9611234567', 'juan.jpg', 'juan@email.com', 'Chiapas', 'Amigo de la escuela'),
('Ana', 'Gómez', '9619876543', 'ana.jpg', 'ana@email.com', 'Tuxtla', 'Trabajo'),
('Luis', 'Martínez', '9615558888', 'luis.jpg', NULL, NULL, 'Sin notas');

-- =====================================================
-- ÍNDICES (MEJORA DE RENDIMIENTO)
-- =====================================================

CREATE INDEX idx_nombre ON contactos(nombre);
CREATE INDEX idx_telefono ON contactos(telefono);

-- =====================================================
-- CONSULTA DE PRUEBA
-- =====================================================

SELECT * FROM contactos;