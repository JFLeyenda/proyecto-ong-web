-- Script para crear la base de datos ORGANIZACION
-- Semana 6 - Programación Web 2
-- Para MySQL (XAMPP)

CREATE DATABASE IF NOT EXISTS ORGANIZACION;
USE ORGANIZACION;

-- Crear tabla PROYECTO
CREATE TABLE IF NOT EXISTS PROYECTO (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    presupuesto DECIMAL(15,2),
    fecha_inicio DATE,
    fecha_fin DATE
);

-- Crear tabla DONANTE
CREATE TABLE IF NOT EXISTS DONANTE (
    id_donante INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    direccion VARCHAR(200),
    telefono VARCHAR(20)
);

-- Crear tabla DONACION
CREATE TABLE IF NOT EXISTS DONACION (
    id_donacion INT AUTO_INCREMENT PRIMARY KEY,
    monto DECIMAL(15,2) NOT NULL,
    fecha DATE NOT NULL,
    id_proyecto INT,
    id_donante INT,
    FOREIGN KEY (id_proyecto) REFERENCES PROYECTO(id_proyecto),
    FOREIGN KEY (id_donante) REFERENCES DONANTE(id_donante)
);

-- Insertar proyectos de ejemplo con fechas y presupuestos en pesos chilenos
INSERT INTO PROYECTO (nombre, descripcion, presupuesto, fecha_inicio, fecha_fin) VALUES
('Construcción Escuela Rural', 'Proyecto para construir una escuela en zona rural con necesidades educativas urgentes', 85000000, '2024-08-01', '2025-06-30'),
('Programa de Alimentación Infantil', 'Iniciativa para proporcionar alimentos nutritivos a niños en situación de vulnerabilidad social', 45000000, '2024-09-15', '2025-03-15'),
('Centro de Salud Comunitario', 'Creación de un centro médico básico para atender a la comunidad local de manera integral', 120000000, '2024-10-01', '2025-08-30'),
('Apoyo Digital Educativo', 'Programa de capacitación en tecnologías digitales para estudiantes de escuelas vulnerables', 28000000, '2024-11-01', '2025-05-31'),
('Huertos Comunitarios Sustentables', 'Creación de espacios verdes productivos para fomentar la alimentación saludable', 15000000, '2024-12-01', '2025-09-30');

-- Insertar donantes de ejemplo con datos variados
INSERT INTO DONANTE (nombre, email, direccion, telefono) VALUES
('María Elena González López', 'maria.gonzalez@gmail.com', 'Av. Providencia 1234, Providencia, Santiago', '+56912345678'),
('Juan Carlos Pérez Morales', 'juan.perez@outlook.com', 'Los Olivos 456, Viña del Mar, Valparaíso', '+56987654321'),
('Ana Sofía Martínez Rojas', 'ana.martinez@yahoo.com', 'Calle Barros Arana 789, Concepción', '+56911223344'),
('Roberto Silva Fernández', 'roberto.silva@empresa.cl', 'Las Condes 567, Las Condes, Santiago', '+56923456789'),
('Carmen Torres Mendoza', 'carmen.torres@gmail.com', 'O\'Higgins 890, Rancagua', '+56934567890'),
('Diego Morales Castro', 'diego.morales@hotmail.com', 'Independencia 321, Temuco', '+56945678901');

-- Insertar donaciones de ejemplo (15 donaciones con montos en pesos chilenos)
INSERT INTO DONACION (monto, fecha, id_proyecto, id_donante) VALUES
-- Donaciones recientes (octubre 2024)
(500000, '2024-10-01', 1, 1),
(750000, '2024-10-05', 2, 2),
(1200000, '2024-10-08', 3, 3),
(300000, '2024-10-10', 4, 4),
(850000, '2024-10-12', 1, 5),

-- Donaciones de septiembre 2024
(650000, '2024-09-25', 2, 6),
(400000, '2024-09-28', 5, 1),
(900000, '2024-09-30', 3, 2),
(250000, '2024-09-22', 4, 3),
(1500000, '2024-09-20', 1, 4),

-- Donaciones de agosto 2024
(350000, '2024-08-15', 2, 5),
(800000, '2024-08-18', 3, 6),
(450000, '2024-08-25', 5, 1),
(600000, '2024-08-28', 1, 2),
(550000, '2024-08-30', 4, 3);