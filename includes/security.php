<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkSession() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}

function checkSuperAdminRole() {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'SuperAdmin') {
        header("Location: dashboard.php");
        exit();
    }
}

function checkAdminRole() {
    if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Admin' && $_SESSION['rol'] !== 'SuperAdmin')) {
        header("Location: dashboard.php");
        exit();
    }
}

function checkVendedorRole() {
    if (!isset($_SESSION['rol']) || 
        ($_SESSION['rol'] !== 'Vendedor' && 
         $_SESSION['rol'] !== 'Admin' && 
         $_SESSION['rol'] !== 'SuperAdmin')) {
        header("Location: dashboard.php");
        exit();
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header("HTTP/1.1 403 Forbidden");
            exit('CSRF token validation failed');
        }
    }
}
?>