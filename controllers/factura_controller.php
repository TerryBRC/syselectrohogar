<?php
session_start(); // Add this at the very top
require_once '../config/database.php';
require_once '../models/factura.php';
require_once '../models/inventario.php';

// Add session check right after requires
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sesión de usuario no válida']);
    exit;
}

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
                
                // Validate required data
                if (empty($_POST['numeroFactura']) || empty($_POST['tipoPago']) || 
                    empty($_POST['telefono']) || empty($_POST['numeroSAP']) || 
                    empty($_POST['nombreCliente'])) {
                    throw new Exception('Todos los campos son requeridos');
                }

                // Process products and check stock
                $productos = json_decode($_POST['productos'], true);
                if (empty($productos)) {
                    throw new Exception('No hay productos en la factura');
                }

                // Create invoice data array
                $facturaData = [
                    'numeroFactura' => $_POST['numeroFactura'],
                    'tipoPago' => $_POST['tipoPago'],
                    'telefono' => $_POST['telefono'],
                    'numeroSAP' => $_POST['numeroSAP'],
                    'nombreCliente' => $_POST['nombreCliente']
                ];

                // Create invoice and get ID
                $facturaId = $factura->create($facturaData);
                
                if (!$facturaId) {
                    throw new Exception('Error al crear la factura');
                }

                // Add details and update inventory
                foreach ($productos as $producto) {
                    $factura->addDetail(
                        $facturaId,
                        $producto['id'],
                        $producto['descuento'],
                        $producto['cantidad'],
                        $producto['precio']
                    );

                    // Register inventory movement for sale
                    $query = "CALL sp_RegisterInventoryMovement(?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    
                    $stmt->execute([
                        $producto['id'],
                        'Venta',
                        $producto['cantidad'],
                        intval($_SESSION['user_id']), // Keep the integer conversion
                        $facturaId
                    ]);
                }

                $db->commit();
                $response['success'] = true;
                $response['message'] = 'Factura creada exitosamente';
                $response['facturaId'] = $facturaId;
                
            } catch (Exception $e) {
                $db->rollBack();
                $response['success'] = false;
                $response['message'] = $e->getMessage();
                error_log("Error creating invoice: " . $e->getMessage());
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
                $query = "SELECT 
                            p.ID_Producto, 
                            p.Nombre, 
                            p.Precio,
                            COALESCE(
                                SUM(CASE 
                                    WHEN i.TipoMovimiento = 'Entrada' THEN i.Cantidad
                                    WHEN i.TipoMovimiento = 'Salida' THEN -i.Cantidad
                                    ELSE 0
                                END), 0
                            ) as Stock
                         FROM Productos p 
                         LEFT JOIN Inventario i ON p.ID_Producto = i.ID_Producto AND i.Activo = 1
                         WHERE p.Activo = 1 
                         GROUP BY p.ID_Producto, p.Nombre, p.Precio
                         ORDER BY p.Nombre ASC";
                         
                $stmt = $db->prepare($query);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'products' => $products]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit();
            break;
    }
}