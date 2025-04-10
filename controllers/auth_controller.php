<?php
session_start();
require_once '../config/database.php';
require_once '../models/usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $usuario = new Usuario($db);

    if ($_POST['action'] === 'login') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Add debugging
        error_log("Login attempt - Email: " . $email);
        
        if ($usuario->login($email, $password)) {
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            error_log("Login failed for email: " . $email);
            header("Location: ../index.php?error=1");
            exit();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>