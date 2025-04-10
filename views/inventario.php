<?php 
require_once '../includes/security.php';
checkSession();
checkAdminRole();
include_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Control de Inventario</h2>
    <button class="btn-primary" onclick="showModal()">Nuevo Movimiento</button>
</div>

<div class="table-container">
    <div id="loading-spinner" class="spinner-container" style="display: none;">
        <div class="spinner"></div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Usuario</th>
                <th>Factura SAP</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div id="inventarioModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Registrar Movimiento</h2>
        <form id="inventarioForm">
            <div class="form-group">
                <label for="producto">Producto</label>
                <select id="producto" name="producto_id" required>
                    <option value="">Seleccione un producto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo de Movimiento</label>
                <select id="tipo" name="tipo" required>
                    <option value="Entrada">Entrada</option>
                    <option value="Salida">Salida</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad</label>
                <input type="number" id="cantidad" name="cantidad" min="1" required>
            </div>
            <button type="submit" class="btn-primary">Guardar</button>
        </form>
    </div>
</div>

<script src="../assets/js/utils.js"></script>
<script src="../assets/js/inventario.js"></script>

<?php include_once '../includes/footer.php'; ?>