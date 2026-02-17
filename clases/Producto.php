<?php
// Clase abstracta base para todos los productos
abstract class Producto {
    public $id_producto;
    public $nombre;
    public $precio;
    public $imagen;
    /**
     * @var TipoProducto $tipo Objeto que representa la categoría del producto
     */
    public $tipo;

    /**
     * Constructor de Producto
     * @param int $id_producto
     * @param string $nombre
     * @param float $precio
     * @param string $imagen
     * @param TipoProducto $tipo
     */
    public function __construct($id_producto, $nombre, $precio, $imagen, TipoProducto $tipo) {
        $this->id_producto = $id_producto;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->imagen = $imagen;
        $this->tipo = $tipo;
    }

    // Método para mostrar información del producto
    public function mostrarInfo() {
        return "<strong>{$this->nombre}</strong> ({$this->tipo->nombre_tipo}) - " . $this->getPrecioFormateado();
    }

    // Devuelve el precio formateado con dos decimales y símbolo de euro
    public function getPrecioFormateado() {
        return number_format($this->precio, 2, ',', '.') . ' €';
    }

    // Permite mostrar el producto como string directamente
    public function __toString() {
        return $this->mostrarInfo();
    }
}
