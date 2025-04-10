<?php
require_once('../config/database.php');

try {
    // Create database instance and get connection
    $database = new Database();
    $conn = $database->getConnection();

    $email = 'electro.sqladmin@electrohogar.com';
    $password = 'admin@123';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $role = 'SuperAdmin';

    $sql = "INSERT INTO usuarios (CorreoElectronico, Contrasena, Rol, Activo) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email, $hashedPassword, $role]);

    echo "SuperAdmin user created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>