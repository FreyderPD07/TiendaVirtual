<?php
// app/config/Database.php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Manejo estricto de errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Arrays asociativos por defecto
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Seguridad extra contra Inyección SQL
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En producción, esto debería ir a un log, no mostrarse al usuario
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Evitar clonación del objeto (Singleton)
    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}