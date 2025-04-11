<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: views/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Electro Hogar - Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Add these bubbles before the login container -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-container">
        <div class="brand" style="align-items: center;
    justify-content: center;">
            <img src="assets/img/logo.png" alt="Electro Hogar">
        </div>
        <form action="controllers/auth_controller.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Iniciar Sesión</button>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    Credenciales incorrectas
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>