<?php
// Página de higiene: muestra productos de higiene y permite añadir a deseos o carrito
// Incluir configuración y funciones
require_once __DIR__ . '/config.php';

// Aseguramos que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// lista de productos de higiene y limpieza
require_once __DIR__ . '/db.php';
// Incluir clases necesarias para POO
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Higiene.php';
require_once __DIR__ . '/clases/TipoProducto.php';
// Obtener productos de la categoría higiene 
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id_tipo = ?");
$stmt->execute([3]);
$productos_higiene = $stmt->fetchAll();

// Crear objetos Higiene
$higienes = [];
foreach ($productos_higiene as $prod) {
    $stmt_tipo = $pdo->prepare("SELECT nombre FROM tipos_productos WHERE id_tipo = ?");
    $stmt_tipo->execute([$prod['id_tipo']]);
    $nombre_tipo = $stmt_tipo->fetchColumn() ?: 'Higiene';
    $tipo = new TipoProducto($prod['id_tipo'], $nombre_tipo);
    $higienes[] = new Higiene($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>higiene</title>
    <link rel="stylesheet" href="style.css">
</head>
<body <?php echo get_body_attrs(); ?>>
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
            <li><a href="contacto.php">Contacto</a></li>
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

    <?php if (!empty($_SESSION['mensaje_deseo'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje_deseo']; ?></p>
        <?php unset($_SESSION['mensaje_deseo']); ?>
    <?php endif; ?>

    <!-- Mini resumen de la lista de deseos -->
    <div class="mini-deseos">
        <?php
        // Contamos el total de deseos, usando 0 si la lista no existe
        $totalDeseos = count($_SESSION['lista_deseos'] ?? []);
        ?>
        <span class="contador"><?php echo $totalDeseos; ?></span>
        <span> productos en tu lista de deseos</span>

        <?php
        // Mostramos hasta 3 productos de la lista de deseos
        $primeros_deseos = array_slice($_SESSION['lista_deseos'] ?? [], 0, 3);
        foreach ($primeros_deseos as $deseo):
            // Obtenemos la ruta de la imagen (asumiendo que es la ruta o parte de un array de producto)
            $ruta_imagen = is_array($deseo) ? $deseo['imagen'] : $deseo;
        ?>
            <a href="listaDeseos.php">
                <img src="<?php echo $ruta_imagen; ?>" alt="Miniatura de deseo" class="imagen-deseo-mini">
            </a>
        <?php endforeach; ?>
        <?php if ($totalDeseos > 3): ?>
            <span>... y más</span>
        <?php endif; ?>
    </div>

    <!-- Listado de productos de higiene -->
    <div class="productos-lista">
    <h3><?php echo t('Productos destacados'); ?></h3>
    <div class="carrusel-contenedor">
        <button class="carousel-btn prev">‹</button>
        <div class="carousel">
            <div class="carousel-track">
                <?php foreach ($higienes as $higiene): ?>
                    <form class="producto-item" method="post" action="listaDeseos.php" style="max-width:340px;min-width:260px;">
                        <input type="hidden" name="producto_imagen" value="<?php echo htmlspecialchars($higiene->imagen); ?>">
                        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($higiene->nombre); ?>">

                        <img src="<?php echo htmlspecialchars($higiene->imagen); ?>" alt="<?php echo htmlspecialchars($higiene->nombre); ?>" style="width:100%;height:220px;object-fit:contain;">
                        <p class="nombre-producto"><?php echo htmlspecialchars($higiene->nombre); ?></p>

                        <div style="display:flex;justify-content:center;gap:1.2rem;align-items:center;">
                            <button type="submit" name="accion" value="añadir_deseo" title="Añadir a deseos" style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                ❤️
                            </button>
                            <button class="add-to-cart" title="Añadir al carrito"
                                data-id="<?php echo $higiene->id_producto; ?>"
                                data-nombre="<?php echo htmlspecialchars($higiene->nombre); ?>"
                                data-precio="<?php echo htmlspecialchars($higiene->precio); ?>"
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
</div>

    <!-- Script para añadir productos al carrito usando localStorage -->
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
