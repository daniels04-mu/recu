
CREATE OR REPLACE VIEW vw_estado_stock AS
SELECT 
    id,
    nombre,
    precio,
    stock,
    CASE 
        WHEN stock = 0 THEN 'Agotado'
        WHEN stock <= 5 THEN 'Stock Crítico'
        ELSE 'Disponible'
    END AS estado_inventario
FROM productos;


CREATE OR REPLACE VIEW vw_resumen_inventario AS
SELECT 
    COUNT(*) AS total_referencias,
    SUM(stock) AS unidades_totales,
    ROUND(AVG(precio), 2) AS precio_promedio,
    SUM(precio * stock) AS valor_total_inventario
FROM productos;