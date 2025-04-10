<?php 
require_once '../includes/security.php';
checkSession();
checkAdminRole(); // Both admin and superadmin can manage products
include_once '../includes/header.php';
require_once '../config/database.php';
require_once '../models/producto.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);
?>

<div class="content-header">
    <h2>Gestión de Productos</h2>
    <div class="action-buttons">
        <button class="btn-primary" onclick="showModal()">Nuevo Producto</button>
        <button class="btn-secondary" onclick="exportToWord()">
            <i class="fas fa-file-word"></i> Exportar a Word
        </button>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="productosTableBody">
            <!-- JavaScript will populate this -->
        </tbody>
    </table>
    <div class="pagination" id="pagination">
        <!-- JavaScript will populate this -->
    </div>
</div>

<h3 class="mt-4">Productos Inactivos</h3>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="inactiveProductsTableBody">
            <!-- JavaScript will populate this -->
        </tbody>
    </table>
</div>

<!-- Add this after your table structure -->
<div id="productoModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Nuevo Producto</h2>
        <form id="productoForm">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="id" value="">
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn-primary">Guardar</button>
                <button type="button" class="btn-secondary" onclick="hideModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/productos.js"></script>
<?php include_once '../includes/footer.php';
?>