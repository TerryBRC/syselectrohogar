<?php
class Empleado {
    private $conn;
    private $table_name = "Empleados";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        try {
            $query = "SELECT 
                        e.ID_Empleado,
                        e.Nombre,
                        e.Apellido,
                        e.Telefono,
                        e.Direccion,
                        u.CorreoElectronico,
                        u.Rol
                     FROM " . $this->table_name . " e
                     LEFT JOIN Usuarios u ON e.ID_Usuario = u.ID_Usuario
                     WHERE e.Activo = 1
                     ORDER BY e.ID_Empleado ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employees: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        try {
            $this->conn->beginTransaction();

            // First create user account
            $query = "INSERT INTO Usuarios 
                    (CorreoElectronico, Contrasena, Rol, Activo) 
                    VALUES (?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['rol']
            ]);
            
            $userId = $this->conn->lastInsertId();

            // Then create employee record
            $query = "INSERT INTO " . $this->table_name . "
                    (ID_Usuario, Nombre, Apellido, Telefono, Direccion, Activo) 
                    VALUES (?, ?, ?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $userId,
                $data['nombre'],
                $data['apellido'],
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

    // Add this method inside the Empleado class
    public function getById($id) {
        try {
            $query = "SELECT 
                        e.ID_Empleado,
                        e.Nombre,
                        e.Apellido,
                        e.Telefono,
                        e.Direccion,
                        u.CorreoElectronico,
                        u.Rol,
                        u.ID_Usuario
                     FROM " . $this->table_name . " e
                     LEFT JOIN Usuarios u ON e.ID_Usuario = u.ID_Usuario
                     WHERE e.ID_Empleado = ? AND e.Activo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employee: " . $e->getMessage());
            return false;
        }
    }

    public function update($data) {
        try {
            $this->conn->beginTransaction();

            // Update employee data
            $query = "UPDATE " . $this->table_name . " 
                     SET Nombre = ?, Apellido = ?, Telefono = ?, Direccion = ?
                     WHERE ID_Empleado = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['nombre'],
                $data['apellido'],
                $data['telefono'],
                $data['direccion'],
                $data['id']
            ]);

            // Update user data
            $query = "UPDATE Usuarios 
                     SET CorreoElectronico = ?, Rol = ?
                     WHERE ID_Usuario = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $data['email'],
                $data['rol'],
                $data['id_usuario']
            ]);

            // Update password only if provided
            if (!empty($data['password'])) {
                $query = "UPDATE Usuarios 
                          SET Contrasena = ?
                          WHERE ID_Usuario = ?";
                
                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    password_hash($data['password'], PASSWORD_BCRYPT),
                    $data['id_usuario']
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error updating employee: " . $e->getMessage());
            return false;
        }
    }
}
?>