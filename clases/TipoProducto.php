<?php
// Clase para representar la categoría o tipo de producto
class TipoProducto {
    public $id_tipo;
    public $nombre_tipo;


    public function __construct($id_tipo, $nombre_tipo) {
        $this->id_tipo = $id_tipo;
        $this->nombre_tipo = $nombre_tipo;
    }

    // Permite mostrar el tipo como string directamente
    public function __toString() {
        return $this->nombre_tipo;
    }
}

?>