<?php
// Página del carrito: aquí ves los productos que agregaste y puedes vaciar el carrito o comprar
// Todo lo que ves aquí se maneja con JavaScript y localStorage (o sea, se guarda en tu navegador)
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <link rel="icon" type="image/png" href="images/logo.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Carrito de Compra</h1>

    <!-- Menú de navegación principal -->
    <!-- Este menú te deja moverte por las secciones principales de la tienda -->
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
            <li><a href="login.php">Iniciar Sesión</a></li>
        </ul>
    </div>

    <div id="carrito">
        <!-- Aquí se va a mostrar la lista de productos que tienes en el carrito -->
        <ul id="lista-carrito"></ul>

        <!-- Aquí se muestra el total a pagar -->
        <p id="total"></p>

        <!-- Botón para vaciar todo el carrito -->
        <button id="vaciar-carrito" class="boton-vaciar">Vaciar carrito</button>

        <!-- Botón para comprar lo que tienes en el carrito -->
        <button id="btn-comprar" class="boton-comprar">Comprar</button>

        <!-- Enlace para volver a la página de inicio -->
        <a href="inicio.php" class="volver">⬅ Volver</a>
    </div>

    <script src="js/carrito.js"></script>
</body>
</html>
