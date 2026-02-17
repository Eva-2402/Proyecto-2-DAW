<?php
// Página de lista de deseos: permite ver, añadir y eliminar productos de la lista de deseos del usuario
// Incluir configuración y funciones
require_once __DIR__ . '/config.php';

// === LÓGICA DE MANEJO DE DESEOS ===

//aseguramos que la lista de deseos sea un array vacío si no existe.
if (!isset($_SESSION['lista_deseos'])) {
    $_SESSION['lista_deseos'] = [];
}

// se ejecuta SÓLO si se envía un formulario 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // variabels del formulario
    $accion = $_POST['accion'] ?? '';
    $producto_imagen = $_POST['producto_imagen'] ?? '';
    $producto_nombre = $_POST['producto_nombre'] ?? '';

    // El producto que vamos a guardar/buscar es un array con sus dos campos
    $producto_a_guardar = [
        'imagen' => trim($producto_imagen),
        'nombre' => trim($producto_nombre)
    ];

    // --- Lógica para AÑADIR producto ---
    if ($accion === 'añadir_deseo' && !empty($producto_a_guardar['imagen'])) {
        
        $imagen_ruta = $producto_a_guardar['imagen'];

        // Comprobamos si el producto (por su ruta de imagen) ya está en la lista.
        // Usamos array_column para buscar solo en las rutas de imagen.
        $existe = in_array($imagen_ruta, array_column($_SESSION['lista_deseos'], 'imagen'));

        if (!$existe) {
            // Si NO existe, lo añadimos a la lista (como un array completo)
            $_SESSION['lista_deseos'][] = $producto_a_guardar;
            
            // Mensaje de éxito, guardado en sesión para mostrarlo en la página anterior (flash message)
            $_SESSION['mensaje_deseo'] = '🎉 ¡Has añadido **' . $producto_a_guardar['nombre'] . '** a tu lista!';
        } else {
            // Si SÍ existe
            $_SESSION['mensaje_deseo'] = '👉 **' . $producto_a_guardar['nombre'] . '** ya está en la lista.';
        }
    }

    // --- Lógica para ELIMINAR producto ---
    if ($accion === 'eliminar_deseo' && !empty($producto_a_guardar['imagen'])) {
        
        $imagen_ruta = $producto_a_guardar['imagen'];
        
        // Buscamos la clave (índice) del producto a eliminar
        $indice_a_eliminar = -1;
        foreach ($_SESSION['lista_deseos'] as $indice => $item) {
            if ($item['imagen'] === $imagen_ruta) {
                $indice_a_eliminar = $indice;
                break;
            }
        }

        // Si encontramos el índice, lo eliminamos
        if ($indice_a_eliminar !== -1) {
            // array_splice elimina el elemento del array por su índice.
            array_splice($_SESSION['lista_deseos'], $indice_a_eliminar, 1);
            $_SESSION['mensaje_deseo'] = '🗑️ Producto eliminado: ' . $producto_a_guardar['nombre'] . '.';
        } else {
            $_SESSION['mensaje_deseo'] = 'El producto a eliminar no se encontró.';
        }
    }

    // Patrón PRG (Post/Redirect/Get): Evita que el usuario reenvíe el formulario al recargar.
    // Redirigimos a la página actual (si no se especifica la página anterior)
    $referer = $_SERVER['HTTP_REFERER'] ?? 'listaDeseos.php';
    header('Location: ' . $referer);
    exit();
}

// === VISTA HTML: SOLO SE MUESTRA SI NO HAY POST O DESPUÉS DE LA REDIRECCIÓN ===
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos 💖</title>
    <link rel="stylesheet" href="style.css">
</head>
<body <?php echo get_body_attrs(); ?> >
    <h1>Mi Lista de Deseos 🛍️</h1>
    
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
            <?php if (isset($_SESSION['usuario'])): ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php"><?php echo t('Iniciar Sesión'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <main id="contenido">
        <section class="lista-principal-deseos">
            <h2>Mi Lista de Deseos</h2>
            
            <?php if (!empty($_SESSION['mensaje_deseo'])): ?>
                <p class="mensaje-exito"><?php echo $_SESSION['mensaje_deseo']; ?></p>
                <?php unset($_SESSION['mensaje_deseo']); // Borramos el mensaje para que no se muestre de nuevo ?>
            <?php endif; ?>

            <?php if (empty($_SESSION['lista_deseos'])): ?>
                <p>No hay productos en tu lista de deseos. ¡Añade algunos desde las categorías!</p>
                <a href="productos.php" class="btn-ir-a-comprar">Ir a Comprar</a>
            <?php else: ?>
                <p>Tienes **<?php echo count($_SESSION['lista_deseos']); ?>** productos guardados:</p>

                <div class="productos-lista">
                    <?php foreach ($_SESSION['lista_deseos'] as $producto): ?>
                        <div class="producto-item">
                            <img src="<?php echo $producto['imagen']; ?>" 
                                 alt="<?php echo $producto['nombre']; ?>">
                            
                            <p class="nombre-producto-lista"><?php echo $producto['nombre']; ?></p>

                            <form method="post" action="listaDeseos.php">
                                <input type="hidden" name="producto_imagen" value="<?php echo $producto['imagen']; ?>">
                                <input type="hidden" name="producto_nombre" value="<?php echo $producto['nombre']; ?>">
                                <button type="submit" name="accion" value="eliminar_deseo" class="btn-eliminar">
                                    <?php echo t('Eliminar'); ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="js\app.js"></script>
</body>
</html>