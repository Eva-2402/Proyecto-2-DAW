<?php
// Iniciamos la sesión para poder saber quién eres
session_start();

// Esta función comprueba si hay un usuario logueado
function isLogged() {
    return isset($_SESSION['usuario']);
}

// Esta función comprueba si el usuario logueado es admin
function isAdmin() {
    if (!isset($_SESSION['usuario'])) return false;
    // Consultamos el rol del usuario en la base de datos
    require_once __DIR__ . '/db.php';
    global $pdo;
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE nombre = ?");
    $stmt->execute([$_SESSION['usuario']]);
    $rol = $stmt->fetchColumn();
    return $rol === 'admin';
}

// Si el usuario no está logueado, lo mandamos a la página de login
function requireLogin() {
    if (!isLogged()) {
        header('Location: login.php');
        exit;
    }
}

// Si el usuario no es admin, lo mandamos a la página principal
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}
?>