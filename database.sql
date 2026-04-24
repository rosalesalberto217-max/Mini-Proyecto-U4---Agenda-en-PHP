-- =====================================================
-- Script SQL para la Agenda de Contactos
-- Crear la base de datos y la tabla de contactos
-- =====================================================

CREATE DATABASE IF NOT EXISTS agenda_contactos
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_contactos;

-- Tabla principal de contactos
CREATE TABLE IF NOT EXISTS contactos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    apellido    VARCHAR(100) NOT NULL,
    telefono    VARCHAR(20) NOT NULL,
    foto        VARCHAR(255) NOT NULL,       -- Ruta del archivo de imagen
    email       VARCHAR(150) DEFAULT NULL,   -- Opcional
    direccion   TEXT DEFAULT NULL,            -- Opcional
    notas       TEXT DEFAULT NULL,            -- Opcional
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
