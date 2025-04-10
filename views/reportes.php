<?php 
require_once '../includes/security.php';
checkSession();
checkAdminRole();
include_once '../includes/header.php';
require_once '../config/database.php';
require_once '../models/reporte.php';

$database = new Database();
$db = $database->getConnection();
$reporte = new Reporte($db);
?>

<div class="content-header">
    <h2>Reportes</h2>
</div>

<div class="report-filters">
    <form id="reportForm">
        <div class="form-group">
            <label>Fecha Inicio:</label>
            <input type="date" id="startDate" name="startDate" required>
        </div>
        <div class="form-group">
            <label>Fecha Fin:</label>
            <input type="date" id="endDate" name="endDate" required>
        </div>
        <button type="submit" class="btn-primary">Generar Reporte</button>
    </form>
</div>

<div id="reportResults" class="table-container">
    <!-- Los resultados del reporte se cargarán aquí -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/reportes.js"></script>
<?php include_once '../includes/footer.php'; ?>