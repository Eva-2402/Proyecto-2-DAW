<?php
// Incluir configuración y funciones
require_once __DIR__ . '/config.php';

// Redirigir si no es una petición POST (evita acceso directo al archivo)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: login.php');
    exit();
}

// Inicializamos la lista de errores
$errores = [];

// Función de validación (fuera del bloque principal para mayor claridad)
function validar_requerido(string $valor) : bool {
    return trim($valor) !== '';
}

// Obtener los datos del formulario
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// --- Lógica de Validación de Entrada ---

// Validar que los campos no estén vacíos
if (!validar_requerido($usuario)) {
    $errores[] = "El campo usuario es obligatorio.";
}
if (!validar_requerido($password)) {
    $errores[] = "El campo contraseña es obligatorio.";
}

// Si no hay errores de entrada, validar las credenciales
if (empty($errores)) {
    // Solo autenticación con usuarios registrados en la base de datos
    require_once 'db.php';
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = ?");
    $stmt->execute([$usuario]);
    $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($usuario_db && password_verify($password, $usuario_db['password'])) {
        // Login correcto
        $_SESSION['usuario'] = $usuario_db['nombre']; 
        $_SESSION['id_usuario'] = $usuario_db['id_usuario']; // ← AÑADIDO
        $_SESSION['preferencias'] = true;

        header("Location: preferencias.php");
        exit();
    } else {
        // --- AUTENTICACIÓN FALLIDA ---
        $errores[] = "Usuario o contraseña incorrectos.";
    }
}

// MANEJO DE ERRORES

if (!empty($errores)) {
    // Unimos los errores en una sola cadena
    // pasamos solo el mensaje de error de credenciales
    if (in_array("Usuario o contraseña incorrectos.", $errores)) {
        $_SESSION['error_login'] = "Usuario o contraseña incorrectos. Por favor, inténtalo de nuevo.";
    } else {
        // enviar al usuario los errores de campos vacíos
        $_SESSION['error_login'] = implode('<br>', $errores);
    }
    
    // Redirigir de vuelta al formulario de inicio de sesión
    header("Location: login.php");
    exit();
}