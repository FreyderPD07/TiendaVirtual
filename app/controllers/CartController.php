<?php

class CartController {
    
    public function __construct() {
        // Asegurarnos de que el carrito exista en la sesión
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Mostrar los productos que están en el carrito
    public function index() {
        require_once APP_PATH . '/views/cart/index.phtml';
    }

    // Añadir producto al carrito
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = intval($_POST['id_producto']);
            $nombre = $_POST['nombre'];
            $precio = floatval($_POST['precio']);
            $cantidad = intval($_POST['cantidad']);

            // Si el producto ya está en el carrito, sumamos la cantidad
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['cantidad'] += $cantidad;
            } else {
                // Si no, lo agregamos como nuevo
                $_SESSION['cart'][$id] = [
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'cantidad' => $cantidad
                ];
            }
        }
        // Redirigir de vuelta al catálogo
        header("Location: " . BASE_URL . "/shop/index");
        exit();
    }

    // Vaciar por completo el carrito
    public function clear() {
        $_SESSION['cart'] = [];
        header("Location: " . BASE_URL . "/cart/index");
        exit();
    }

    // Procesar la compra real conectando Usuario + Base de datos
    public function checkout() {
        // 1. Validar si el cliente inició sesión. Si no, lo mandamos a loguearse
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: " . BASE_URL . "/auth/login");
            exit();
        }

        // 2. Si el carrito está vacío, regresar a la vista del carrito
        if (empty($_SESSION['cart'])) {
            header("Location: " . BASE_URL . "/cart/index");
            exit();
        }

        // 3. Calcular el total acumulado de la venta
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // 4. Cargar el modelo e insertar el pedido real
        require_once APP_PATH . '/models/OrderModel.php';
        $orderModel = new OrderModel();
        
        // Pasamos el ID del usuario dinámico, el carrito y el total calculado
        $id_pedido = $orderModel->createOrder($_SESSION['usuario_id'], $_SESSION['cart'], $total);
        
        if ($id_pedido) {
            // Si se guarda con éxito, vaciamos el carrito de compras de la sesión
            $_SESSION['cart'] = [];
            
            // Guardamos el ID del pedido generado para que la vista success lo use
            $_SESSION['ultimo_pedido'] = $id_pedido;
            
            // Redirigimos de forma limpia a la página de éxito
            header("Location: " . BASE_URL . "/cart/success");
            exit();
        } else {
            // Si falla la base de datos por stock o restricciones, devolvemos un aviso limpio
            echo "<h3>Error: No se pudo procesar tu pedido en la base de datos. Inténtalo de nuevo.</h3>";
            exit();
        }
    }

    // Mostrar pantalla de felicitaciones (Ruta intermedia)
    public function success() {
        if (!isset($_SESSION['ultimo_pedido'])) {
            header("Location: " . BASE_URL . "/shop/index");
            exit();
        }
        
        $id_pedido = $_SESSION['ultimo_pedido'];
        // Limpiamos la variable para que no se quede guardada permanentemente
        unset($_SESSION['ultimo_pedido']); 

        require_once APP_PATH . '/views/cart/success.phtml';
    }
}