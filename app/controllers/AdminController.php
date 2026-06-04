<?php
class AdminController {
    
    public function __construct() {
        // Asegurar que la sesión global del index.php sea reconocida
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validar con un echo temporal si te está sacando por falta de datos
        if (!isset($_SESSION['id_rol']) || intval($_SESSION['id_rol']) !== 1) {
            header("Location: " . rtrim(BASE_URL, '/') . "/auth/login");
            exit();
        }
    }

    // Método que carga la vista principal del dashboard
    public function dashboard() {
        // Obtenemos el nombre del usuario de la sesión para saludarlo
        $nombreAdmin = $_SESSION['nombre_completo'];
        
        require_once APP_PATH . '/views/admin/dashboard.phtml';
    }
    

    // Agregar dentro de la clase AdminController
    
    // Método para gestionar el listado de productos
    public function products() {
        // Instanciamos el modelo de productos
        // (Funciona automáticamente gracias a nuestro spl_autoload_register en index.php)
        $productModel = new ProductModel();
        
        // Obtenemos la lista completa
        $productos = $productModel->getAllProductsAdmin();
        $nombreAdmin = $_SESSION['nombre_completo']; // Para mantener el header
        
        // Cargamos la vista
        require_once APP_PATH . '/views/admin/products.phtml';
    }

    public function create_product() {
        $productModel = new ProductModel();
        $nombreAdmin = $_SESSION['nombre_completo'];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $stock = intval($_POST['stock']);
            $id_categoria = intval($_POST['id_categoria']);
            $estado = $_POST['estado'];
            $imagen_nombre = 'default.png'; // Imagen por defecto

            if (empty($nombre) || empty($precio) || empty($id_categoria)) {
                $error = "Por favor, completa los campos obligatorios.";
            } else {
                // Procesar subida de Imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $imagen_nombre = time() . '.' . $ext; // Nombre único para que no se repitan
                    
                    $target_dir = __DIR__ . '/../../public/assets/uploads/';
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true); // Crea la carpeta si no existe
                    }
                    move_uploaded_file($_FILES['imagen']['tmp_name'], $target_dir . $imagen_nombre);
                }

                // Guardar
                if ($productModel->createProduct($nombre, $descripcion, $precio, $stock, $id_categoria, $imagen_nombre, $estado)) {
                    header("Location: " . BASE_URL . "/admin/products");
                    exit();
                } else {
                    $error = "Error al guardar en la base de datos.";
                }
            }
        }

        $categorias = $productModel->getCategories();
        require_once APP_PATH . '/views/admin/create_product.phtml';
    }

    // Método para gestionar el listado de pedidos
    public function orders() {
        // Instanciamos el modelo
        $orderModel = new OrderModel();
        
        // Obtenemos todos los pedidos
        $pedidos = $orderModel->getAllOrdersAdmin();
        $nombreAdmin = $_SESSION['nombre_completo']; 
        
        // Cargamos la vista
        require_once APP_PATH . '/views/admin/orders.phtml';
    }

    public function order_detail() {
        // Verificar que venga el ID del pedido en la URL
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header("Location: " . BASE_URL . "/admin/orders");
            exit();
        }

        $id_pedido = intval($_GET['id']);
        $orderModel = new OrderModel();

        // Obtener datos desde el modelo
        $pedido = $orderModel->getOrderById($id_pedido);
        $detalles = $orderModel->getOrderDetails($id_pedido);
        $nombreAdmin = $_SESSION['nombre_completo'];

        // Si el pedido no existe en la BD, regresamos al listado
        if (!$pedido) {
            header("Location: " . BASE_URL . "/admin/orders");
            exit();
        }

        require_once APP_PATH . '/views/admin/order_detail.phtml';
    }

    // Método para editar producto
    public function edit_product() {
        if (!isset($_GET['id'])) {
            header("Location: " . BASE_URL . "/admin/products");
            exit();
        }

        $id_producto = intval($_GET['id']);
        $productModel = new ProductModel();
        $nombreAdmin = $_SESSION['nombre_completo'];

        // Si se envió el formulario de actualización
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);
            $precio = floatval($_POST['precio']);
            $stock = intval($_POST['stock']);
            $id_categoria = intval($_POST['id_categoria']);
            $estado = $_POST['estado'];

            if ($productModel->updateProduct($id_producto, $nombre, $descripcion, $precio, $stock, $id_categoria, $estado)) {
                header("Location: " . BASE_URL . "/admin/products");
                exit();
            }
        }

        // Obtener datos actuales para rellenar el formulario
        $producto = $productModel->getProductById($id_producto);
        $categorias = $productModel->getCategories();

        require_once APP_PATH . '/views/admin/edit_product.phtml';
    }

    // Método para eliminar producto
    public function delete_product() {
        if (isset($_GET['id'])) {
            $id_producto = intval($_GET['id']);
            $productModel = new ProductModel();
            
            // Intentamos eliminar
            if ($productModel->deleteProduct($id_producto)) {
                // Si se pudo borrar (porque nadie lo ha comprado aún)
                header("Location: " . BASE_URL . "/admin/products");
                exit();
            } else {
                // Si la BD lo impidió porque ya está en un pedido
                echo "<script>
                        alert('⛔ No puedes eliminar este producto porque ya está registrado en el historial de un pedido. Te sugerimos Editarlo y cambiar su estado a INACTIVO.');
                        window.location.href = '" . BASE_URL . "/admin/products';
                      </script>";
                exit();
            }
        } else {
            header("Location: " . BASE_URL . "/admin/products");
            exit();
        }
    }
}