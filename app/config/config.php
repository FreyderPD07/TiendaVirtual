<?php
// app/config/config.php

// Detectar automáticamente si estamos en Render o en tu computadora
if (isset($_ENV['RENDER']) || getenv('RENDER')) {
    // ☁️ CONFIGURACIÓN PARA RENDER (Modo demostración sin base de datos externa)
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "https://tiendavirtual-render.onrender.com/");
} else {
    // 💻 CONFIGURACIÓN PARA TU COMPUTADORA (Localhost de siempre)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); 
    define('DB_PASS', 'admin'); 
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "http://localhost/tienda_virtual/public/"); 
}

define('APP_PATH', dirname(dirname(__FILE__)));