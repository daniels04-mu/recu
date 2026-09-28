<?php
require_once 'app/config/guardia.php';
require_once 'app/config/conexion.php';

// Asegurarse de que solo administradores o vendedores entren aquí
if ($_SESSION['rol'] !== 'Administrador' && $_SESSION['rol'] !== 'Vendedor') {
    header("Location: catalogo_cliente.php");
    exit();
}

$mensaje = '';

// CAMBIAR ESTADO DEL PEDIDO (Pendiente, Pagado, Completado)
if (isset($_GET['cambiar_estado']) && isset($_GET['id'])) {
    $nuevo_estado = $_GET['cambiar_estado'];
    $id_pedido = $_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE pedidos SET estado_pago = ? WHERE id = ?");
        $stmt->execute([$nuevo_estado, $id_pedido]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $mensaje = "Error al actualizar el estado.";
    }
}

// ELIMINAR / DAR FIN A UN PEDIDO (Y devolver stock opcionalmente si se requiere, o solo borrar)
if (isset($_GET['eliminar_pedido'])) {
    $id_pedido = $_GET['eliminar_pedido'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$id_pedido]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar el pedido.";
    }
}

// Contadores rápidos para el resumen
$totalProductos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totalClientes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'Consultor'")->fetchColumn();
$totalPedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();

// Obtener TODOS los pedidos con cantidad, cliente y producto
$stmt_pedidos = $pdo->query("
    SELECT p.id, u.nombre AS cliente_nombre, pr.nombre AS producto_nombre, p.cantidad, p.metodo_pago, p.estado_pago, p.fecha_pedido 
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN productos pr ON p.producto_id = pr.id
    ORDER BY p.fecha_pedido DESC
");
$pedidos_admin = $stmt_pedidos->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Red Forge</title>
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .tabla-admin-pedidos { width: 100%; border-collapse: collapse; margin-top: 1rem; background: var(--forja-panel); border-radius: 8px; overflow: hidden; }
        .tabla-admin-pedidos th, .tabla-admin-pedidos td { padding: 0.9rem; text-align: left; border-bottom: 1px solid var(--forja-borde); font-size: 0.9rem; }
        .tabla-admin-pedidos th { background: #1a1f2c; color: #f97316; }
        .btn-accion-estado { padding: 0.3rem 0.6rem; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: bold; margin-right: 4px; display: inline-block; }
        .btn-pagado { background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid #22c55e; }
        .btn-completado { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; }
        .btn-eliminar { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; padding: 0.3rem 0.6rem; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: bold; display: inline-block; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h2 style="color: #f97316;">RED FORGE</h2>
            <p style="color: var(--forja-acero); font-size: 0.85rem; margin-bottom: 2rem;">Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="active">📊 Inicio / Pedidos</a></li>
                    <li><a href="productos.php">📦 Productos (CRUD)</a></li>
                    <li><a href="app/config/logout.php" style="color: #f87171;">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <header class="header">
            <h3>Panel de Control Industrial</h3>
            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
        </header>

        <main class="main-content">
            <h1>Gestión de Pedidos del Sistema</h1>
            <p>Monitorea y administra las solicitudes de compra en tiempo real.</p>

            <?php if($mensaje): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Tarjetas de Resumen -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <div class="card" style="background: var(--forja-panel); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--forja-borde);">
                    <h3 style="color: var(--forja-acero); font-size: 0.85rem;">TOTAL PRODUCTOS</h3>
                    <p style="font-size: 2.2rem; font-weight: bold; color: white; margin: 0.5rem 0;"><?php echo $totalProductos; ?></p>
                </div>
                <div class="card" style="background: var(--forja-panel); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--forja-borde);">
                    <h3 style="color: var(--forja-acero); font-size: 0.85rem;">CLIENTES REGISTRADOS</h3>
                    <p style="font-size: 2.2rem; font-weight: bold; color: white; margin: 0.5rem 0;"><?php echo $totalClientes; ?></p>
                </div>
                <div class="card" style="background: var(--forja-panel); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--forja-borde);">
                    <h3 style="color: var(--forja-acero); font-size: 0.85rem;">TOTAL PEDIDOS</h3>
                    <p style="font-size: 2.2rem; font-weight: bold; color: #f97316; margin: 0.5rem 0;"><?php echo $totalPedidos; ?></p>
                </div>
            </div>

            <!-- Tabla de Control de Pedidos -->
            <h2 style="margin-top: 2.5rem; font-size: 1.3rem;">Listado General de Pedidos</h2>
            <table class="tabla-admin-pedidos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Método Pago</th>
                        <th>Estado Actual</th>
                        <th>Fecha</th>
                        <th>Acciones / Estados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($pedidos_admin) > 0): ?>
                        <?php foreach($pedidos_admin as $ped): ?>
                        <tr>
                            <td>#<?php echo $ped['id']; ?></td>
                            <td><?php echo htmlspecialchars($ped['cliente_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ped['producto_nombre']); ?></td>
                            <td><strong><?php echo $ped['cantidad']; ?></strong></td>
                            <td><?php echo htmlspecialchars($ped['metodo_pago']); ?></td>
                            <td>
                                <?php 
                                    $color = '#ef4444'; // Pendiente (Rojo)
                                    if($ped['estado_pago'] === 'Pagado') $color = '#22c55e'; // Verde
                                    if($ped['estado_pago'] === 'Completado') $color = '#60a5fa'; // Azul
                                ?>
                                <span style="padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem; background: rgba(0,0,0,0.3); color: <?php echo $color; ?>;">
                                    <?php echo $ped['estado_pago']; ?>
                                </span>
                            </td>
                            <td><?php echo $ped['fecha_pedido']; ?></td>
                            <td>
                                <a href="dashboard.php?cambiar_estado=Pagado&id=<?php echo $ped['id']; ?>" class="btn-accion-estado btn-pagado">Pagado</a>
                                <a href="dashboard.php?cambiar_estado=Completado&id=<?php echo $ped['id']; ?>" class="btn-accion-estado btn-completado">Completar</a>
                                <a href="dashboard.php?eliminar_pedido=<?php echo $ped['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de dar fin y borrar este pedido?');">Borrar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--forja-acero);">No hay pedidos registrados en el sistema todavía.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>