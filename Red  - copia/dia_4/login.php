<?php
require_once 'app/config/conexion.php';
session_start();

$errorLogin = '';
$errorRegistro = '';
$exitoRegistro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'login') {
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

            if ($usuario['rol'] === 'Administrador' || $usuario['rol'] === 'Vendedor') {
                header("Location: dashboard.php");
            } else {
                header("Location: catalogo_cliente.php");
            }
            exit();
        } else {
            $errorLogin = "Correo o contraseña incorrectos.";
        }
    }

    if ($accion === 'registro') {
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = 'Consultor';

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $correo, $password, $rol]);
            $exitoRegistro = "¡Registro exitoso! Ya puedes iniciar sesión.";
        } catch (PDOException $e) {
            $errorRegistro = "El correo electrónico ya se encuentra registrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso y Registro - Red Forge</title>
    <link rel="stylesheet" href="assets/css/tokens.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        body { 
            display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; 
            background-color: var(--forja-fondo); color: var(--forja-texto); font-family: 'Segoe UI', sans-serif;
            overflow: hidden; position: relative;
        }
        
        /* Contenedor de partículas de forja (Chispas) */
        #forja-chispas {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 1; overflow: hidden;
        }
        .chispa {
            position: absolute; background: #fb923c; border-radius: 50%;
            box-shadow: 0 0 8px #ea580c, 0 0 15px #f97316;
            animation: elevarChispa linear infinite;
        }
        @keyframes elevarChispa {
            0% { transform: translateY(105vh) translateX(0) scale(0.5); opacity: 0; }
            20% { opacity: 0.8; }
            80% { opacity: 0.8; }
            100% { transform: translateY(-10vh) translateX(40px) scale(1.2); opacity: 0; }
        }

        .auth-container { 
            background: rgba(19, 22, 28, 0.9); padding: 2.5rem; border-radius: 10px; 
            border: 1px solid var(--forja-borde); width: 100%; max-width: 420px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.7); box-sizing: border-box; 
            position: relative; z-index: 2; backdrop-filter: blur(5px);
        }
        .tabs { display: flex; margin-bottom: 1.5rem; border-bottom: 2px solid var(--forja-borde); }
        .tab-btn { 
            flex: 1; background: none; border: none; padding: 0.75rem; color: var(--forja-acero); 
            font-weight: bold; cursor: pointer; font-size: 1rem; transition: all 0.3s; 
        }
        .tab-btn.active { color: #f97316; border-bottom: 2px solid #f97316; margin-bottom: -2px; }
        .form-section { display: none; }
        .form-section.active { display: block; }
        .campo { margin-bottom: 1.2rem; }
        .campo label { display: block; margin-bottom: 0.5rem; color: var(--forja-acero); font-size: 0.85rem; }
        .campo input { 
            width: 100%; padding: 0.75rem; background: #0a0b0e; border: 1px solid var(--forja-borde); 
            color: white; border-radius: 6px; box-sizing: border-box; 
        }
        .campo input:focus { border-color: #ea580c; outline: none; box-shadow: 0 0 5px rgba(234, 88, 12, 0.4); }
        .btn-forja { 
            width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #ea580c, #c2410c); color: white; 
            border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: filter 0.2s; 
        }
        .btn-forja:hover { filter: brightness(1.2); }
        .msg-error { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 0.6rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
        .msg-exito { background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #22c55e; padding: 0.6rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div id="forja-chispas"></div>

    <main class="auth-container">
        <h2 style="color: #f97316; text-align: center; margin-top: 0; letter-spacing: 2px; text-shadow: 0 0 10px rgba(249,115,22,0.4);">🔥 RED FORGE</h2>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="cambiarTab('login', event)">Iniciar Sesión</button>
            <button class="tab-btn" onclick="cambiarTab('registro', event)">Registrarse</button>
        </div>

        <div id="seccion-login" class="form-section active">
            <?php if($errorLogin): ?>
                <div class="msg-error"><?php echo $errorLogin; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="hidden" name="accion" value="login">
                <div class="campo">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" required>
                </div>
                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-forja">Ingresar al Sistema</button>
            </form>
        </div>

        <div id="seccion-registro" class="form-section">
            <?php if($errorRegistro): ?>
                <div class="msg-error"><?php echo $errorRegistro; ?></div>
            <?php endif; ?>
            <?php if($exitoRegistro): ?>
                <div class="msg-exito"><?php echo $exitoRegistro; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="hidden" name="accion" value="registro">
                <div class="campo">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="campo">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" required>
                </div>
                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-forja">Crear Cuenta</button>
            </form>
        </div>
    </main>

    <script>
        function cambiarTab(tipo, evento) {
            document.querySelectorAll('.form-section').forEach(sec => sec.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            if(tipo === 'login') {
                document.getElementById('seccion-login').classList.add('active');
            } else {
                document.getElementById('seccion-registro').classList.add('active');
            }
            evento.currentTarget.classList.add('active');
        }

        // Generador dinámico de partículas de chispas de forja
        const contenedorChispas = document.getElementById('forja-chispas');
        const cantidadChispas = 25;

        for (let i = 0; i < cantidadChispas; i++) {
            const chispa = document.createElement('div');
            chispa.classList.add('chispa');
            
            const size = Math.random() * 4 + 2 + 'px';
            chispa.style.width = size;
            chispa.style.height = size;
            
            chispa.style.left = Math.random() * 100 + 'vw';
            chispa.style.animationDuration = (Math.random() * 3 + 2) + 's';
            chispa.style.animationDelay = (Math.random() * 5) + 's';
            
            contenedorChispas.appendChild(chispa);
        }
    </script>
</body>
</html>