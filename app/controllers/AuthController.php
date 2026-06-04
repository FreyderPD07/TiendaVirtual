<?php
require_once APP_PATH . '/models/UserModel.php';

class AuthController {

    // Mostrar el formulario de Login
    // Mostrar el formulario de Login y procesar la entrada
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $correo = trim($_POST['email'] ?? $_POST['correo'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($correo) || empty($password)) {
                $error = "Por favor, completa todos los campos.";
                require_once APP_PATH . '/views/auth/login.phtml';
                return;
            }

            $userModel = new UserModel();
            $usuario = $userModel->getUserByEmail($correo);

            if ($usuario && password_verify($password, $usuario['password'])) {
                
                $rolActual = intval($usuario['id_rol']);

                // Guardar datos en la sesión global
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['id_rol']     = $rolActual; 
                $_SESSION['rol']        = $rolActual; 
                $_SESSION['nombre_completo'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
                
                // 🔥 LA CLAVE: Forzar a PHP a guardar la sesión en el disco inmediatamente
                session_write_close();

                $baseUrlLimpia = rtrim(BASE_URL, '/');

                if ($rolActual === 1) {
                    header("Location: " . $baseUrlLimpia . "/admin/dashboard");
                    exit();
                } else {
                    header("Location: " . $baseUrlLimpia . "/shop/index");
                    exit();
                }
                
            }
        } else {
            require_once APP_PATH . '/views/auth/login.phtml';
        }
    }
    public function register() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']); // Agregamos el apellido
            $correo = trim($_POST['email']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if ($password !== $password_confirm) {
                $error = "Las contraseñas no coinciden.";
            } else {
                $userModel = new UserModel();
                if ($userModel->registerUser($nombre, $apellido, $correo, $password)) {
                    $success = "¡Registro exitoso! Ya puedes iniciar sesión.";
                } else {
                    $error = "Este correo electrónico ya está registrado.";
                }
            }
        }
        require_once APP_PATH . '/views/auth/register.phtml';
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "/shop/index");
        exit();
    }
}