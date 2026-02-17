<?php
// Página de inicio de sesión: permite a los usuarios autenticarse
// Redirige a inicio.php si ya está logueado
// Incluir el archivo de configuración y funciones (inicia la sesión)
require_once __DIR__ . '/config.php';

// Comprobar si hay un mensaje de error de la sesión
$error_login = $_SESSION['error_login'] ?? '';
// Limpiamos el mensaje inmediatamente después de leerlo para que no se muestre de nuevo al recargar
if (!empty($error_login)) {
    unset($_SESSION['error_login']);
}

// Redirigir al usuario si ya ha iniciado sesión
if (!empty($_SESSION['usuario'])) {
    header('Location: inicio.php');
    exit();
}

// Usamos $idioma_actual que definimos en config.php
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('Iniciar Sesión'); ?> | Animalia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body <?php echo get_body_attrs(); ?>>
    
    <h1>animalia</h1>

    <div id="menu_hamburguesa">
        <span class="-top"></span>
        <span class="-mid"></span>
        <span class="-bottom"></span>
    </div>
    
    <!-- Menú de navegación principal -->
    <div class="menu_desplegable">
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="listaDeseos.php">Lista de Deseos</a></li>
            <?php
            if (!empty($_SESSION['usuario'])) {
                require_once __DIR__ . '/db.php';
                $stmt_rol = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre = ?");
                $stmt_rol->execute([$_SESSION['usuario']]);
                $rol = $stmt_rol->fetchColumn();
                if ($rol === 'admin') {
                    echo '<li><a href="admin/admin_panel.php">Panel de control</a></li>';
                }
            }
            ?>
        </ul>
    </div>

    <main class="contenedor-login">
        <h2><?php echo t('Iniciar Sesión'); ?></h2>

        <?php if (!empty($error_login)): ?>
            <p class="mensaje-error"><?php echo $error_login; ?></p>
        <?php endif; ?>

        <form action="procesar_login.php" method="POST" class="formulario-login">
            <div class="campo-form">
                <label for="usuario"><?php echo t('Usuario'); ?></label>
                <input type="text" id="usuario" name="usuario" required autocomplete="username">
            </div>
            
            <div class="campo-form">
                <label for="password"><?php echo t('Contraseña'); ?></label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-ingresar"><?php echo t('Ingresar'); ?></button>
        </form>
    </main>
    
    <script src="js\app.js"></script>
</body>
</html>