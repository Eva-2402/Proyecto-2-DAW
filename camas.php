<?php
// Página de camas: muestra productos de camas y permite añadir a deseos o carrito
// incluir configuración y funciones
require_once __DIR__ . '/config.php';

// aseguramos que la sesión está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// lista de productos de camas
require_once __DIR__ . '/db.php';
// Incluir clases necesarias para POO
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Cama.php';
require_once __DIR__ . '/clases/TipoProducto.php';
// Obtener productos de la categoría camas 
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id_tipo = ?");
$stmt->execute([1]);
$productos_camas = $stmt->fetchAll();

// Crear objetos Cama
$camas = [];
foreach ($productos_camas as $prod) {
    // Si quieres el nombre real de la categoría, puedes consultarlo en la tabla tipos_productos
    $stmt_tipo = $pdo->prepare("SELECT nombre FROM tipos_productos WHERE id_tipo = ?");
    $stmt_tipo->execute([$prod['id_tipo']]);
    $nombre_tipo = $stmt_tipo->fetchColumn() ?: 'Cama';
    $tipo = new TipoProducto($prod['id_tipo'], $nombre_tipo);
    $camas[] = new Cama($prod['id_producto'], $prod['nombre'], $prod['precio'], $prod['imagen'], $tipo);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual ?? 'es'; ?>">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>camas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body <?php echo get_body_attrs(); ?>>
    <h1>Camitas 😴</h1>

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
        // Contamos cuántos productos hay en la lista, usando 0 si está vacía
        $totalDeseos = count($_SESSION['lista_deseos'] ?? []);

        ?>
        <span class="contador"><?php echo $totalDeseos; ?></span>
        <span> productos en tu lista de deseos</span>

        <?php
        // Mostramos hasta 3 productos de la lista de deseos
        $primeros_deseos = array_slice($_SESSION['lista_deseos'] ?? [], 0, 3);
        foreach ($primeros_deseos as $deseo):
            // Asumimos que $deseo es la ruta de la imagen o un array con la clave 'imagen'
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

    <!-- Listado de productos de camas -->
    <div class="productos-lista">
    <h3><?php echo t('Productos destacados'); ?></h3>
    <div class="carrusel-contenedor">
        <button class="carousel-btn prev">‹</button>
        <div class="carousel">
            <div class="carousel-track">
                <?php foreach ($camas as $cama): ?>
                    <form class="producto-item" method="post" action="listaDeseos.php" style="max-width:340px;min-width:260px;">
                        <input type="hidden" name="producto_imagen" value="<?php echo htmlspecialchars($cama->imagen); ?>">
                        <input type="hidden" name="producto_nombre" value="<?php echo htmlspecialchars($cama->nombre); ?>">

                        <img src="<?php echo htmlspecialchars($cama->imagen); ?>" alt="<?php echo htmlspecialchars($cama->nombre); ?>" style="width:100%;height:220px;object-fit:contain;">
                        <p class="nombre-producto"><?php echo htmlspecialchars($cama->nombre); ?></p>
                        <div style="display:flex;justify-content:center;gap:1.2rem;align-items:center;">
                            <button type="submit" name="accion" value="añadir_deseo" title="Añadir a deseos" style="background:none;border:none;font-size:2rem;cursor:pointer;">
                                ❤️
                            </button>
                            <button class="add-to-cart" title="Añadir al carrito"
                                data-id="<?php echo $cama->id_producto; ?>"
                                data-nombre="<?php echo htmlspecialchars($cama->nombre); ?>"
                                data-precio="<?php echo htmlspecialchars($cama->precio); ?>"
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
        document.querySelectorAll(".add-to-cart").forEach(boton => {
            boton.addEventListener("click", function (e) {
                e.preventDefault(); // evita envío del formulario
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                // limpia posibles espacios y símbolos
                let precio = this.dataset.precio
                    .replace("€", "")
                    .replace(",", ".")
                    .trim();
                carrito.push({
                    nombre: this.dataset.nombre,
                    precio: Number(precio),
                    cantidad: 1
                });
                localStorage.setItem("carrito", JSON.stringify(carrito));
                alert("Producto añadido al carrito");
            });
        });
    </script>

    <script src="js\app.js"></script>
</body>
</html>