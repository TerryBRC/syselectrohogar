<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /syselectrohogar/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electro Hogar - Sistema</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/utils.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <img src="../assets/img/logo.png" alt="Electro Hogar">
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="facturas.php">Facturas</a>
            <a href="inventario.php">Inventario</a>
            <a href="empleados.php">Empleados</a>
            <a href="reportes.php">Reportes</a>
            <a href="../controllers/auth_controller.php?action=logout" class="logout-link">Cerrar Sesión</a>
        </div>
    </nav>
    <div class="container">