<?php
// Página de inicio principal. Muestra productos destacados y menú de navegación.
require_once __DIR__ . '/config.php';
// Asegura que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Redirige si el usuario ya configuró preferencias
if (isset($_SESSION['preferencias']) && $_SESSION['preferencias'] === true) {
    header('Location: preferencias.php');
    exit();
}
// Variables de usuario y productos
$usuario_actual = $_SESSION['usuario'] ?? '';
$mensaje_bienvenida = "¡Holi " . $usuario_actual . "!";
$es_admin = ($usuario_actual === 'admin');

require_once __DIR__ . '/db.php';
// Incluir clases orientadas a objetos
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Cama.php';
require_once __DIR__ . '/clases/Juguete.php';
require_once __DIR__ . '/clases/Comedero.php';
require_once __DIR__ . '/clases/Higiene.php';
require_once __DIR__ . '/clases/Accesorio.php';
require_once __DIR__ . '/clases/TipoProducto.php';

// Obtener productos destacados desde la base de datos 
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id_producto DESC LIMIT 24");
$productos_destacados = $stmt->fetchAll();

// Crear objetos de productos destacados usando POO
$productos_destacados_objetos = [];
foreach ($productos_destacados as $prod) {
    // Obtener el nombre del tipo desde la tabla tipos_productos
    $stmt_tipo = $pdo->prepare("SELECT nombre FROM tipos_productos WHERE id_tipo = ?");
    $stmt_tipo->execute([$prod['id_tipo']]);
    $nombre_tipo = $stmt_tipo->fetchColumn();
    $tipo = new TipoProducto($prod['id_tipo'], $nombre_tipo);
    // Según el tipo, creamos el objeto adecuado
    switch ($prod['id_tipo']) {
        case 1: // Cama
            $producto = new Cama($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
            break;
        case 2: // Juguete
            $producto = new Juguete($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
            break;
        case 3: // Comedero
            $producto = new Comedero($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
            break;
        case 4: // Higiene
            $producto = new Higiene($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
            break;
        case 5: // Accesorio
            $producto = new Accesorio($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
            break;
        default:
            $producto = null;
    }
    if ($producto) $productos_destacados_objetos[] = $producto;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>animalia - inicio 🐾</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body <?php echo get_body_attrs(); if ($es_admin) echo ' data-user-admin="1"'; ?>>
    
    <h1>
        <img src="images/logo.png" alt="Logo Animalia" style="height: 60px; width: 60px; object-fit: contain; vertical-align: middle; border-radius: 50%;">
        <?php echo t('animalia'); ?>
    </h1>

    <div id="menu_hamburguesa">
        <span class="-top"></span>
        <span class="-mid"></span>
        <span class="-bottom"></span>
    </div>
    
    <div class="menu_desplegable">
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="carrito.php">Carrito</a></li>
            <li><a href="listaDeseos.php">Lista de Deseos</a></li>
            <li><a href="registro.php">Registro</a></li>
            <?php
            // Mostrar enlace al panel de control solo si el usuario es admin
            if (!empty($usuario_actual)) {
                // Consultar la base de datos para saber si el usuario es admin
                require_once __DIR__ . '/db.php';
                $stmt_rol = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre = ?");
                $stmt_rol->execute([$usuario_actual]);
                $rol = $stmt_rol->fetchColumn();
                if ($rol === 'admin') {
                    echo '<li><a href="admin/admin_panel.php">Panel de control</a></li>';
                }
            }
            ?>
            <?php if (!empty($usuario_actual)): ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php"><?php echo t('Iniciar Sesión'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if (!empty($usuario_actual)): ?>
        <!-- Mensaje de bienvenida para el usuario logueado -->
        <p class="mensaje-bienvenida">
            <?php echo $mensaje_bienvenida; ?>
            <?php if ($es_admin): ?>
                <img id="animalGif" src="admin_gif.gif" alt="animal" class="admin-gif" style="display:none;" />
            <?php endif; ?>
        </p>
    <?php endif; ?>
    
    <!-- Mini lista de deseos rápida -->
    <div class="mini-deseos">
        <?php $totalDeseos = count($_SESSION['lista_deseos'] ?? []); ?>
        <span class="contador"><?php echo $totalDeseos; ?></span>
        <span> en tu lista</span>
        <?php if (!empty($_SESSION['lista_deseos'])): ?>
            <?php foreach (array_slice($_SESSION['lista_deseos'], 0, 3) as $pd): ?>
                <a href="listaDeseos.php"><img src="<?php echo $pd['imagen'] ?? $pd; ?>" alt="deseo" class="imagen-deseo-mini"></a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <main id="productos">
        <!-- Categorías principales -->
        <h2><?php echo t('Categorías'); ?></h2>
        <p><?php echo t('Selecciona una categoría'); ?></p>
        <ul class="categorias">
            <li><a href="camas.php">Camas</a></li>
            <li><a href="comederos.php">Comederos</a></li>
            <li><a href="higiene.php">Higiene</a></li>
            <li><a href="juguetes.php">Juguetes</a></li>
            <li><a href="accesorios.php">Accesorios</a></li>
        </ul>

        <!-- Productos destacados -->
        <div class="productos-lista">
    <h3><?php echo t('Productos destacados'); ?></h3>

    <div class="carrusel-contenedor">
        <button class="carousel-btn prev">‹</button>
        <div class="carousel">
            <div class="carousel-track">
                <?php foreach ($productos_destacados_objetos as $producto): ?>
                    <form class="producto-item" method="post" action="listaDeseos.php" style="max-width:340px;min-width:260px;">
                        <input type="hidden" name="producto_imagen" value="<?php echo htmlspecialchars($producto->imagen); ?>">
                        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($producto->nombre); ?>">

                        <img src="<?php echo htmlspecialchars($producto->imagen); ?>" alt="<?php echo htmlspecialchars($producto->nombre); ?>" style="width:100%;height:220px;object-fit:contain;">
                        <p class="nombre-producto"><?php echo htmlspecialchars($producto->nombre); ?></p>

                        <div style="display:flex;justify-content:center;gap:1.2rem;align-items:center;">
                            <button type="submit" name="accion" value="añadir_deseo" title="Añadir a deseos" style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                ❤️
                            </button>
                            <button class="add-to-cart" title="Añadir al carrito"
                                data-id="<?php echo $producto->id_producto; ?>"
                                data-nombre="<?php echo htmlspecialchars($producto->nombre); ?>"
                                data-precio="<?php echo htmlspecialchars($producto->precio); ?>"
                                style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                🛒
                            </button>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn next">›</button>
    </div>
    <!-- ANCLA PARA VALORACIÓN (fuera de main para máxima visibilidad) -->
    <div id="valoracion-anchor"></div>
</div>

    </main>
    <!-- Script para añadir productos al carrito (localStorage) -->
    <script>
// ==========================
//  ADD TO CART - CARRITO
// ==========================

// Esperamos a que el DOM esté cargado
document.addEventListener("DOMContentLoaded", () => {

    // Seleccionamos todos los botones "Añadir al carrito"
    document.querySelectorAll(".add-to-cart").forEach(boton => {

        boton.addEventListener("click", (e) => {
            e.preventDefault(); // Evita submit si está dentro de un form

            // Obtener carrito actual o crear uno nuevo
            let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

            // Extraer datos del botón
            const id_producto = Number(boton.dataset.id);
            const nombre = boton.dataset.nombre;

            // Limpieza del precio (por si viene con € o comas)
            let precio = boton.dataset.precio
                .replace("€", "")
                .replace(",", ".")
                .trim();

            precio = Number(precio);

            // Validaciones básicas
            if (!id_producto || !nombre || isNaN(precio)) {
                alert("Error al añadir el producto al carrito.");
                return;
            }

            // Comprobar si el producto ya existe en el carrito
            const productoExistente = carrito.find(
                p => p.id_producto === id_producto
            );

            if (productoExistente) {
                // Si ya existe, solo aumentamos la cantidad
                productoExistente.cantidad++;
            } else {
                // Si no existe, lo añadimos
                carrito.push({
                    id_producto: id_producto,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1
                });
            }

            // Guardar carrito actualizado
            localStorage.setItem("carrito", JSON.stringify(carrito));

            alert(`🛒 "${nombre}" añadido al carrito`);
        });

    });

});
</script>


    <script src="js\app.js"></script>
</body>
</html>