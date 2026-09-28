<?php
/**
 * Guardia de acceso - Red Forge
 * Verifica que el usuario tenga una sesión activa para permitirle ver las páginas protegidas.
 */

// Asegurarnos de que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no existe la variable de sesión del usuario, redirigir al login
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['correo'])) {
    // Ajusta la ruta de redirección según dónde se encuentre la vista actual
    header("Location: login.php");
    exit();
}