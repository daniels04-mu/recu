<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../modelos/ProductoModelo.php';

class ProductoControlador {
    private $modelo;

    public function __construct() {
        global $pdo;
        $this->modelo = new ProductoModelo($pdo);
    }

    public function listar() {
        return $this->modelo->obtenerTodos();
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;

            if (!empty($nombre) && $precio > 0) {
                $this->modelo->crear($nombre, $precio, $stock);
                header("Location: productos.php?exito=1");
                exit();
            }
        }
    }
}