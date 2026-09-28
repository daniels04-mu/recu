<?php
class ProductoModelo {
    private $pdo;

    public function __construct($conexionPdo) {
        $this->pdo = $conexionPdo;
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM productos");
        return $stmt->fetchAll();
    }

    public function crear($nombre, $precio, $stock) {
        $sql = "INSERT INTO productos (nombre, precio, stock) VALUES (:nombre, :precio, :stock)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nombre' => $nombre,
            'precio' => $precio,
            'stock' => $stock
        ]);
    }
}