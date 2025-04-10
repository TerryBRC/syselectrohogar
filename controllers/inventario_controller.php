<?php
require_once '../config/database.php';
require_once '../models/inventario.php';
require_once '../models/producto.php';

$database = new Database();
$db = $database->getConnection();
$inventario = new Inventario($db);
$producto = new Producto($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'register_movement':
            if ($inventario->registerMovement($_POST)) {
                $response['success'] = true;
                $response['message'] = 'Movimiento registrado exitosamente';
            } else {
                $response['message'] = 'Error al registrar el movimiento';
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
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