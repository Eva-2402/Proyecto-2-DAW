<?php
session_start();
header('Content-Type: application/json');

// Mostrar errores SOLO en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";

// ==========================
// VALIDAR USUARIO LOGUEADO
// ==========================
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Debes iniciar sesión para realizar la compra."
    ]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

// ==========================
// LEER JSON DEL FETCH
// ==========================
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["carrito"]) || empty($input["carrito"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "El carrito está vacío."
    ]);
    exit;
}

$productos = $input["carrito"];

// ==========================
// PROCESAR COMPRA
// ==========================
try {
    $pdo->beginTransaction();

    // Calcular total
    $total = 0;
    foreach ($productos as $p) {
        $total += $p["precio"] * $p["cantidad"];
    }

    // Insertar venta
    $stmtVenta = $pdo->prepare(
        "INSERT INTO ventas (id_usuario, total) VALUES (?, ?)"
    );
    $stmtVenta->execute([$id_usuario, $total]);

    $id_venta = $pdo->lastInsertId();

    // Insertar detalle
    $stmtDetalle = $pdo->prepare("
        INSERT INTO detalle_ventas 
        (id_venta, id_producto, cantidad, precio_unitario)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($productos as $p) {
        $stmtDetalle->execute([
            $id_venta,
            $p["id_producto"],
            $p["cantidad"],
            $p["precio"]
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "mensaje" => "Compra realizada con éxito.",
        "id_venta" => $id_venta
    ]);
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        "success" => false,
        "mensaje" => "Error en la compra",
        "detalle" => $e->getMessage()
    ]);
    exit;
}
