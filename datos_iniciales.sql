-- ACTUALIZACIÓN COMPLETA DE DATOS A FECHAS RECIENTES
-- Ejecutar en phpMyAdmin para tener datos actuales

USE ORGANIZACION;

-- 1. ACTUALIZAR FECHAS DE PROYECTOS (2025-2026)
UPDATE PROYECTO SET fecha_inicio = '2025-10-01', fecha_fin = '2026-06-30' WHERE id_proyecto = 1;
UPDATE PROYECTO SET fecha_inicio = '2025-10-15', fecha_fin = '2026-04-15' WHERE id_proyecto = 2;
UPDATE PROYECTO SET fecha_inicio = '2025-11-01', fecha_fin = '2026-08-31' WHERE id_proyecto = 3;
UPDATE PROYECTO SET fecha_inicio = '2025-11-15', fecha_fin = '2026-05-31' WHERE id_proyecto = 4;
UPDATE PROYECTO SET fecha_inicio = '2025-12-01', fecha_fin = '2026-09-30' WHERE id_proyecto = 5;

-- 2. ELIMINAR DONACIONES ANTIGUAS Y REINICIAR CONTADOR
DELETE FROM DONACION;
ALTER TABLE DONACION AUTO_INCREMENT = 1;

-- 3. INSERTAR DONACIONES RECIENTES (OCTUBRE-NOVIEMBRE 2025)
INSERT INTO DONACION (monto, fecha, id_proyecto, id_donante) VALUES
-- Noviembre 2025 (última semana)
(750000, '2025-11-12', 1, 1),
(1200000, '2025-11-11', 2, 2),
(500000, '2025-11-10', 3, 3),
(850000, '2025-11-09', 4, 4),
(950000, '2025-11-08', 1, 5),

-- Noviembre 2025 (primera semana)
(600000, '2025-11-06', 2, 6),
(1500000, '2025-11-05', 3, 1),
(450000, '2025-11-04', 5, 2),
(800000, '2025-11-03', 1, 3),
(350000, '2025-11-02', 4, 4),

-- Octubre 2025 (últimos días)
(1100000, '2025-10-30', 2, 5),
(700000, '2025-10-28', 3, 6),
(550000, '2025-10-26', 1, 1),
(900000, '2025-10-24', 4, 2),
(650000, '2025-10-22', 5, 3),

-- Octubre 2025 (mediados de mes)  
(400000, '2025-10-20', 2, 4),
(1300000, '2025-10-18', 1, 5),
(750000, '2025-10-16', 3, 6);

-- 4. VERIFICAR RESULTADOS
SELECT 'DONACIONES POR MES' as Reporte;
SELECT 
    DATE_FORMAT(fecha, '%Y-%m') as Mes,
    COUNT(*) as Total_Donaciones,
    SUM(monto) as Monto_Total
FROM DONACION 
GROUP BY DATE_FORMAT(fecha, '%Y-%m')
ORDER BY Mes DESC;

SELECT '' as Separador;
SELECT 'PROYECTOS ACTUALIZADOS' as Reporte;
SELECT 
    nombre,
    fecha_inicio,
    fecha_fin,
    CASE 
        WHEN fecha_fin >= CURDATE() THEN 'ACTIVO ✓'
        ELSE 'FINALIZADO'
    END as Estado
FROM PROYECTO
ORDER BY fecha_inicio;

SELECT '' as Separador;
SELECT 'DONACIONES MÁS RECIENTES' as Reporte;
SELECT 
    d.fecha,
    FORMAT(d.monto, 0) as Monto_CLP,
    p.nombre as Proyecto,
    dt.nombre as Donante
FROM DONACION d
INNER JOIN PROYECTO p ON d.id_proyecto = p.id_proyecto
INNER JOIN DONANTE dt ON d.id_donante = dt.id_donante
ORDER BY d.fecha DESC
LIMIT 8;