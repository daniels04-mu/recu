<?php
require_once 'app/config/conexion.php';
session_start();

// Verificación de seguridad estándar para clientes
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Consultor') {
    header("Location: login.php");
    exit();
}

$error = '';

// Procesar la compra del producto con cantidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $cantidad_deseada = intval($_POST['cantidad']);
    $metodo_pago = $_POST['metodo_pago'];
    $estado_pago = ($metodo_pago === 'Efectivo') ? 'Pendiente' : 'Pagado'; 

    try {
        // Verificar stock actual del producto
        $stmt_stock = $pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
        $stmt_stock->execute([$producto_id]);
        $prod_info = $stmt_stock->fetch();

        if ($prod_info && $cantidad_deseada > 0 && $cantidad_deseada <= $prod_info['stock']) {
            // 1. Registrar el pedido con la cantidad
            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, producto_id, cantidad, metodo_pago, estado_pago) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $producto_id, $cantidad_deseada, $metodo_pago, $estado_pago]);

            // 2. Descontar el stock del producto automáticamente
            $nuevo_stock = $prod_info['stock'] - $cantidad_deseada;
            $stmt_update = $pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
            $stmt_update->execute([$nuevo_stock, $producto_id]);

            // Redirección para evitar duplicados al recargar (PRG)
            header("Location: catalogo_cliente.php?exito=1");
            exit();
        } else {
            $error = "La cantidad solicitada supera el stock disponible o no es válida.";
        }
    } catch (PDOException $e) {
        $error = "Error al procesar el pedido.";
    }
}

$productos = $pdo->query("SELECT * FROM productos WHERE stock > 0")->fetchAll();

$mis_pedidos = $pdo->prepare("SELECT p.*, pr.nombre AS producto_nombre FROM pedidos p JOIN productos pr ON p.producto_id = pr.id WHERE p.usuario_id = ?");
$mis_pedidos->execute([$_SESSION['usuario_id']]);
$pedidos = $mis_pedidos->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal de Clientes - Red Forge</title>
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        body { background-color: var(--forja-fondo); color: var(--forja-texto); font-family: 'Segoe UI', sans-serif; margin: 0; padding: 2rem; }
        .cliente-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--forja-borde); padding-bottom: 1rem; margin-bottom: 2rem; }
        .grid-catalogo { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
        .card-producto { background: var(--forja-panel); border: 1px solid var(--forja-borde); border-radius: 8px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; }
        .btn-comprar { background: #ea580c; color: white; border: none; padding: 0.6rem; border-radius: 6px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 1rem; }
        .btn-comprar:hover { background: #c2410c; }
        select, input[type="number"] { width: 100%; padding: 0.5rem; background: #0a0b0e; border: 1px solid var(--forja-borde); color: white; border-radius: 4px; margin-top: 0.3rem; margin-bottom: 0.8rem; box-sizing: border-box; }
        .tabla-pedidos { width: 100%; border-collapse: collapse; margin-top: 1rem; background: var(--forja-panel); border-radius: 8px; overflow: hidden; }
        .tabla-pedidos th, .tabla-pedidos td { padding: 0.8rem; text-align: left; border-bottom: 1px solid var(--forja-borde); font-size: 0.9rem; }
        .tabla-pedidos th { background: #1a1f2c; color: #f97316; }
    </style>
</head>
<body>
    <div class="cliente-header">
        <h2 style="color: #f97316; margin: 0;">RED FORGE — Portal de Clientes</h2>
        <div>
            <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
            <a href="app/config/logout.php" style="margin-left: 1.5rem; color: #f87171; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
        </div>
    </div>

    <?php if(isset($_GET['exito'])): ?>
        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #22c55e; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem;">
            ¡Pedido realizado con éxito y stock actualizado!
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <h1>Catálogo Disponible</h1>
    <p>Selecciona el producto, indica cuántas unidades deseas y elige tu método de pago.</p>

    <div class="grid-catalogo">
        <?php foreach($productos as $prod): ?>
        <div class="card-producto">
            <div>
                <span style="font-size: 0.8rem; color: var(--forja-acero); text-transform: uppercase;"><?php echo htmlspecialchars($prod['categoria']); ?></span>
                <h3 style="color: white; margin: 0.3rem 0; font-size: 1.1rem;"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                <p style="color: #f97316; font-size: 1.4rem; font-weight: bold; margin: 0.5rem 0;">$<?php echo number_format($prod['precio'], 2); ?></p>
                <span style="font-size: 0.85rem; color: var(--forja-acero);">Stock disponible: <strong><?php echo $prod['stock']; ?></strong></span>
            </div>

            <form action="catalogo_cliente.php" method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="comprar" value="1">
                <input type="hidden" name="producto_id" value="<?php echo $prod['id']; ?>">
                
                <label style="font-size: 0.8rem; color: var(--forja-acero);">Cantidad:</label>
                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $prod['stock']; ?>" required>

                <label style="font-size: 0.8rem; color: var(--forja-acero);">Método de Pago:</label>
                <select name="metodo_pago" required>
                    <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
                    <option value="Transferencia">Transferencia Bancaria</option>
                    <option value="Efectivo">Efectivo (Pago en entrega)</option>
                </select>

                <button type="submit" class="btn-comprar">Comprar Producto</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top: 3rem;">Mis Pedidos y Estado</h2>
    <table class="tabla-pedidos">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Método de Pago</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($pedidos) > 0): ?>
                <?php foreach($pedidos as $ped): ?>
                <tr>
                    <td>#<?php echo $ped['id']; ?></td>
                    <td><?php echo htmlspecialchars($ped['producto_nombre']); ?></td>
                    <td><strong><?php echo $ped['cantidad']; ?></strong></td>
                    <td><?php echo htmlspecialchars($ped['metodo_pago']); ?></td>
                    <td>
                        <?php 
                            $color = '#ef4444'; // Pendiente
                            if($ped['estado_pago'] === 'Pagado') $color = '#22c55e';
                            if($ped['estado_pago'] === 'Completado') $color = '#60a5fa';
                        ?>
                        <span style="padding: 0.3rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.8rem; background: rgba(0,0,0,0.3); color: <?php echo $color; ?>;">
                            <?php echo $ped['estado_pago']; ?>
                        </span>
                    </td>
                    <td><?php echo $ped['fecha_pedido']; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--forja-acero);">Aún no has realizado pedidos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>