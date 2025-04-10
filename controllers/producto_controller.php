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

        case 'update':
            if ($producto->update($_POST)) {
                $response['success'] = true;
                $response['message'] = 'Producto actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar el producto';
            }
            break;

        case 'delete':
            if ($producto->delete($_POST['id'])) {
                $response['success'] = true;
                $response['message'] = 'Producto eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar el producto';
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($_GET['action']) {
        case 'export':
            $productos = $producto->getAll();
            
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="productos.html"');
            
            echo '<html>
            <head>
                <meta charset="UTF-8">
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    th { background-color: #4CAF50; color: white; }
                </style>
            </head>
            <body>';
            
            echo '<table>';
            echo '<tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>';
            
            foreach ($productos as $item) {
                echo '<tr>';
                echo '<td>' . $item['ID_Producto'] . '</td>';
                echo '<td>' . $item['Nombre'] . '</td>';
                echo '<td>' . $item['Descripcion'] . '</td>';
                echo '<td>' . $item['Precio'] . '</td>';
                echo '<td>' . $item['Stock'] . '</td>';
                echo '</tr>';
            }
            
            echo '</table></body></html>';
            exit();
            break;
            
        case 'list':
            $productos = $producto->getAll();
            header('Content-Type: application/json');
            echo json_encode($productos);
            exit();
        case 'get':
            $productoData = $producto->getById($_GET['id']);
            header('Content-Type: application/json');
            echo json_encode($productoData);
            exit();
            break;
    }
}