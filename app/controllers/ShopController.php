<?php
class ShopController {
    private $productModel;

    public function __construct() {
        // Instanciamos el modelo de productos
        $this->productModel = new ProductModel();
    }

    public function index() {
        // 1. Obtener los productos desde la base de datos
        $productos = $this->productModel->getActiveProducts();
        
        // 2. Cargar la vista pasándole los productos
        require_once APP_PATH . '/views/shop/index.phtml';
    }
}