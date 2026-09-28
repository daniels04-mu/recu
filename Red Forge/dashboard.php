<?php
require_once 'app/config/seguridad/sesion.php';
require_once 'app/config/seguridad/guardia.php';
require_once 'app/config/conexion.php';

$pageTitle = "Dashboard - Red Forge";
include 'app/vistas/parciales/cabecera.php';
?>

    <div class="dashboard-container">
        <!-- Barra Lateral / Menú Modular -->
        <aside class="sidebar">
            <div class="logo-area">
                <h2>Red Forge</h2>
            </div>
            <?php include 'app/vistas/parciales/menu.php'; ?>
        </aside>

        <!-- Contenido Principal del Dashboard -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Panel de Control Industrial</h1>
                <div class="user-info">
                    <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['correo'] ?? 'Administrador'); ?></strong></span>
                </div>
            </header>

            <!-- Sección de Estadísticas y Métricas Generales -->
            <section class="stats-grid">
                <div class="card-stat">
                    <h3>Gestión Activa</h3>
                    <p>Sistema sincronizado con el plan SENA.</p>
                </div>
                <div class="card-stat">
                    <h3>Seguridad</h3>
                    <p>Sesiones cifradas y protección activas.</p>
                </div>
            </section>

            <!-- Contenedor dinámico inyectado por graficos.js (Día 14) -->
            <section class="stats-grid" id="metricas-js">
                <!-- Aquí se cargarán automáticamente los datos de stock y valor del inventario -->
            </section>
        </main>
    </div>

    <!-- Scripts de JavaScript -->
    <script src="assets/js/datos-prueba.js"></script>
    <script src="assets/js/ejercicios.js"></script>
    <script src="assets/js/graficos.js"></script>

<?php include 'app/vistas/parciales/pie.php'; ?>