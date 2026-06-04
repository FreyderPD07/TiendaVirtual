<?php
require_once APP_PATH . '/models/OrderModel.php';

class CustomerController {

    public function __construct() {
        // Bloqueo de seguridad: Si no está logueado, directo al login
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit();
        }
    }

    // Listar las compras del cliente
    public function orders() {
        $orderModel = new OrderModel();
        
        // Buscamos solo los pedidos que le pertenecen al ID en sesión
        $misPedidos = $orderModel->getOrdersByUserId($_SESSION['usuario_id']);

        // Cargamos la vista del historial
        require_once APP_PATH . '/views/customer/orders.phtml';
    }
}