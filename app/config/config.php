<?php
// app/config/config.php

// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Cambia según tu entorno
define('DB_PASS', 'admin');     // Cambia según tu entorno
define('DB_NAME', 'tienda_virtual');
define('DB_CHARSET', 'utf8mb4');

// Rutas de la aplicación
// Modifica esto según la ruta de tu localhost (ej. http://localhost/tienda_virtual/public)
define("BASE_URL", "https://tiendavirtual-render.onrender.com/");
define('APP_PATH', dirname(dirname(__FILE__)));