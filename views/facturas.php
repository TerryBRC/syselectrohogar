<?php 
require_once '../includes/security.php';
checkSession();
checkVendedorRole();
include_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gestión de Facturas</h2>
    <button class="btn-primary" onclick="showModal()">Nueva Factura</button>
</div>

<div class="table-container">
    <div id="loading-spinner" class="spinner-container" style="display: none;">
        <div class="spinner"></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Número SAP</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="facturasTableBody">
        </tbody>
    </table>
</div>

<!-- Modal structure -->
<div id="facturaModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Nueva Factura</h2>
        <form id="facturaForm">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="numeroSAP">Número SAP</label>
                <input type="text" id="numeroSAP" name="numeroSAP" required>
            </div>
            <div class="form-group">
                <label for="nombreCliente">Nombre del Cliente</label>
                <input type="text" id="nombreCliente" name="nombreCliente" required>
            </div>
            <div id="productosContainer">
                <h3>Productos</h3>
                <button type="button" onclick="addProductRow()" class="btn-secondary">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
                <div id="productosRows">
                    <!-- Product rows will be added here -->
                </div>
            </div>
            <button type="submit" class="btn-primary">Guardar Factura</button>
        </form>
    </div>
</div>

<script src="../assets/js/utils.js"></script>
<script src="../assets/js/facturas.js"></script>

<?php include_once '../includes/footer.php'; ?>