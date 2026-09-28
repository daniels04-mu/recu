<?php
require_once __DIR__ . '/../../config/seguridad/sesion.php';
require_once __DIR__ . '/../../config/seguridad/guardia.php';
require_once __DIR__ . '/../../config/conexion.php';

// Consultar los datos utilizando la vista SQL que creamos en el Día 14
try {
    $stmt = $pdo->query("SELECT * FROM vw_estado_stock");
    $productos = $stmt->fetchAll();

    $stmtResumen = $pdo->query("SELECT * FROM vw_resumen_inventario");
    $resumen = $stmtResumen->fetch();
} catch (PDOException $e) {
    $productos = [];
    $resumen = ['total_referencias' => 0, 'unidades_totales' => 0, 'valor_total_inventario' => 0];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario - Red Forge</title>
    <!-- Enlace al archivo CSS específico de reportes -->
    <link rel="stylesheet" href="../../../assets/css/reporte.css">
</head>
<body class="reporte-body">

    <div class="reporte-container">
        <header class="reporte-header">
            <h1>Red Forge - E-commerce Industrial</h1>
            <p>Reporte Oficial de Estado de Inventario y Stock</p>
            <p>Fecha de emisión: <?php echo date('d/m/Y H:i:s'); ?></p>
        </header>

        <section class="resumen-global" style="margin-bottom: 20px;">
            <h3>Resumen Ejecutivo</h3>
            <ul>
                <li>Total de referencias en sistema: <strong><?php echo $resumen['total_referencias'] ?? 0; ?></strong></li>
                <li>Unidades totales en bodega: <strong><?php echo $resumen['unidades_totales'] ?? 0; ?></strong></li>
                <li>Valor total estimado: <strong>$<?php echo number_format($resumen['valor_total_inventario'] ?? 0, 2); ?></strong></li>
            </ul>
        </section>

        <table class="tabla-reporte">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Producto</th>
                    <th>Precio Unitario</th>
                    <th>Stock Actual</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td>$<?php echo number_format($item['precio'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['stock']); ?></td>
                            <td>
                                <?php if ($item['stock'] <= 5): ?>
                                    <span class="badge-critico">⚠️ <?php echo htmlspecialchars($item['estado_inventario']); ?></span>
                                <?php else: ?>
                                    <span class="badge-ok">✔ <?php echo htmlspecialchars($item['estado_inventario']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay productos registrados en el sistema.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Botón para imprimir el reporte directamente desde el navegador -->
        <div style="text-align: center;" class="no-print">
            <button onclick="window.print();" style="padding: 10px 20px; background: #1a1a1a; color: #fff; border: none; cursor: pointer; border-radius: 4px;">
                🖨️ Imprimir / Guardar como PDF
            </button>
            <a href="../../../dashboard.php" style="margin-left: 15px; text-decoration: none; color: #333;">Volver al Dashboard</a>
        </div>

        <footer class="reporte-footer">
            <span>Sistema Red Forge - Módulo de Reportes</span>
            <span>Plan de Mejoramiento SENA</span>
        </footer>
    </div>

</body>
</html>