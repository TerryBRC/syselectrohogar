<?php
require_once '../includes/security.php'; // Make sure this is included
require_once '../config/database.php';
require_once '../models/inventario.php';
require_once '../models/producto.php';

$database = new Database();
$db = $database->getConnection();
$inventario = new Inventario($db);
$producto = new Producto($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'Usuario no autenticado';
        echo json_encode($response);
        exit();
    }

    switch ($_POST['action']) {
        case 'register_movement':
            try {
                if ($inventario->registerMovement($_POST)) {
                    $response['success'] = true;
                    $response['message'] = 'Movimiento registrado exitosamente';
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
        case 'list':
            try {
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = 10;
                
                $movements = $inventario->getMovements($page, $limit);
                $total = $inventario->getTotalMovements();
                $totalPages = ceil($total / $limit);
                
                echo json_encode([
                    'success' => true,
                    'movements' => $movements,
                    'total_pages' => $totalPages,
                    'current_page' => $page
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al cargar los movimientos']);
            }
            exit();
            break;
            
        case 'get_stock':
            if (isset($_GET['producto_id'])) {
                $stock = $inventario->getCurrentStock($_GET['producto_id']);
                header('Content-Type: application/json');
                echo json_encode(['stock' => $stock]);
                exit();
            }
            break;
    }
}