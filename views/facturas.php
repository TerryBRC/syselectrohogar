<?php 
require_once '../includes/security.php';
checkSession();
checkVendedorRole();
include_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Nueva Factura</h2>
</div>

<div class="invoice-container">
    <form id="facturaForm">
        <div class="invoice-header">
            <div class="form-row">
            <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="numeroFactura">Número de Factura</label>
                    <input type="text" id="numeroFactura" name="numeroFactura" required>
                </div>
                <div class="form-group">
                    <label for="tipoPago">Tipo de Pago</label>
                    <select id="tipoPago" name="tipoPago" required>
                        <option value="CREDITO">Crédito</option>
                        <option value="CONTADO">Contado</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="numeroSAP">Número SAP</label>
                    <input type="text" id="numeroSAP" name="numeroSAP" required>
                </div>
                <div class="form-group">
                    <label for="nombreCliente">Nombre Completo</label>
                    <input type="text" id="nombreCliente" name="nombreCliente" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
            </div>

        </div>

        <div class="products-section">
            <h3>Productos (Máximo 10 artículos)</h3>
            <div class="products-table">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productosTableBody">
                        <!-- Products will be added here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td></td>
                            <td id="totalFactura">$0.00</td>
                            <td>
                                <button type="button" id="addProductBtn" class="btn-secondary">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="invoice-footer">
            <button type="submit" class="btn-primary" id="saveInvoiceBtn">
                <i class="fas fa-save"></i> Guardar Factura
            </button>
        </div>
    </form>
</div>

<!-- Product Selection Modal -->
<div id="productoModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Seleccionar Producto</h3>
        <div class="form-group">
            <label for="producto">Producto</label>
            <select id="producto" required>
                <!-- Products will be loaded here -->
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" min="1" required>
        </div>
        <div class="form-group">
            <label for="descuento">Descuento ($)</label>
            <input type="number" id="descuento" min="0" step="0.01" value="0">
        </div>
        <div class="form-group">
            <label for="precio">Precio Unitario</label>
            <input type="number" id="precio" readonly>
        </div>
        <button type="button" class="btn-primary" onclick="agregarProductoAFactura()">
            Agregar a Factura
        </button>
    </div>
</div>

<script src="../assets/js/utils.js"></script>
<script src="../assets/js/facturas.js"></script>

<?php include_once '../includes/footer.php'; ?>