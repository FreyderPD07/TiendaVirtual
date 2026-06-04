<?php
// --- CONFIGURACIÓN PARA EL SERVIDOR INTEGRADO DE PHP EN TERMINAL ---
if (php_sapi_name() == 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    // Si el archivo físico existe (ej. un CSS, JS o imagen), devuélvelo directamente
    if ($path && __FILE__ !== $path && is_file($path)) {
        return false; 
    }
}
// -------------------------------------------------------------------

// Iniciar sesiones de forma segura si no se ha iniciado antes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requerir archivos de configuración core
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/Database.php';

// Autocarga de clases (Modelos y Controladores)
spl_autoload_register(function ($class_name) {
    if (file_exists(__DIR__ . '/../app/controllers/' . $class_name . '.php')) {
        require_once __DIR__ . '/../app/controllers/' . $class_name . '.php';
    } elseif (file_exists(__DIR__ . '/../app/models/' . $class_name . '.php')) {
        require_once __DIR__ . '/../app/models/' . $class_name . '.php';
    }
});

// Capturar y limpiar la URL amigable de forma robusta
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si el proyecto está corriendo en subcarpeta (ej: /TiendaVirtual/public/...) la limpiamos
if (defined('BASE_URL')) {
    $relative_path = parse_url(BASE_URL, PHP_URL_PATH);
    if ($relative_path && $relative_path !== '/') {
        $request_uri = str_replace($relative_path, '', $request_uri);
    }
}

$request_uri = trim($request_uri, '/');
$url = !empty($request_uri) ? explode('/', $request_uri) : [];

// Determinar Controlador y Método (Valores por defecto si está vacío)
$controllerParam = isset($url[0]) && !empty($url[0]) ? $url[0] : 'auth';
$methodParam     = isset($url[1]) && !empty($url[1]) ? $url[1] : 'login';

$controllerName = ucfirst($controllerParam) . 'Controller';
$methodName     = $methodParam;

// Enrutamiento dinámico con manejo de errores limpio
if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        $params = array_slice($url, 2);
        call_user_func_array([$controller, $methodName], $params);
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Error 404</h1><p>El método <b>{$methodName}</b> no existe en el controlador <b>{$controllerName}</b>.</p>";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404</h1><p>El controlador <b>{$controllerName}</b> no fue encontrado en el sistema.</p>";
}