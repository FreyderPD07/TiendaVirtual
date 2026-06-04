<?php
class OrderModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createOrder($id_usuario, $cartItems, $total) {
        try {
            $this->db->beginTransaction();

            $sqlPedido = "INSERT INTO pedidos (id_usuario, total, estado) VALUES (:id_usuario, :total, 'Pendiente')";
            $stmtPedido = $this->db->prepare($sqlPedido);
            $stmtPedido->execute([
                ':id_usuario' => $id_usuario,
                ':total' => $total
            ]);
            
            $id_pedido = $this->db->lastInsertId(); 

            $sqlDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                           VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario, :subtotal)";
            $stmtDetalle = $this->db->prepare($sqlDetalle);

            foreach ($cartItems as $id_producto => $item) {
                $subtotal = $item['precio'] * $item['cantidad'];
                $stmtDetalle->execute([
                    ':id_pedido' => $id_pedido,
                    ':id_producto' => $id_producto,
                    ':cantidad' => $item['cantidad'],
                    ':precio_unitario' => $item['precio'],
                    ':subtotal' => $subtotal
                ]);
            }

            $this->db->commit();
            return $id_pedido;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Corregido: Agregamos alias 'fecha_pedido' por si en tu tabla se llama simplemente 'fecha'
    public function getAllOrdersAdmin() {
        $sql = "SELECT p.*, COALESCE(u.nombre, 'Cliente Temporal') as cliente_nombre 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                ORDER BY p.id_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Corregido: Aseguramos el alias correcto del producto
    public function getOrderDetails($id_pedido) {
        $sql = "SELECT dp.*, p.nombre as producto_nombre 
                FROM detalle_pedido dp
                INNER JOIN productos p ON dp.id_producto = p.id_producto
                WHERE dp.id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Corregido: Agregamos soporte dinámico para la fecha
    public function getOrderById($id_pedido) {
        $sql = "SELECT p.*, u.nombre as cliente_nombre 
                FROM pedidos p 
                INNER JOIN usuarios u ON p.id_usuario = u.id_usuario 
                WHERE p.id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pedido' => $id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrdersByUserId($id_usuario) {
        $sql = "SELECT *, COALESCE(fecha, fecha_pedido, NOW()) as fecha_pedido FROM pedidos 
                WHERE id_usuario = :id_usuario 
                ORDER BY id_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}