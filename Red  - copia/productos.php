<?php
require_once 'app/config/guardia.php';
require_once 'app/config/conexion.php';

$mensaje = '';
$modoEdicion = false;
$prodEditar = ['id' => '', 'nombre' => '', 'precio' => '', 'stock' => '', 'categoria' => ''];

// 1. CREAR O EDITAR PRODUCTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre']);
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $categoria = trim($_POST['categoria']);

        if ($precio >= 0 && $stock >= 0) {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock, categoria) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $precio, $stock, $categoria]);
            header("Location: productos.php");
            exit();
        } else {
            $mensaje = "Error: El precio y el stock no pueden ser negativos.";
        }
    }

    if ($accion === 'actualizar') {
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $categoria = trim($_POST['categoria']);

        if ($precio >= 0 && $stock >= 0) {
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ?, categoria = ? WHERE id = ?");
            $stmt->execute([$nombre, $precio, $stock, $categoria, $id]);
            header("Location: productos.php");
            exit();
        } else {
            $mensaje = "Error: Los valores numéricos no pueden ser negativos.";
        }
    }
}

// 2. OBTENER DATOS PARA EDITAR SI SE SOLICITA POR URL
if (isset($_GET['editar'])) {
    $modoEdicion = true;
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $prodEditar = $stmt->fetch();
}

$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario y Edición - Red Forge</title>
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        .tabla-inventario { width: 100%; border-collapse: collapse; margin-top: 1.5rem; background: var(--forja-panel); border-radius: 8px; overflow: hidden; }
        .tabla-inventario th, .tabla-inventario td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--forja-borde); }
        .tabla-inventario th { background: #1a1f2c; color: #f97316; }
        .form-inline { background: var(--forja-panel); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--forja-borde); display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 130px; gap: 1rem; align-items: end; margin-bottom: 2rem; }
        .form-inline input { padding: 0.6rem; background: #0a0b0e; border: 1px solid var(--forja-borde); color: white; border-radius: 6px; width: 100%; box-sizing: border-box; }
        .form-inline label { display: block; margin-bottom: 0.3rem; color: var(--forja-acero); font-size: 0.85rem; }
        .btn-accion { padding: 0.4rem 0.8rem; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: bold; cursor: pointer; }
        .btn-editar { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid #3b82f6; }
        .btn-editar:hover { background: #3b82f6; color: white; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h2 style="color: #f97316;">RED FORGE</h2>
            <p style="color: var(--forja-acero); font-size: 0.85rem;">Módulo de Inventario</p>
            <nav>
                <ul>
                    <li><a href="dashboard.php">📊 Inicio</a></li>
                    <li><a href="productos.php" class="active">📦 Productos (CRUD)</a></li>
                    <li><a href="app/config/logout.php" style="color: #f87171;">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <header class="header">
            <h3 style="color: white;">Gestión y Edición de Inventario</h3>
            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
        </header>

        <main class="main-content">
            <h1><?php echo $modoEdicion ? 'Editar Producto #' . $prodEditar['id'] : 'Registrar Nuevo Producto'; ?></h1>
            <p>Control de componentes industriales con validación segura de stock.</p>

            <?php if($mensaje): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form action="productos.php" method="POST" class="form-inline">
                <input type="hidden" name="accion" value="<?php echo $modoEdicion ? 'actualizar' : 'crear'; ?>">
                <?php if($modoEdicion): ?>
                    <input type="hidden" name="id" value="<?php echo $prodEditar['id']; ?>">
                <?php endif; ?>

                <div>
                    <label>Nombre del producto</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($prodEditar['nombre']); ?>" required>
                </div>
                <div>
                    <label>Precio ($)</label>
                    <input type="number" step="0.01" min="0" name="precio" value="<?php echo $prodEditar['precio']; ?>" required>
                </div>
                <div>
                    <label>Stock (Cantidad)</label>
                    <input type="number" min="0" name="stock" value="<?php echo $prodEditar['stock']; ?>" required>
                </div>
                <div>
                    <label>Categoría</label>
                    <input type="text" name="categoria" value="<?php echo htmlspecialchars($prodEditar['categoria']); ?>" required>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" style="height: 38px; width: 100%; background-color: #ea580c; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                        <?php echo $modoEdicion ? 'Actualizar' : 'Guardar'; ?>
                    </button>
                    <?php if($modoEdicion): ?>
                        <a href="productos.php" style="height: 38px; display: flex; align-items: center; justify-content: center; background: #334155; color: white; text-decoration: none; border-radius: 6px; padding: 0 10px; font-size: 0.85rem;">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>

            <h2 style="margin-top: 2rem; font-size: 1.3rem;">Lista de Inventario Actual</h2>
            <table class="tabla-inventario">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td>$<?php echo number_format($p['precio'], 2); ?></td>
                        <td>
                            <span style="color: <?php echo $p['stock'] <= 5 ? '#ef4444' : '#22c55e'; ?>; font-weight: bold;">
                                <?php echo $p['stock']; ?> un.
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                        <td>
                            <a href="productos.php?editar=<?php echo $p['id']; ?>" class="btn-accion btn-editar">✏️ Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>