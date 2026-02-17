<?php
// Panel de administración: listado y gestión de productos
// Requiere login y permisos de administrador
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

requireLogin();
requireAdmin();

$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>panel de control</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
    <!-- Menú hamburguesa -->
    <nav class="navbar">
        <input type="checkbox" id="menu-toggle" class="menu-toggle" />
        <label for="menu-toggle" class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <ul class="menu">
            <li><a href="admin_panel.php">Inicio admin</a></li>
            <li><a href="agregar.php">Agregar producto</a></li>
            <li><a href="../index.php">Volver a tienda</a></li>
            <li><a href="../logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
    <h1>Panel de Administración</h1>
    <div class="admin-actions">
        <a href="agregar.php" class="btn">Agregar producto</a>
    </div>
    <div class="admin-table">
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>

            <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['id_producto'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><img src="<?= $p['imagen'] ?>" width="60"></td>
                <td><?= $p['precio'] ?> €</td>
                <td>
                    <a href="modificar.php?id=<?= $p['id_producto'] ?>">Modificar</a> |
                    <a href="eliminar.php?id=<?= $p['id_producto'] ?>">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>



