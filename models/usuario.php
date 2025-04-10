<?php
class Usuario {
    private $conn;
    private $table_name = "Usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM usuarios 
                  WHERE CorreoElectronico = :email 
                  AND Activo = 1";
        try {
            $query = "SELECT ID_Usuario, CorreoElectronico, Contrasena, Rol 
                     FROM " . $this->table_name . " 
                     WHERE CorreoElectronico = ? AND Activo = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                error_log("User found, verifying password");
                
                // Debug the stored hash
                error_log("Stored hash: " . $row['Contrasena']);
                
                if (password_verify($password, $row['Contrasena'])) {
                    error_log("Password verified successfully");
                    $_SESSION['user_id'] = $row['ID_Usuario'];
                    $_SESSION['email'] = $row['CorreoElectronico'];
                    $_SESSION['rol'] = $row['Rol'];
                    return true;
                } else {
                    error_log("Password verification failed");
                }
            } else {
                error_log("No user found with email: " . $email);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function create($data) {
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $query = "INSERT INTO " . $this->table_name . " 
                    (CorreoElectronico, Contrasena, Rol, Activo) 
                    VALUES (?, ?, ?, 1)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['rol']
            ]);
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
}
?>