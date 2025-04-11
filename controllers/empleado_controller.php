<?php
require_once '../config/database.php';
require_once '../models/empleado.php';

$database = new Database();
$db = $database->getConnection();
$empleado = new Empleado($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'create':
            if ($empleado->create($_POST)) {
                $response['success'] = true;
                $response['message'] = 'Empleado creado exitosamente';
            } else {
                $response['message'] = 'Error al crear el empleado';
            }
            break;

        case 'update':  // Move update case here
            if ($empleado->update($_POST)) {
                $response['success'] = true;
                $response['message'] = 'Empleado actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar el empleado';
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
            $empleados = $empleado->getAll();
            header('Content-Type: application/json');
            echo json_encode($empleados);
            exit();
        
        case 'get':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $empleadoData = $empleado->getById($id);
                echo json_encode($empleadoData);
            }
            exit();
    }
}