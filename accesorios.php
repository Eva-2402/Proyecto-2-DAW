<?php
// Página de accesorios: muestra productos de la categoría accesorios
require_once __DIR__ . '/config.php';
// Asegura que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
// Incluir clases necesarias para POO
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Accesorio.php';
require_once __DIR__ . '/clases/TipoProducto.php';
// Obtener productos de la categoría accesorios 
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id_tipo = ?");
$stmt->execute([5]);
$productos_accesorios = $stmt->fetchAll();

// Crear objetos Accesorio
$accesorios = [];
foreach ($productos_accesorios as $prod) {
    // Si quieres el nombre real de la categoría, puedes consultarlo en la tabla tipos_productos
    $stmt_tipo = $pdo->prepare("SELECT nombre FROM tipos_productos WHERE id_tipo = ?");
    $stmt_tipo->execute([$prod['id_tipo']]);
    $nombre_tipo = $stmt_tipo->fetchColumn() ?: 'Accesorio';
    $tipo = new TipoProducto($prod['id_tipo'], $nombre_tipo);
    $accesorios[] = new Accesorio($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
    <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>accesorios</title>
    <link rel="stylesheet" href="style.css">
</head>
<body <?php echo get_body_attrs(); ?>>
    <h1>accesorios 🎀</h1>

    <div id="menu_hamburguesa">
        <span class="-top"></span>
        <span class="-mid"></span>
        <span class="-bottom"></span>
    </div>

    <!-- Menú de navegación principal -->
    <div class="menu_desplegable">
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="carrito.php">Carrito</a></li>
            <li><a href="listaDeseos.php">Lista de Deseos</a></li>
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

    <?php if (!empty($_SESSION['mensaje_deseo'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje_deseo']; ?></p>
        <?php unset($_SESSION['mensaje_deseo']); ?>
    <?php endif; ?>

    <div class="mini-deseos">
        <?php
        // Contamos los productos de la lista de deseos. Si no existe, es 0.
        $totalDeseos = count($_SESSION['lista_deseos'] ?? []);
        $primeros_deseos = array_slice($_SESSION['lista_deseos'] ?? [], 0, 3);
        foreach ($primeros_deseos as $deseo):
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

    <div class="productos-lista">
    <h3><?php echo t('Productos destacados'); ?></h3>
    <div class="carrusel-contenedor">
        <button class="carousel-btn prev">‹</button>
        <div class="carousel">
            <div class="carousel-track">
                <?php foreach ($accesorios as $accesorio): ?>
                    <form class="producto-item" method="post" action="listaDeseos.php" style="max-width:340px;min-width:260px;">
                        <input type="hidden" name="producto_imagen" value="<?php echo htmlspecialchars($accesorio->imagen); ?>">
                        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($accesorio->nombre); ?>">

                        <img src="<?php echo htmlspecialchars($accesorio->imagen); ?>" alt="<?php echo htmlspecialchars($accesorio->nombre); ?>" style="width:100%;height:220px;object-fit:contain;">
                        <p class="nombre-producto"><?php echo htmlspecialchars($accesorio->nombre); ?></p>

                        <div style="display:flex;justify-content:center;gap:1.2rem;align-items:center;">
                            <button type="submit" name="accion" value="añadir_deseo" title="Añadir a deseos" style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                ❤️
                            </button>
                            <button class="add-to-cart" title="Añadir al carrito"
                                data-id="<?php echo $accesorio->id_producto; ?>"
                                data-nombre="<?php echo htmlspecialchars($accesorio->nombre); ?>"
                                data-precio="<?php echo htmlspecialchars($accesorio->precio); ?>"
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