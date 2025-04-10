<?php
require_once '../config/database.php';
require_once '../models/producto.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'create':
            if ($producto->create($_POST)) {
                $response['success'] = true;
                $response['message'] = 'Producto creado exitosamente';
            } else {
                $response['message'] = 'Error al crear el producto';
            }
            break;

        case 'delete':
            if (isset($_POST['id']) && $producto->delete($_POST['id'])) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar el producto';
            }
            break;

        case 'restore':
            if (isset($_POST['id']) && $producto->restore($_POST['id'])) {
                $response['success'] = true;
                $response['message'] = 'Producto restaurado exitosamente';
            } else {
                $response['message'] = 'Error al restaurar el producto';
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = ['success' => false, 'message' => ''];

    switch ($_GET['action']) {
        case 'list':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            
            try {
                $products = $producto->getAll($page, $limit);
                $totalPages = $producto->getTotalPages($limit);
                
                $response = [
                    'success' => true,
                    'products' => $products,
                    'total_pages' => $totalPages,
                    'current_page' => $page
                ];
            } catch (Exception $e) {
                $response['message'] = 'Error al cargar los productos';
            }
            break;

        case 'list_inactive':
            try {
                $products = $producto->getInactiveProducts(); // We'll create this method
                $response = [
                    'success' => true,
                    'products' => $products
                ];
            } catch (Exception $e) {
                $response['message'] = 'Error al cargar los productos inactivos';
            }
            break;

        case 'export':
            $productos = $producto->getAll(1, 999);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="productos.doc"');
            
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="UTF-8">
                <title>Reporte de Productos</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    th { background-color: #4CAF50; color: white; }
                </style>
            </head>
            <body>';
            
            echo '<h1>Reporte de Productos</h1>';
            echo '<table>';
            echo '<tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock Actual</th>
                </tr>';
            
            foreach ($productos as $item) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($item['ID_Producto']) . '</td>';
                echo '<td>' . htmlspecialchars($item['Nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($item['Descripcion']) . '</td>';
                echo '<td>$' . number_format($item['Precio'], 2) . '</td>';
                echo '<td>' . $item['stock_actual'] . '</td>';
                echo '</tr>';
            }
            
            echo '</table></body></html>';
            exit();
            break;
            
        case 'get':
            $productoData = $producto->getById($_GET['id']);
            header('Content-Type: application/json');
            echo json_encode($productoData);
            exit();
            break;

        default:
            $response['message'] = 'Acción no válida';
            break;
    }

    if (!in_array($_GET['action'], ['export'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    exit();
}