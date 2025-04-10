<?php
require_once '../includes/security.php';
checkSession();
include_once '../includes/header.php';
require_once '../config/database.php';
require_once '../models/inventario.php';
require_once '../models/producto.php';

// Initialize database and objects
$database = new Database();
$db = $database->getConnection();
$inventario = new Inventario($db);
$producto = new Producto($db);

?>

<div class="dashboard-container">
    <h1>Panel de Control</h1>
    
    <div class="dashboard-layout">
        <div class="dashboard-main">
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Productos</h3>
                    <div class="number" id="totalProductos">0</div>
                    <a href="productos.php">Ver Productos</a>
                </div>
                <div class="card">
                    <h3>Facturas del Mes</h3>
                    <div class="number" id="facturasDelMes">0</div>
                    <a href="facturas.php">Ver Facturas</a>
                </div>
            </div>
        </div>
        
        <div class="dashboard-sidebar">
            <div class="dashboard-card">
                <h3>Productos Bajo Stock</h3>
                <table id="lowStockTable" class="low-stock-table">
                    <tbody>
                        <!-- Contenido dinámico -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/dashboard.js"></script>
<?php include_once '../includes/footer.php'; ?>
