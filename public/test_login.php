<?php
// Forzar a PHP a mostrar absolutamente todos los errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir rutas mínimas requeridas
define('APP_PATH', __DIR__ . '/../app');

// Requerir dependencias básicas
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/UserModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>🧪 Sistema de Diagnóstico de Autenticación</h2>";
echo "<hr>";

// 1. Probar conexión a la Base de Datos
try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ <b>Paso 1:</b> Conexión a la base de datos exitosa.</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <b>Paso 1 Fallido:</b> Error de conexión: " . $e->getMessage() . "</p>";
    exit();
}

// 2. Intentar buscar al Administrador en la BD
// !!! CAMBIA ESTE CORREO por el correo exacto que quieras probar (ej: carlos@admin.com o admin@tienda.com)
$correoAProbar = 'carlos@admin.com'; 
$claveAProbar  = 'admin123';

$userModel = new UserModel();
$usuario = $userModel->getUserByEmail($correoAProbar);

if ($usuario) {
    echo "<p style='color: green;'>✅ <b>Paso 2:</b> Usuario <b>{$correoAProbar}</b> encontrado en la base de datos.</p>";
    echo "<pre>Datos encontrados en la BD:\n";
    print_r([
        'id_usuario' => $usuario['id_usuario'],
        'id_rol' => $usuario['id_rol'],
        'nombre' => $usuario['nombre'],
        'correo' => $usuario['correo'],
        'password_hash' => substr($usuario['password'], 0, 20) . '...' // Solo una muestra del hash
    ]);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ <b>Paso 2 Fallido:</b> El correo <b>{$correoAProbar}</b> no existe en la tabla 'usuarios'. Verifícalo en MySQL Workbench.</p>";
    exit();
}

// 3. Probar la verificación del Hash de la contraseña
echo "<p>⏳ <b>Paso 3:</b> Verificando contraseña usando password_verify()...</p>";
if (password_verify($claveAProbar, $usuario['password'])) {
    echo "<p style='color: green;'>✅ <b>Contraseña CORRECTA!</b> El texto plano '{$claveAProbar}' coincide con el hash almacenado.</p>";
} else {
    echo "<p style='color: red;'>❌ <b>Contraseña INCORRECTA!</b> El texto '{$claveAProbar}' NO coincide con el hash de la base de datos.</p>";
    echo "<p><i>Tip: Si acabas de cambiar el id_rol a mano en MySQL o creaste el usuario desde afuera, es probable que la contraseña se haya guardado mal.</i></p>";
    exit();
}

// 4. Evaluar el Rol y la Sesión
echo "<p>⏳ <b>Paso 4:</b> Evaluando rol para redirección...</p>";
$rolActual = intval($usuario['id_rol']);
echo "El ID de rol convertido a entero es: <b>{$rolActual}</b> (Tipo: " . gettype($rolActual) . ")<br>";

if ($rolActual === 1) {
    echo "<p style='color: blue;'>🚀 <b>Resultado esperado:</b> El sistema detecta que eres <b>ADMINISTRADOR</b> y debería mandarte a <b>/admin/dashboard</b>.</p>";
} else {
    echo "<p style='color: orange;'>🛒 <b>Resultado esperado:</b> El sistema detecta que eres <b>CLIENTE</b> (Rol: {$rolActual}) y debería mandarte a <b>/shop/index</b>.</p>";
}