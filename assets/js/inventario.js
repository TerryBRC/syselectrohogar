// Main initialization when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal elements
    const modal = document.getElementById('inventarioModal');
    const closeBtn = document.querySelector('.close');
    const inventarioForm = document.getElementById('inventarioForm');
    
    // Set up modal close events
    if (closeBtn) {
        closeBtn.addEventListener('click', hideModal);
    }
    
    // Close modal when clicking outside
    if (modal) {
        window.addEventListener('click', (event) => {
            if (event.target == modal) hideModal();
        });
    }

    // Initialize form if it exists
    if (inventarioForm) {
        inventarioForm.addEventListener('submit', handleFormSubmit);
    }

    // Load initial data
    loadProductos();
    loadInventario();
});

// Handle form submission
// Add this function to check stock
async function checkStock(productoId) {
    try {
        const response = await fetch(`../controllers/inventario_controller.php?action=get_stock&producto_id=${productoId}`);
        const data = await response.json();
        return data.stock;
    } catch (error) {
        console.error('Error checking stock:', error);
        return 0;
    }
}

// Update the form submission handler
async function handleFormSubmit(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'register_movement');
    
    const tipo = formData.get('tipo');
    const cantidad = parseInt(formData.get('cantidad'));
    const productoId = formData.get('producto_id');

    // Check stock for non-entry movements
    if (tipo !== 'Entrada') {
        const currentStock = await checkStock(productoId);
        if (currentStock < cantidad) {
            showNotification(`Stock insuficiente. Stock actual: ${currentStock}`, 'error');
            return;
        }
        formData.set('cantidad', -Math.abs(cantidad));
    }
    
    try {
        const response = await fetch('../controllers/inventario_controller.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Movimiento registrado exitosamente', 'success');
            hideModal();
            loadInventario(currentPage);
        } else {
            showNotification(result.message || 'Error al registrar el movimiento', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
}

// Show modal and load products
// Update modal functions
function showModal() {
    const modal = document.getElementById('inventarioModal');
    if (modal) {
        modal.classList.add('active');
        loadProductos(); // Reload products when modal opens
    }
}

function hideModal() {
    const modal = document.getElementById('inventarioModal');
    if (modal) {
        modal.classList.remove('active');
        const form = document.getElementById('inventarioForm');
        if (form) form.reset();
    }
}

// Load products into select dropdown
async function loadProductos() {
    try {
        console.log('Loading products...');
        
        const response = await fetch('../controllers/producto_controller.php?action=list');
        const data = await response.json();
        
        console.log('Products response:', data);
        
        // Changed from producto_id to producto to match the HTML element ID
        const select = document.getElementById('producto');
        if (!select) {
            console.error('Product select element not found');
            return;
        }
        
        select.innerHTML = '<option value="">Seleccione un producto</option>';
        
        if (data.products && Array.isArray(data.products)) {
            data.products.forEach(producto => {
                select.innerHTML += `
                    <option value="${producto.ID_Producto}">${producto.Nombre} - C$${producto.Precio}</option>
                `;
            });
            console.log('Products loaded successfully');
        } else {
            console.warn('No products data available');
            showNotification('No hay productos disponibles', 'warning');
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showNotification('Error al cargar los productos', 'error');
    }
}

// Load inventory movements
let currentPage = 1;

async function loadInventario(page = 1) {
    try {
        const response = await fetch(`../controllers/inventario_controller.php?action=list&page=${page}`);
        const data = await response.json();
        
        const tbody = document.getElementById('inventarioTableBody');
        if (!tbody) {
            console.error('Inventory table body not found');
            return;
        }
        
        tbody.innerHTML = '';
        
        if (data.movements && Array.isArray(data.movements)) {
            data.movements.forEach(movimiento => {
                tbody.innerHTML += `
                    <tr>
                        <td>${movimiento.Fecha}</td>
                        <td>${movimiento.NombreProducto}</td>
                        <td>${movimiento.TipoMovimiento}</td>
                        <td>${movimiento.Cantidad}</td>
                        <td>${movimiento.Usuario}</td>
                        <td>${movimiento.FacturaSAP}</td>
                    </tr>
                `;
            });
            
            // Update pagination
            updatePagination(data.current_page, data.total_pages);
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay movimientos registrados</td></tr>';
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar el inventario', 'error');
    }
}

function updatePagination(currentPage, totalPages) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    let html = '<div class="pagination-container">';
    
    // Previous button
    html += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
        &laquo; Anterior
    </button>`;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        html += `<button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">
            ${i}
        </button>`;
    }

    // Next button
    html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
        Siguiente &raquo;
    </button>`;

    html += '</div>';
    pagination.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadInventario(page);
}