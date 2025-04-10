// Modal elements
const modal = document.getElementById('facturaModal');
const detalleModal = document.getElementById('detalleModal');
const closeBtns = document.getElementsByClassName('close');
const facturaForm = document.getElementById('facturaForm');

// Event listeners
Array.from(closeBtns).forEach(btn => {
    btn.onclick = function() {
        modal.style.display = 'none';
        detalleModal.style.display = 'none';
    }
});

window.onclick = (event) => {
    if (event.target == modal) modal.style.display = 'none';
    if (event.target == detalleModal) detalleModal.style.display = 'none';
};

facturaForm.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(facturaForm);
    
    // Replace alert() calls with showNotification()
    try {
        const response = await fetch('../controllers/factura_controller.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Factura guardada exitosamente', 'success');
            hideModal();
            loadFacturas();
        } else {
            showNotification(result.message || 'Error al guardar la factura', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
};

const span = document.getElementsByClassName('close')[0];

function showModal() {
    modal.style.display = 'block';
    // Clear previous form data
    document.getElementById('facturaForm').reset();
    document.getElementById('productosRows').innerHTML = '';
    addProductRow(); // Add first product row by default
}

span.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        modal.style.display = 'none';
    }
});

function addProductRow() {
    const row = document.createElement('div');
    row.className = 'product-row';
    row.innerHTML = `
        <div class="form-group">
            <select name="productos[]" required>
                <option value="">Seleccione un producto</option>
                <!-- Products will be loaded dynamically -->
            </select>
            <input type="number" name="cantidades[]" placeholder="Cantidad" min="1" required>
            <button type="button" class="btn-danger" onclick="this.parentElement.remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    document.getElementById('productosRows').appendChild(row);
    loadProductsIntoSelect(row.querySelector('select'));
}

function addProductRow() {
    const container = document.getElementById('productosRows');
    const rowDiv = document.createElement('div');
    rowDiv.className = 'producto-row';
    rowDiv.innerHTML = `
        <div class="form-group">
            <select name="productos[]" required onchange="updatePrice(this)">
                <option value="">Seleccione un producto</option>
            </select>
        </div>
        <div class="form-group">
            <input type="number" name="cantidades[]" min="1" required placeholder="Cantidad" onchange="updateTotal()">
        </div>
        <div class="form-group">
            <input type="number" name="precios[]" readonly>
        </div>
    `;
    container.appendChild(rowDiv);
    loadProductOptions(rowDiv.querySelector('select'));
}

async function loadProductOptions(select) {
    try {
        const response = await fetch('../controllers/producto_controller.php?action=list');
        const productos = await response.json();
        
        productos.forEach(producto => {
            const option = document.createElement('option');
            option.value = producto.ID_Producto;
            option.textContent = producto.Nombre;
            option.dataset.precio = producto.Precio;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadFacturas() {
    const spinner = document.getElementById('loading-spinner');
    const tableBody = document.getElementById('facturasTableBody');
    
    try {
        spinner.style.display = 'flex';
        tableBody.innerHTML = '';
        
        const response = await fetch('../controllers/factura_controller.php?action=list');
        const facturas = await response.json();
        
        if (facturas.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-file-invoice"></i>
                            <p>No hay facturas registradas</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        facturas.forEach(factura => {
            tableBody.innerHTML += `
                <tr>
                    <td>${factura.NumeroSAP}</td>
                    <td>${factura.Fecha}</td>
                    <td>${factura.Nombre_Completo}</td>
                    <td>$${parseFloat(factura.Total).toFixed(2)}</td>
                    <td>
                        <button onclick="viewDetails(${factura.ID_Factura})" class="btn-secondary">Ver</button>
                        <button onclick="printFactura(${factura.ID_Factura})" class="btn-primary">Imprimir</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar las facturas', 'error');
    } finally {
        spinner.style.display = 'none';
    }
}

async function viewDetails(facturaId) {
    try {
        const response = await fetch(`../controllers/factura_controller.php?action=get_details&id=${facturaId}`);
        const detalles = await response.json();
        
        let html = '<table><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>';
        let total = 0;
        
        detalles.forEach(detalle => {
            const subtotal = detalle.Cantidad * detalle.PrecioUnitario;
            total += subtotal;
            html += `
                <tr>
                    <td>${detalle.NombreProducto}</td>
                    <td>${detalle.Cantidad}</td>
                    <td>$${parseFloat(detalle.PrecioUnitario).toFixed(2)}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                </tr>
            `;
        });
        
        html += `</tbody><tfoot><tr><td colspan="3">Total</td><td>$${total.toFixed(2)}</td></tr></tfoot></table>`;
        
        document.getElementById('detalleContent').innerHTML = html;
        detalleModal.style.display = 'block';
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

// Load facturas when page loads
document.addEventListener('DOMContentLoaded', loadFacturas);

let productosSeleccionados = [];

function showModal() {
    document.getElementById('facturaModal').style.display = 'block';
}

function hideModal() {
    document.getElementById('facturaModal').style.display = 'none';
}

function agregarProducto() {
    const productoId = document.getElementById('producto_id').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    
    fetch('../controllers/producto_controller.php?action=get&id=' + productoId)
        .then(response => response.json())
        .then(producto => {
            if (producto) {
                productosSeleccionados.push({
                    id: producto.ID_Producto,
                    nombre: producto.Nombre,
                    cantidad: cantidad,
                    precio: producto.Precio,
                    subtotal: cantidad * producto.Precio
                });
                actualizarTablaProductos();
            }
        });
}

function actualizarTablaProductos() {
    const tabla = document.getElementById('productosTabla');
    let html = '';
    let total = 0;
    
    productosSeleccionados.forEach((item, index) => {
        html += `<tr>
            <td>${item.nombre}</td>
            <td>${item.cantidad}</td>
            <td>$${item.precio}</td>
            <td>$${item.subtotal}</td>
            <td><button onclick="eliminarProducto(${index})">Eliminar</button></td>
        </tr>`;
        total += item.subtotal;
    });
    
    tabla.innerHTML = html;
    document.getElementById('totalFactura').textContent = `Total: $${total}`;
}