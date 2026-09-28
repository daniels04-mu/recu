<?php
/**
 * Configuración y control seguro de sesiones - Red Forge
 */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Cambiar a true si usas HTTPS en producción
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    if (!isset($_SESSION['ultimo_cambio'])) {
        $_SESSION['ultimo_cambio'] = time();
    } elseif (time() - $_SESSION['ultimo_cambio'] > 1800) { // Cada 30 minutos
        session_regenerate_id(true);
        $_SESSION['ultimo_cambio'] = time();
    }
}