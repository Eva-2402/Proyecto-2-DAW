<?php
// =============================
// CONFIGURACIÓN DE BASE DE DATOS
// =============================

// Aquí ponemos los datos para conectarnos a la base de datos
$servidor_bd = 'localhost'; // Normalmente es 'localhost', a menos que tu base esté en otro servidor
$nombre_bd   = 'tiendica';  // El nombre de la base de datos que creaste
$usuario_bd  = 'root';      // El usuario para entrar a la base (en local suele ser 'root')
$clave_bd    = '';          // La contraseña (en local muchas veces está vacía)
$charset_bd  = 'utf8mb4';   // Esto es para que los acentos y emojis se vean bien

// Ahora intentamos conectarnos a la base de datos usando PDO (es una forma moderna y segura)
try {
    $pdo = new PDO(
        "mysql:host=$servidor_bd;dbname=$nombre_bd;charset=$charset_bd",
        $usuario_bd,
        $clave_bd,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Si hay un error, que lo muestre clarito
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Así los resultados vienen como array con nombres
        ]
    );
    $conexion_bd = $pdo; // Guardamos la conexión en una variable para usarla en otros archivos
} catch (PDOException $excepcion_bd) {
    // Si algo sale mal, mostramos el error y paramos todo
    die('Error de conexión a la base de datos: ' . $excepcion_bd->getMessage());
}
?>
