<?php
class Empleado {
    private $conn;
    private $table_name = "Empleados";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        try {
            $query = "SELECT e.*, u.CorreoElectronico, u.Rol 
                     FROM " . $this->table_name . " e
                     LEFT JOIN Usuarios u ON e.ID_Usuario = u.ID_Usuario
                     WHERE e.Activo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employees: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        $query = "INSERT INTO empleados 
                  (ID_Usuario, Nombre, Apellido, Telefono, Direccion, Activo) 
                  VALUES (:userId, :nombre, :apellido, :telefono, :direccion, 1)";
        try {
            $this->conn->beginTransaction();

            // Create user account first with bcrypt password
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $query = "INSERT INTO Usuarios 
                    (CorreoElectronico, Contrasena, Rol, Activo) 
                    VALUES (?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['rol']
            ]);
            
            $userId = $this->conn->lastInsertId();

            // Create employee record
            $query = "INSERT INTO " . $this->table_name . "
                    (ID_Usuario, Nombre, Apellido, DNI, Telefono, Direccion, Activo) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $userId,
                $data['nombre'],
                $data['apellido'],
                $data['dni'],
                $data['telefono'],
                $data['direccion']
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error creating employee: " . $e->getMessage());
            return false;
        }
    }
}
?>