// Modal elements
const modal = document.getElementById('inventarioModal');
const closeBtn = document.getElementsByClassName('close')[0];
const inventarioForm = document.getElementById('inventarioForm');
const productoSelect = document.getElementById('producto_id');

// Event listeners
closeBtn.onclick = hideModal;
window.onclick = (event) => {
    if (event.target == modal) hideModal();
};

inventarioForm.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(inventarioForm);
    
    // Replace alert() calls with showNotification()
    try {
        const response = await fetch('../controllers/inventario_controller.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Movimiento registrado exitosamente', 'success');
            hideModal();
            loadInventario();
        } else {
            showNotification(result.message || 'Error al registrar el movimiento', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
};

function showModal() {
    modal.style.display = 'block';
    inventarioForm.reset();
    loadProductos();
}

function hideModal() {
    modal.style.display = 'none';
}

async function loadProductos() {
    try {
        const response = await fetch('../controllers/producto_controller.php?action=list');
        const productos = await response.json();
        
        productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
        productos.forEach(producto => {
            productoSelect.innerHTML += `
                <option value="${producto.ID_Producto}">${producto.Nombre}</option>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

async function updateStock() {
    const productoId = productoSelect.value;
    if (!productoId) {
        document.getElementById('stockActual').textContent = '0';
        return;
    }

    try {
        const response = await fetch(`../controllers/inventario_controller.php?action=get_stock&producto_id=${productoId}`);
        const data = await response.json();
        document.getElementById('stockActual').textContent = data.stock;
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadInventario() {
    const spinner = document.getElementById('loading-spinner');
    const tableBody = document.getElementById('inventarioTableBody');
    
    try {
        spinner.style.display = 'flex';
        tableBody.innerHTML = '';
        
        const response = await fetch('../controllers/inventario_controller.php?action=list');
        const movimientos = await response.json();
        
        if (movimientos.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>No hay movimientos registrados</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        movimientos.forEach(movimiento => {
            tableBody.innerHTML += `
                <tr>
                    <td>${movimiento.NombreProducto}</td>
                    <td>
                        <span class="badge ${movimiento.TipoMovimiento === 'Entrada' ? 'badge-success' : 'badge-warning'}">
                            ${movimiento.TipoMovimiento}
                        </span>
                    </td>
                    <td>${movimiento.Cantidad}</td>
                    <td>${movimiento.Fecha}</td>
                    <td>${movimiento.Usuario}</td>
                    <td>${movimiento.StockActual}</td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar el inventario', 'error');
    } finally {
        spinner.style.display = 'none';
    }
}

// Load products for select
async function loadProductos() {
    try {
        const response = await fetch('../controllers/producto_controller.php?action=list');
        const productos = await response.json();
        
        const select = document.getElementById('producto');
        select.innerHTML = '<option value="">Seleccione un producto</option>';
        
        productos.forEach(producto => {
            select.innerHTML += `
                <option value="${producto.ID_Producto}">${producto.Nombre}</option>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar los productos', 'error');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadInventario();
    loadProductos();
});