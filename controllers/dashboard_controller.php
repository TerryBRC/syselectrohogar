<?php
require_once '../config/config.php';  // Add this line first
require_once '../config/database.php';
require_once '../models/producto.php';
require_once '../models/factura.php';
require_once '../models/inventario.php';

// Ensure no output has been sent
if (headers_sent()) {
    die(json_encode(['success' => false, 'message' => 'Headers already sent']));
}

// Prevent any output buffering issues
ob_clean();

header('Content-Type: application/json');

try {
    require_once '../config/database.php';
    require_once '../models/producto.php';
    require_once '../models/factura.php';
    require_once '../models/inventario.php';

    $database = new Database();
    $db = $database->getConnection();

    $producto = new Producto($db);
    $factura = new Factura($db);
    $inventario = new Inventario($db);

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_stats') {
        // Get total active products
        $totalProductos = $producto->getTotalActive();

        // Get current month invoices
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $facturasDelMes = $factura->getCountByDateRange($firstDayOfMonth, $lastDayOfMonth);

        // Get low stock products
        $productosStockBajo = $inventario->getLowStockProducts();

        echo json_encode([
            'success' => true,
            'totalProductos' => $totalProductos,
            'facturasDelMes' => $facturasDelMes,
            'productosStockBajo' => $productosStockBajo
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;