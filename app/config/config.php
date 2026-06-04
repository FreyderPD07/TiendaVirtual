<?php
// app/config/config.php

// Si el host es false (como en Render), salta la conexión para que no de error
if (DB_HOST === 'false') {
    return null; 
}

if (isset($_ENV['RENDER']) || getenv('RENDER')) {
    // ☁️ SI ESTÁ EN RENDER: Engañamos al sistema para que no use base de datos real
    define('DB_HOST', 'false'); 
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "https://tiendavirtual-render.onrender.com/");
} else {
    // 💻 EN TU COMPUTADORA: Sigue funcionando normal con tu Workbench
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); 
    define('DB_PASS', 'admin'); 
    define('DB_NAME', 'tienda_virtual');
    define('DB_CHARSET', 'utf8mb4');
    define("BASE_URL", "http://localhost/tienda_virtual/public/"); 
}

define('APP_PATH', dirname(dirname(__FILE__)));