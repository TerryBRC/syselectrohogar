<?php
require_once '../config/database.php';
require_once '../models/reporte.php';

$database = new Database();
$db = $database->getConnection();
$reporte = new Reporte($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
        case 'sales_report':
            $inicio = $_GET['start_date'] ?? date('Y-m-01');
            $fin = $_GET['end_date'] ?? date('Y-m-t');
            
            $ventas = $reporte->getSalesReport($inicio, $fin);
            header('Content-Type: application/json');
            echo json_encode($ventas);
            exit();
            
        case 'top_products':
            $productos = $reporte->getTopSellingProducts();
            header('Content-Type: application/json');
            echo json_encode($productos);
            exit();
            
        case 'stock_movements':
            $movimientos = $reporte->getStockMovements();
            header('Content-Type: application/json');
            echo json_encode($movimientos);
            exit();
    }
}