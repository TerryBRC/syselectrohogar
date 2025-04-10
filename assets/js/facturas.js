// Modal elements
const modal = document.getElementById('facturaModal');
const detalleModal = document.getElementById('detalleModal');
const closeBtns = document.getElementsByClassName('close');
const facturaForm = document.getElementById('facturaForm');
const productoModal = document.getElementById('productoModal');
const addProductBtn = document.getElementById('addProductBtn');
const productosTableBody = document.getElementById('productosTableBody');
const totalFacturaElement = document.getElementById('totalFactura');
const productosSectionTitle = document.querySelector('.products-section h3');

let productosSeleccionados = [];
const MAX_PRODUCTOS = 10;

// Function to hide a modal
function hideModalElement(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'none';
    }
}

// Function to show a modal
function showModalElement(modalElement) {
    if (modalElement) {
        modalElement.style.display = 'block';
    }
}

// Event listeners for closing modals
Array.from(closeBtns).forEach(btn => {
    btn.onclick = function() {
        hideModalElement(modal);
        hideModalElement(detalleModal);
        hideModalElement(productoModal);
    }
});

window.onclick = (event) => {
    if (event.target == modal) hideModalElement(modal);
    if (event.target == detalleModal) hideModalElement(detalleModal);
    if (event.target == productoModal) hideModalElement(productoModal);
};

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideModalElement(modal);
        hideModalElement(detalleModal);
        hideModalElement(productoModal);
    }
});

// Event listener for form submission
facturaForm.onsubmit = guardarFactura;

function showFacturaModal() {
    showModalElement(modal);
    document.getElementById('facturaForm').reset();
    document.getElementById('productosRows').innerHTML = '';
    // The previous logic to add a default product row is removed
    // as the new UI uses a separate "Add Product" button.
}

async function loadProductOptions() {
    try {
        const response = await fetch('../controllers/producto_controller.php?action=list');
        const result = await response.json();
        
        const select = document.getElementById('producto');
        select.innerHTML = '<option value="">Seleccione un producto</option>';
        
        if (!result.success || !result.products) {
            throw new Error(result.message || 'Error al cargar productos');
        }
        
        result.products.forEach(product => {
            const precio = parseFloat(product.Precio).toFixed(2);
            select.innerHTML += `
                <option value="${product.ID_Producto}" 
                        data-precio="${product.Precio}"
                        data-stock="${product.Stock}">
                    ${product.Nombre} - $${precio}
                </option>
            `;
        });
        
        select.addEventListener('change', updatePrecio);
    } catch (error) {
        console.error('Error loading products:', error);
        showNotification('Error al cargar productos: ' + (error.message || 'Error desconocido'), 'error');
    }
}

function agregarProductoAFactura() {
    const productoSelect = document.getElementById('producto');
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const precio = parseFloat(document.getElementById('precio').value);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    
    if (!productoSelect.value || !cantidad || !precio) {
        showNotification('Por favor complete todos los campos', 'error');
        return;
    }

    // Calculate total current quantity of all products
    const currentTotalQuantity = productosSeleccionados.reduce((sum, producto) => sum + producto.cantidad, 0);
    
    // Check if new quantity would exceed maximum
    if (currentTotalQuantity + cantidad > MAX_PRODUCTOS) {
        const remainingQuantity = MAX_PRODUCTOS - currentTotalQuantity;
        showNotification(`Solo puede agregar ${remainingQuantity} productos más`, 'warning');
        return;
    }

    // Validación de stock
    const stockDisponible = parseInt(productoSelect.options[productoSelect.selectedIndex].dataset.stock);
    const existingProductIndex = productosSeleccionados.findIndex(p => p.id === productoSelect.value);
    const totalCantidad = existingProductIndex !== -1 ? 
        productosSeleccionados[existingProductIndex].cantidad + cantidad : 
        cantidad;

    if (stockDisponible === 0) {
        showNotification('Producto sin stock disponible', 'error');
        return;
    }

    if (totalCantidad > stockDisponible) {
        showNotification(`Stock insuficiente. Stock disponible: ${stockDisponible}`, 'error');
        return;
    }
    
    if (existingProductIndex !== -1) {
        // Update existing product
        productosSeleccionados[existingProductIndex].cantidad = totalCantidad;
        productosSeleccionados[existingProductIndex].descuento += descuento;
        productosSeleccionados[existingProductIndex].subtotal = 
            (totalCantidad * precio) - productosSeleccionados[existingProductIndex].descuento;
    } else {
        // Add new product
        productosSeleccionados.push({
            id: productoSelect.value,
            nombre: productoSelect.options[productoSelect.selectedIndex].text.split(' (Stock')[0],
            cantidad: cantidad,
            precio: precio,
            descuento: descuento,
            subtotal: (cantidad * precio) - descuento
        });
    }
    
    actualizarTablaProductos();
    hideProductModal();
}

function actualizarTablaProductos() {
    let html = '';
    let total = 0;
    let totalCantidad = 0;

    productosSeleccionados.forEach((producto, index) => {
        html += `
            <tr>
                <td>${producto.nombre}</td>
                <td>${producto.cantidad}</td>
                <td>$${producto.precio.toFixed(2)}</td>
                <td>$${producto.descuento.toFixed(2)}</td>
                <td>$${producto.subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn-danger" onclick="eliminarProducto(${index})">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            </tr>
        `;
        total += producto.subtotal;
        totalCantidad += producto.cantidad;
    });

    productosTableBody.innerHTML = html;
    totalFacturaElement.textContent = `$${total.toFixed(2)}`;

    addProductBtn.disabled = totalCantidad >= MAX_PRODUCTOS;
    productosSectionTitle.textContent = `Productos (${totalCantidad}/10 artículos)`;
}

function eliminarProducto(index) {
    productosSeleccionados.splice(index, 1);
    actualizarTablaProductos();
}

async function guardarFactura(e) {
    e.preventDefault();

    try {
        if (productosSeleccionados.length === 0) {
            showNotification('Agregue al menos un producto', 'warning');
            return;
        }

        const numeroFactura = document.getElementById('numeroFactura').value.trim();
        const tipoPago = document.getElementById('tipoPago').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const numeroSAP = document.getElementById('numeroSAP').value.trim();
        const nombreCliente = document.getElementById('nombreCliente').value.trim();

        if (!numeroFactura || !tipoPago || !telefono || !numeroSAP || !nombreCliente) {
            showNotification('Por favor complete todos los campos requeridos de la factura', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'create');
        formData.append('numeroFactura', numeroFactura);
        formData.append('tipoPago', tipoPago);
        formData.append('telefono', telefono);
        formData.append('numeroSAP', numeroSAP);
        formData.append('nombreCliente', nombreCliente);
        formData.append('productos', JSON.stringify(productosSeleccionados));

        const response = await fetch('../controllers/factura_controller.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        console.log('Server response:', result); // Debug line

        if (result.success) {
            showNotification('Factura guardada exitosamente', 'success');
            document.getElementById('facturaForm').reset();
            productosSeleccionados = [];
            actualizarTablaProductos();
            setTimeout(() => {
                window.location.href = 'facturas.php';
            }, 1500);
        } else {
            throw new Error(result.message || 'Error desconocido al guardar la factura');
        }
    } catch (error) {
        console.error('Error detallado:', error);
        showNotification('Error al guardar la factura: ' + error.message, 'error');
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
        showModalElement(detalleModal);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

function hideProductModal() {
    productoModal.style.display = 'none';
    document.getElementById('producto').value = '';
    document.getElementById('cantidad').value = '';
    document.getElementById('descuento').value = '0';
    document.getElementById('precio').value = '';
}

document.addEventListener('DOMContentLoaded', function() {
    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            if (productosSeleccionados.length >= MAX_PRODUCTOS) {
                showNotification('Máximo 10 artículos por factura', 'warning');
                return;
            }
            showModalElement(productoModal);
        });
    }

    const closeProductModalBtn = productoModal ? productoModal.querySelector('.close') : null;
    if (closeProductModalBtn) {
        closeProductModalBtn.addEventListener('click', hideProductModal);
    }

    loadProductOptions();
});


function updatePrecio() {
    const select = document.getElementById('producto');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const precio = parseFloat(selectedOption.dataset.precio);
        document.getElementById('precio').value = precio.toFixed(2);
    } else {
        document.getElementById('precio').value = '';
    }
}