<?php
require_once '../db.php';
require_once '../auth.php';

requireLogin();
requireAdmin();

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = trim($_POST['precio']);
    $id_tipo = $_POST['id_tipo'];
    $imagen = '';

    // Manejar subida de imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen']['tmp_name'];
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $destino = '../images/' . $nombre_archivo;
        if (move_uploaded_file($tmp_name, $destino)) {
            $imagen = $nombre_archivo;
        }
    }

    if (!$nombre || !$precio || !$imagen || !$id_tipo) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!is_numeric($precio)) {
        $error = "El precio debe ser un número válido.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO productos (nombre, imagen, precio, id_tipo) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$nombre, $imagen, $precio, $id_tipo]);

        $exito = "Producto agregado correctamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>agregar producto</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
</head>
<body>
    <h1>Agregar nuevo producto</h1>

    <?php if ($error): ?>
    <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
    <p style="color:green"><?= $exito ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Nombre del producto:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Tipo de producto:</label><br>
        <select name="id_tipo" required>
            <?php
            $tipos = $pdo->query("SELECT id_tipo, nombre FROM tipos_productos")->fetchAll();
            foreach ($tipos as $tipo):
            ?>
                <option value="<?= $tipo['id_tipo'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Precio (€):</label><br>
        <input type="number" step="0.01" name="precio" required><br><br>

        <label>Imagen:</label><br>
        <input type="file" name="imagen" accept="image/*" required><br><br>

        <button type="submit">Agregar Producto</button>
    </form>


    <p><a href="admin_panel.php">Volver al panel de admin</a></p>
</body>
</html>

