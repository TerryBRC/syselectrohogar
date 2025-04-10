<?php
require_once '../config/database.php';
require_once '../models/factura.php';
require_once '../models/inventario.php';

$database = new Database();
$db = $database->getConnection();
$factura = new Factura($db);
$inventario = new Inventario($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'create':
            try {
                $db->beginTransaction();
                
                // Crear factura
                $numeroSAP = 'SAP-' . date('Ymd') . '-' . rand(1000, 9999);
                $_POST['numeroSAP'] = $numeroSAP;
                
                if ($factura->create($_POST)) {
                    $facturaId = $db->lastInsertId();
                    
                    // Procesar detalles
                    foreach ($_POST['productos'] as $producto) {
                        $detalleFactura->create(
                            $facturaId,
                            $producto['id'],
                            $producto['cantidad'],
                            $producto['precio']
                        );
                        
                        // Registrar movimiento en inventario
                        $movimiento = [
                            'producto_id' => $producto['id'],
                            'tipo' => 'Salida',
                            'cantidad' => $producto['cantidad'],
                            'factura_id' => $facturaId
                        ];
                        $inventario->registerMovement($movimiento);
                    }
                    
                    $db->commit();
                    $response['success'] = true;
                    $response['message'] = 'Factura creada exitosamente';
                }
            } catch (Exception $e) {
                $db->rollBack();
                $response['message'] = 'Error al crear la factura';
                error_log($e->getMessage());
            }
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
        case 'list':
            $facturas = $factura->getAll();
            header('Content-Type: application/json');
            echo json_encode($facturas);
            exit();
            
        case 'get_details':
            if (isset($_GET['id'])) {
                $detalles = $factura->getDetails($_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($detalles);
                exit();
            }
            break;
    }
}