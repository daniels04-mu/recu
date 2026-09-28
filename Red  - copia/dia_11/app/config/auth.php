<?php
require_once 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['ultimo_ingreso'] = time();

        // Redirección inteligente según el Rol (Separación de secciones)
        if ($usuario['rol'] === 'Administrador' || $usuario['rol'] === 'Vendedor') {
            header("Location: ../../dashboard.php");
        } else {
            header("Location: ../../catalogo_cliente.php");
        }
        exit();
    } else {
        header("Location: ../../login.php?error=credenciales");
        exit();
    }
}