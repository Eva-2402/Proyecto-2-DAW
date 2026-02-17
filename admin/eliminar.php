<?php
// Página de administración: eliminar producto
// Requiere login y permisos de administrador
require_once '../db.php';
require_once '../auth.php';

requireLogin();
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_panel.php');
    exit;
}


$eliminado = false;
if (isset($_POST['confirmar'])) {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->execute([$id]);
    $eliminado = true;
}

if (isset($_POST['cancelar'])) {
    header('Location: admin_panel.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar producto</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <style>
        .modal-alert {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-content {
            background: var(--color-bg-primary, #fff);
            padding: 32px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            min-width: 320px;
            text-align: center;
        }
        .modal-content h2 {
            margin-bottom: 18px;
        }
        .modal-content p {
            margin-bottom: 28px;
        }
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 24px;
        }
        .modal-buttons button {
            background: var(--color-primary, #A8C3B4);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .modal-buttons button:hover {
            background: var(--color-primary-accent, #6F8F7D);
        }
        .success-msg {
            color: var(--color-success, #27AE60);
            font-weight: bold;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="modal-alert">
        <div class="modal-content">
            <?php if ($eliminado): ?>
                <div class="success-msg">¡Se ha eliminado correctamente!</div>
                <div class="modal-buttons">
                    <a href="admin_panel.php"><button>Volver al panel</button></a>
                </div>
            <?php else: ?>
                <h2>Eliminar producto</h2>
                <p>¿Estás seguro de que quieres eliminar este producto?</p>
                <form method="post">
                    <div class="modal-buttons">
                        <button name="confirmar" type="submit">Sí, eliminar</button>
                        <button name="cancelar" type="submit">Cancelar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

