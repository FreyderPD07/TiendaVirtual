<?php

class UserModel {
    private $db;

    public function __construct() {
        // Adaptado al patrón Singleton que usa tu proyecto
        $this->db = Database::getInstance()->getConnection();
    }

    // Buscar un usuario por su correo electrónico
    public function getUserByEmail($correo) {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return $stmt->fetch();
    }

    // Registrar un nuevo cliente
    public function registerUser($nombre, $apellido, $correo, $password) {
        // Verificamos si el correo ya existe para no duplicarlo
        if ($this->getUserByEmail($correo)) {
            return false; 
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Suponiendo que en tu tabla 'roles', el ID para 'Cliente' es 2 
        // (Si el 1 es Administrador, el 2 suele ser Cliente. Cámbialo si es necesario)
        $id_rol = 2; 

        $sql = "INSERT INTO usuarios (id_rol, nombre, apellido, correo, password) 
                VALUES (:id_rol, :nombre, :apellido, :correo, :password)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id_rol' => $id_rol,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':password' => $password_hash
        ]);
    }
}