<?php
// Página de preferencias: permite al usuario cambiar tema, idioma y preferencias personales
// Solo accesible para usuarios logueados
// Incluimos las configuraciones y funciones principales 
require_once __DIR__ . '/config.php';

// === LÓGICA DE CONTROL DE ACCESO ===

// Si el usuario no está logueado, lo redirigimos a la página de inicio de sesión.
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$es_admin = ($usuario === 'admin');

// === PROCESAMIENTO DEL FORMULARIO POST ===

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // obtener los valores POST
    $nuevo_tema = $_POST['tema'] ?? 'light';
    $nuevo_idioma = $_POST['idioma'] ?? 'es';

    // Guardar en cookies (30 días)
    // El valor 86400 es el número de segundos en un día
    $tiempo_expiracion = time() + (86400 * 30);
    setcookie('tema', $nuevo_tema, $tiempo_expiracion, "/");
    setcookie('idioma', $nuevo_idioma, $tiempo_expiracion, "/");

    // Actualizar la sesión para que los cambios se apliquen INMEDIATAMENTE en la navegación actual
    $_SESSION['tema'] = $nuevo_tema;
    $_SESSION['idioma'] = $nuevo_idioma;
    
    // Establecer un mensaje y limpiar la marca de redirección
    $_SESSION['mensaje_exito'] = '💾 Preferencias guardadas. ¡Bienvenido, ' . $usuario . '!';
    unset($_SESSION['preferencias']);

    // Redirigir al inicio para aplicar los cambios y evitar reenvío de formulario 
    header('Location: inicio.php');
    exit();
}

// === LECTURA DE PREFERENCIAS ACTUALES PARA MOSTRAR EN EL FORMULARIO ===
// Usamos las variables definidas en config.php
$tema_actual = $_SESSION['tema'] ?? 'light';
$idioma_actual = $_SESSION['idioma'] ?? 'es';

?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual; ?>">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('Preferencias'); ?> | animalia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .contenedor-preferencias { max-width: 480px; margin: 1rem auto; padding: 1rem; }
        .campo-preferencia { margin: 1rem 0; }
        .campo-preferencia label { display: block; margin-bottom: 0.3rem; font-weight: bold; }
        .select-preferencia { display: block; width: 100%; padding: 0.5rem; }
    </style>
</head>
<body <?php echo get_body_attrs(); ?>>
    
    <h1>animalia preferencias</h1>

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
            <?php
            // Mostrar enlace al panel de control solo si el usuario es admin
            if (!empty($usuario)) {
                require_once __DIR__ . '/db.php';
                $stmt_rol = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre = ?");
                $stmt_rol->execute([$usuario]);
                $rol = $stmt_rol->fetchColumn();
                if ($rol === 'admin') {
                    echo '<li><a href="admin/admin_panel.php">Panel de control</a></li>';
                }
            }
            ?>
            <?php if (isset($_SESSION['usuario'])): ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php"><?php echo t('Iniciar Sesión'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <main>
        <div class="contenedor-preferencias">
            <h2><?php echo t('Preferencias'); ?></h2>
            <p>
                <?php echo t('Holi, has iniciado sesion'); ?> <strong><?php echo $usuario; ?></strong>. 
                Aquí puedes personalizar tu experiencia.
            </p>

            <form method="post" action="preferencias.php">
                
                <div class="campo-preferencia">
                    <label for="tema"><?php echo t('Selecciona un tema'); ?></label>
                    <select name="tema" id="tema" class="select-preferencia">
                        <option value="light" <?php if ($tema_actual === 'light') echo 'selected'; ?>>Claro</option>
                        <option value="dark" <?php if ($tema_actual === 'dark') echo 'selected'; ?>>Oscuro</option>
                    </select>
                </div>

                <div class="campo-preferencia">
                    <label for="idioma"><?php echo t('Selecciona un idioma'); ?></label>
                    <select name="idioma" id="idioma" class="select-preferencia">
                        <option value="es" <?php if ($idioma_actual === 'es') echo 'selected'; ?>><?php echo t('Español'); ?></option>
                        <option value="en" <?php if ($idioma_actual === 'en') echo 'selected'; ?>><?php echo t('Inglés'); ?></option>
                    </select>
                </div>

                <button type="submit" class="btn-guardar"><?php echo t('Guardar Preferencias'); ?></button>
            </form>

            <?php if ($es_admin): ?>
                <section class="admin-gif-selector">
                    <h3>Configuración de Admin</h3>
                    <p>Selecciona el GIF que aparecerá en tu sesión (se guarda en cookie):</p>
                    <button type="button" onclick="setAnimalCookie('perro')"><?php echo t('Perro'); ?></button>
                    <button type="button" onclick="setAnimalCookie('gato')"><?php echo t('Gato'); ?></button>
                    <button type="button" onclick="eraseCookie('animal')"><?php echo t('Eliminar preferencia'); ?></button>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <script src="js\app.js"></script>
</body>
</html>