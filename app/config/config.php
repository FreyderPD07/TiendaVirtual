<?php
// app/config/config.php

if (isset($_ENV['RENDER']) || getenv('RENDER')) {
    // ☁️ CONFIGURACIÓN PARA RENDER (Sin la barra del final para evitar el //)
    define('DB_HOST', 'localhost'); 
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "https://tiendavirtual-render.onrender.com"); // <-- Sin "/" al final
    
    // Evita que el sistema muera si falta la base de datos en Render
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0'); 
    
} else {
    // 💻 CONFIGURACIÓN PARA TU COMPUTADORA
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); 
    define('DB_PASS', 'admin'); 
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "http://localhost/tienda_virtual/public/"); 
}

define('APP_PATH', dirname(dirname(__FILE__)));