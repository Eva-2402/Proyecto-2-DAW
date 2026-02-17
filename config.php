<?php
// === INICIO DE CONFIGURACIÓN BÁSICA ===

// Aquí nos aseguramos de que la sesión esté iniciada para poder guardar cosas del usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================
// Preferencias de usuario (tema e idioma)
// =============================

// Miramos si el usuario ya eligió un tema (claro/oscuro) o idioma, y si no, ponemos valores por defecto
$tema_usuario = $_COOKIE['tema'] ?? $_SESSION['tema'] ?? 'light';
$idioma_usuario = $_COOKIE['idioma'] ?? $_SESSION['idioma'] ?? 'es';

// Guardamos las preferencias en la sesión para que se recuerden mientras navega
$_SESSION['tema'] = $tema_usuario;
$_SESSION['idioma'] = $idioma_usuario;

// Esto es para poner la clase CSS correcta en el <body> según el tema elegido
$atributos_body = 'class="' . ($tema_usuario === 'dark' ? 'theme-dark' : 'theme-light') . '"';

// Diccionario de traducciones: aquí puedes poner las frases en español e inglés para que la web sea bilingüe
$traducciones = [
    'es' => [
        'Animalia Mascotas' => 'Animalia Mascotas',
        'Añadir a deseos' => 'Añadir a deseos',
        'Eliminar' => 'Eliminar',
        'Iniciar Sesión' => 'Iniciar Sesión',
        'Preferencias' => 'Preferencias',
        'Selecciona un tema' => 'Selecciona un tema:',
        'Selecciona un idioma' => 'Selecciona un idioma:',
        'Guardar Preferencias' => 'Guardar Preferencias',
        'Claro' => 'Claro',
        'Oscuro' => 'Oscuro',
        'Español' => 'Español',
        'Inglés' => 'Inglés',
        'Hola, has iniciado sesion' => 'Hola, has iniciado sesión',
        'Usuario' => 'Usuario:',
        'Contraseña' => 'Contraseña:',
        'Ingresar' => 'Ingresar',
        'Productos Destacados' => 'Productos Destacados',
        'Categorías' => 'Categorías',
        'Selecciona una categoría' => 'Selecciona una categoría para ver los productos disponibles:',
        'Productos destacados' => 'Productos destacados',
        'Perro' => 'Perro',
        'Gato' => 'Gato',
        'Eliminar preferencia' => 'Eliminar preferencia',
        'Añadir al carrito' => 'Añadir al carrito'
    ],
    'en' => [
        'Animalia Mascotas' => 'Animalia Pets',
        'Añadir a deseos' => 'Add to wishlist',
        'Eliminar' => 'Remove',
        'Iniciar Sesión' => 'Sign In',
        'Preferencias' => 'Preferences',
        'Selecciona un idioma' => 'Select a language:',
        'Guardar Preferencias' => 'Save Preferences',
        'Claro' => 'Light',
        'Oscuro' => 'Dark',
        'Español' => 'Spanish',
        'Inglés' => 'English',
        'Hola, has iniciado sesion' => 'Hi, you have logged in',
        'Usuario' => 'User:',
        'Contraseña' => 'Password:',
        'Ingresar' => 'Log In',
        'Productos Destacados' => 'Featured Products',
        'Categorías' => 'Categories',
        'Selecciona una categoría' => 'Select a category to view available products:',
        'Productos destacados' => 'Featured products',
        'Perro' => 'Dog',
        'Gato' => 'Cat',
        'Eliminar preferencia' => 'Remove preference',
        'Añadir al carrito' => 'Add to cart'
    ]
];

// FUNCIÓN DE TRADUCCIÓN (t)
// Esta función sirve para traducir frases según el idioma elegido
function t(string $key): string {
    global $traducciones, $idioma_usuario;
    // Busca la frase ($key) en el idioma actual. Si no la encuentra, devuelve la frase original ($key).
    return $traducciones[$idioma_usuario][$key] ?? $key;
}

// FUNCIÓN PARA OBTENER ATRIBUTOS DEL BODY
// Esto es para poner la clase CSS correcta en el <body>
function get_body_attrs(): string {
    global $atributos_body;
    return $atributos_body;
}
?>