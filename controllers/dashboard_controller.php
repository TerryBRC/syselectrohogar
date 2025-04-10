<?php
require_once '../config/database.php';
require_once '../models/producto.php';
require_once '../models/factura.php';
require_once '../models/inventario.php';

$database = new Database();
$db = $database->getConnection();

$producto = new Producto($db);
$factura = new Factura($db);
$inventario = new Inventario($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = [];

    switch ($_GET['action']) {
        case 'get_stats':
            try {
                // Get total active products
                $totalProductos = $producto->getTotalActive();

                // Get current month invoices
                $firstDayOfMonth = date('Y-m-01');
                $lastDayOfMonth = date('Y-m-t');
                $facturasDelMes = $factura->getCountByDateRange($firstDayOfMonth, $lastDayOfMonth);

                // Get low stock products
                $productosStockBajo = $inventario->getLowStockProducts();

                $response = [
                    'success' => true,
                    'totalProductos' => $totalProductos,
                    'facturasDelMes' => $facturasDelMes,
                    'productosStockBajo' => $productosStockBajo
                ];
            } catch (Exception $e) {
                error_log("Error in dashboard stats: " . $e->getMessage());
                $response = [
                    'success' => false,
                    'message' => 'Error al obtener estadísticas'
                ];
            }
            break;

        default:
            $response = [
                'success' => false,
                'message' => 'Acción no válida'
            ];
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}