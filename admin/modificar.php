<?php
// Página de administración: modificar producto
// Requiere login y permisos de administrador
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../auth.php';

requireLogin();
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $id_tipo = $_POST['id_tipo'];
    $imagen = $producto['imagen']; // Por defecto, la imagen actual

    // Si se sube una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen']['tmp_name'];
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $destino = '../images/' . $nombre_archivo;
        if (move_uploaded_file($tmp_name, $destino)) {
            $imagen = 'images/' . $nombre_archivo;
        }
    }

    $stmt = $pdo->prepare(
        "UPDATE productos 
         SET nombre = ?, imagen = ?, precio = ?, id_tipo = ?
         WHERE id_producto = ?"
    );
    $stmt->execute([$nombre, $imagen, $precio, $id_tipo, $id]);

    header('Location: admin_panel.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>modificar producto</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
    <h1>Modificar producto</h1>

    <form method="post" enctype="multipart/form-data">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br><br>

        <label>Tipo de producto:</label><br>
        <select name="id_tipo" required>
            <?php
            // Obtener tipos de productos
            $tipos = $pdo->query("SELECT id_tipo, nombre FROM tipos_productos")->fetchAll();
            foreach ($tipos as $tipo):
            ?>
                <option value="<?= $tipo['id_tipo'] ?>" <?= $producto['id_tipo'] == $tipo['id_tipo'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Imagen actual:</label><br>
        <img src="../<?= $producto['imagen'] ?>" alt="Imagen actual" style="max-width:120px;"><br>
        <label>Cambiar imagen:</label><br>
        <input type="file" name="imagen" accept="image/*"><br><br>

        <label>Precio (€):</label><br>
        <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required><br><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>

