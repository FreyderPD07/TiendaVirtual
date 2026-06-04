<?php
class ProductModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener todos los productos activos cruzados con su categoría
    public function getActiveProducts() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.estado = 'Activo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Agregar dentro de la clase ProductModel
    
    // Obtener TODOS los productos para la vista administrativa
    public function getAllProductsAdmin() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                ORDER BY p.id_producto DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Insertar un nuevo producto en la BD
    public function createProduct($nombre, $descripcion, $precio, $stock, $id_categoria, $imagen, $estado) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria, imagen, estado) 
                VALUES (:nombre, :descripcion, :precio, :stock, :id_categoria, :imagen, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_categoria' => $id_categoria,
            ':imagen' => $imagen,
            ':estado' => $estado
        ]);
    }

    // Obtener las categorías para el select del formulario
    public function getCategories() {
        $sql = "SELECT * FROM categorias";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un solo producto por su ID
    public function getProductById($id_producto) {
        $sql = "SELECT * FROM productos WHERE id_producto = :id_producto";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetch();
    }

    // Actualizar un producto
    public function updateProduct($id_producto, $nombre, $descripcion, $precio, $stock, $id_categoria, $estado) {
        $sql = "UPDATE productos 
                SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                    stock = :stock, id_categoria = :id_categoria, estado = :estado 
                WHERE id_producto = :id_producto";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
            ':stock' => $stock,
            ':id_categoria' => $id_categoria,
            ':estado' => $estado,
            ':id_producto' => $id_producto
        ]);
    }

    // Eliminar un producto (con protección de llave foránea)
    public function deleteProduct($id_producto) {
        try {
            $sql = "DELETE FROM productos WHERE id_producto = :id_producto";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id_producto' => $id_producto]);
        } catch (PDOException $e) {
            // El código 23000 es la violación de integridad (llave foránea)
            if ($e->getCode() == '23000') {
                return false; 
            }
            // Si es otro tipo de error, que lo muestre
            throw $e;
        }
    }
}