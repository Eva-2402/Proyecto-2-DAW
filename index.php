<?php
// =====================================
// Página principal: categorías y productos
// =====================================

// Incluimos la configuración de la base de datos y otras cosas necesarias
require_once __DIR__ . '/config.php';

// Si la sesión no está iniciada, la iniciamos (esto es para saber quién eres en la tienda)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectamos a la base de datos
require_once __DIR__ . '/db.php';

// Incluimos las clases orientadas a objetos
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Cama.php';
require_once __DIR__ . '/clases/Juguete.php';
require_once __DIR__ . '/clases/Comedero.php';
require_once __DIR__ . '/clases/Higiene.php';
require_once __DIR__ . '/clases/Accesorio.php';
require_once __DIR__ . '/clases/TipoProducto.php';

// Sacamos todos los productos de la base de datos para mostrarlos
$consulta_productos = $conexion_bd->query("SELECT * FROM productos");
$productos_todos = $consulta_productos->fetchAll();

// Sacamos los productos destacados (los más nuevos, por ejemplo)
$consulta_destacados = $conexion_bd->query("SELECT * FROM productos ORDER BY id_producto DESC LIMIT 12");
$productos_destacados = $consulta_destacados->fetchAll();

// Creamos objetos de productos destacados usando POO
$productos_destacados_objetos = [];
foreach ($productos_destacados as $prod) {
    // Obtener el nombre del tipo desde la tabla tipos_productos 
    $stmt_tipo = $conexion_bd->prepare("SELECT nombre FROM tipos_productos WHERE id_tipo = ?");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inicio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <!-- Menú de navegación principal -->
    <!-- Este menú te deja moverte por las secciones principales de la tienda -->
    <div class="menu_desplegable">
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="carrito.php">Carrito</a></li>
            <li><a href="listaDeseos.php">Lista de Deseos</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <?php
            // Si hay un usuario logueado, comprobamos si es admin para mostrar el panel de control
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
    <h1>
        <!-- Logo y nombre de la tienda -->
        <img class="loguito" src="images/logo.png" alt="Logo Animalia" style="height: 60px; width: 60px; object-fit: contain; vertical-align: middle; border-radius: 50%;">
        animalia 🐾
    </h1>
    <div id="menu_hamburguesa">
        <span class="-top"></span>
        <span class="-mid"></span>
        <span class="-bottom"></span>
    </div>
    <!-- ...existing code... (el menú ya está arriba, eliminamos duplicado) -->

    <main id="productos">
        <!-- Aquí mostramos las categorías principales de productos -->
        <h2><?php echo t('Categorías'); ?></h2>
        <p><?php echo t('Selecciona una categoría'); ?></p>
        <ul class="categorias">
            <li><a href="camas.php">Camas</a></li>
            <li><a href="comederos.php">Comederos</a></li>
            <li><a href="higiene.php">Higiene</a></li>
            <li><a href="juguetes.php">Juguetes</a></li>
            <li><a href="accesorios.php">Accesorios</a></li>
        </ul>

        <!-- Mini lista de deseos rápida -->
        <!-- Aquí mostramos un resumen rápido de tu lista de deseos (los productos que marcaste como favoritos) -->
        <div class="mini-deseos">
            <?php $total_deseos = count($_SESSION['lista_deseos'] ?? []); ?>
            <span class="contador"><?php echo $total_deseos; ?></span>
            <span> en tu lista</span>
            <?php if (!empty($_SESSION['lista_deseos'])): ?>
                <?php foreach (array_slice($_SESSION['lista_deseos'], 0, 3) as $producto_deseo): ?>
                    <a href="listaDeseos.php"><img src="<?php echo $producto_deseo['imagen'] ?? $producto_deseo; ?>" alt="deseo"></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Productos destacados -->
        <!-- Aquí mostramos los productos destacados en un carrusel para que puedas ver lo más nuevo o popular -->
        <div class="productos-lista">
    <h3><?php echo t('Productos destacados'); ?></h3>

    <div class="carrusel-contenedor">
        <button class="carousel-btn prev">‹</button>
        <div class="carousel">
            <div class="carousel-track">
                <?php foreach ($productos_destacados_objetos as $producto): ?>
                    <form class="producto-item" method="post" action="listaDeseos.php" style="max-width:340px;min-width:260px;">
                        <!-- Guardamos la imagen y el nombre del producto en campos ocultos para la lista de deseos -->
                        <input type="hidden" name="producto_imagen" value="<?php echo htmlspecialchars($producto->imagen); ?>">
                        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($producto->nombre); ?>">

                        <!-- Imagen del producto -->
                        <img src="<?php echo htmlspecialchars($producto->imagen); ?>" alt="<?php echo htmlspecialchars($producto->nombre); ?>" style="width:100%;height:220px;object-fit:contain;">
                        <p class="nombre-producto"><?php echo htmlspecialchars($producto->nombre); ?></p>
                        <div style="display:flex;justify-content:center;gap:1.2rem;align-items:center;">
                            <!-- Botón para añadir a la lista de deseos -->
                            <button type="submit" name="accion" value="añadir_deseo" title="Añadir a deseos" style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                ❤️
                            </button>
                            <!-- Botón para añadir al carrito (usa JS) -->
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

    </main>

    <script>
// ==========================
//  Lógica para añadir al carrito (localStorage)
// ==========================

// Esperamos a que la página cargue para que todo esté listo
document.addEventListener("DOMContentLoaded", () => {
    // Seleccionamos todos los botones que tienen la clase 'add-to-cart'
    document.querySelectorAll(".add-to-cart").forEach(boton => {
        // Cuando le das click al botón de carrito...
        boton.addEventListener("click", (evento) => {
            evento.preventDefault(); // Así evitamos que el formulario se envíe y recargue la página

            // Recuperamos el carrito actual del localStorage, o creamos uno nuevo si no existe
            let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

            // Sacamos los datos del producto del botón
            const id_producto = Number(boton.dataset.id);
            const nombre_producto = boton.dataset.nombre;
            let precio_producto = boton.dataset.precio
                .replace("€", "")
                .replace(",", ".")
                .trim();
            precio_producto = Number(precio_producto);

            // Comprobamos que los datos sean correctos
            if (!id_producto || !nombre_producto || isNaN(precio_producto)) {
                alert("Error al añadir el producto al carrito.");
                return;
            }

            // Buscamos si el producto ya está en el carrito
            const producto_existente = carrito.find(
                p => p.id_producto === id_producto
            );

            if (producto_existente) {
                // Si ya existe, solo aumentamos la cantidad
                producto_existente.cantidad++;
            } else {
                // Si no existe, lo agregamos al carrito
                carrito.push({
                    id_producto: id_producto,
                    nombre: nombre_producto,
                    precio: precio_producto,
                    cantidad: 1
                });
            }

            // Guardamos el carrito actualizado en el localStorage
            localStorage.setItem("carrito", JSON.stringify(carrito));
            alert(`🛒 "${nombre_producto}" añadido al carrito`);
        });
    });
});
    </script>


    <script src="js\app.js"></script>
</body>
</html>
