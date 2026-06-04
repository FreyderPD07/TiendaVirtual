<?php
// app/config/config.php

if (isset($_ENV['RENDER']) || getenv('RENDER')) {
    // ☁️ CONFIGURACIÓN PARA RENDER (Modo simulación para ver el diseño)
    define('DB_HOST', 'localhost'); // Si te da error, puedes comentarlo o cambiarlo
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "https://tiendavirtual-render.onrender.com/");
    
    // TRUCO: Si estás en Render, esto evita que el código muera por culpa de la base de datos
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0'); 
    
} else {
    // 💻 CONFIGURACIÓN PARA TU COMPUTADORA (Localhost Workbench)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); 
    define('DB_PASS', 'admin'); 
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "http://localhost/tienda_virtual/public/"); 
}

define('APP_PATH', dirname(dirname(__FILE__)));