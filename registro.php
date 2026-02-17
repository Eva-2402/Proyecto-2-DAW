<?php
// Página de registro de nuevos usuarios
// Permite crear un usuario nuevo si el nombre o email no existen
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'] ?? '';

    // Comprobar si el usuario o email ya existen
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM usuarios WHERE nombre = ? OR email = ?"
    );
    $stmt->execute([$nombre, $email]);

    if ($stmt->fetchColumn() > 0) {
        $error = 'El nombre o el email ya existen';
    } else {
        // Hashear la contraseña antes de guardarla
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Insertar nuevo usuario con rol por defecto 'usuario'
        $stmt = $pdo->prepare(
            "INSERT INTO usuarios (nombre, email, password, rol)
             VALUES (?, ?, ?, 'usuario')"
        );
        $stmt->execute([$nombre, $email, $password_hash]);

        // Redirigir al login tras registro exitoso
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <h1>Registro</h1>

    <?php if ($error): ?>
    <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <!-- Formulario de registro de usuario -->
    <form method="post">
        <label>Nombre de usuario:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrarse</button>
    </form>
</body>
</html>

