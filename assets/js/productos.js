// Add this at the beginning of your productos.js file
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('productoModal').style.display = 'none';
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('productoModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
});

// Add ESC key support to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById('productoModal').style.display = 'none';
    }
});

// Update the modal functions
// First, remove any duplicate modal event listeners at the top of the file
// Keep only these modal-related functions:

function showModal(id = null) {
    const modal = document.getElementById('productoModal');
    const form = document.getElementById('productoForm');
    const modalTitle = document.getElementById('modalTitle');

    // Reset form
    form.reset();
    
    if (id) {
        modalTitle.textContent = 'Editar Producto';
        form.action.value = 'update';
        form.id.value = id;
        
        fetch(`../controllers/producto_controller.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                form.nombre.value = data.Nombre;
                form.descripcion.value = data.Descripcion || '';
                form.precio.value = data.Precio;
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al cargar datos del producto', 'error');
            });
    } else {
        modalTitle.textContent = 'Nuevo Producto';
        form.action.value = 'create';
        form.id.value = '';
    }
    
    modal.style.display = 'block';
}

function hideModal() {
    const modal = document.getElementById('productoModal');
    modal.style.display = 'none';
}

// Update the form submission handler
document.getElementById('productoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../controllers/producto_controller.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message || 'Producto guardado exitosamente', 'success');
            hideModal();
            loadProducts(currentPage);
        } else {
            showNotification(result.message || 'Error al guardar el producto', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
});

// Add these event listeners for modal closing
document.querySelector('.close').addEventListener('click', hideModal);

window.addEventListener('click', function(event) {
    const modal = document.getElementById('productoModal');
    if (event.target == modal) {
        hideModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideModal();
    }
});

// Update the delete confirm dialog
function showDeleteConfirmDialog(message, callback) {
    const dialogHtml = `
        <div class="confirm-dialog">
            <div class="confirm-content">
                <p>${message}</p>
                <div class="confirm-buttons">
                    <button class="btn-secondary" onclick="closeConfirmDialog()">Cancelar</button>
                    <button class="btn-danger" onclick="confirmAction()">Eliminar</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', dialogHtml);
    window.confirmCallback = callback;
}

// Add restore confirm dialog
function showRestoreConfirmDialog(message, callback) {
    const dialogHtml = `
        <div class="confirm-dialog">
            <div class="confirm-content">
                <p>${message}</p>
                <div class="confirm-buttons">
                    <button class="btn-secondary" onclick="closeConfirmDialog()">Cancelar</button>
                    <button class="btn-success" onclick="confirmAction()">Restaurar</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', dialogHtml);
    window.confirmCallback = callback;
}

// Update delete product function
// Remove these conflicting functions
// Delete the old showDeleteConfirmDialog, showRestoreConfirmDialog, and old restoreProduct functions

// Update the delete function
function deleteProduct(id) {
    const dialogHtml = `
        <div class="confirm-dialog" id="deleteDialog">
            <div class="confirm-content">
                <p>¿Está seguro de eliminar este producto?</p>
                <div class="confirm-buttons">
                    <button class="btn-secondary" onclick="removeDialog('deleteDialog')">Cancelar</button>
                    <button class="btn-danger" onclick="confirmDelete(${id})">Eliminar</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', dialogHtml);
}

function confirmDelete(id) {
    removeDialog('deleteDialog');
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../controllers/producto_controller.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Producto eliminado exitosamente', 'success');
            loadProducts(currentPage);
            loadInactiveProducts();
        } else {
            showNotification(data.message || 'Error al eliminar el producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    });
}


// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('productoModal');
    if (event.target == modal) {
        hideModal();
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideModal();
    }
});

function exportToWord() {
    window.location.href = '../controllers/producto_controller.php?action=export';
}

let currentPage = 1;
let totalPages = 1;

function loadProducts(page = 1) {
    currentPage = page;
    fetch(`../controllers/producto_controller.php?action=list&page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showNotification(data.message || 'Error al cargar productos', 'error');
                return;
            }

            const tbody = document.getElementById('productosTableBody');
            tbody.innerHTML = '';
            
            if (!data.products || data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">No hay productos disponibles</td>
                    </tr>`;
                return;
            }

            data.products.forEach(product => {
                tbody.innerHTML += `
                    <tr>
                        <td>${product.ID_Producto}</td>
                        <td>${product.Nombre}</td>
                        <td>${product.Descripcion || ''}</td>
                        <td>$${parseFloat(product.Precio).toFixed(2)}</td>
                        <td>${product.stock_actual || 0}</td>
                        <td>
                            <button onclick="showModal(${product.ID_Producto})" class="btn-secondary">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button onclick="deleteProduct(${product.ID_Producto})" class="btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>`;
            });

            // Update pagination
            updatePagination(data.total_pages);
            
            // Load inactive products
            loadInactiveProducts();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los productos', 'error');
        });
}

function loadInactiveProducts() {
    fetch('../controllers/producto_controller.php?action=list_inactive')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showNotification(data.message || 'Error al cargar productos inactivos', 'error');
                return;
            }

            const tbody = document.getElementById('inactiveProductsTableBody');
            tbody.innerHTML = '';

            if (!data.products || data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">No hay productos inactivos</td>
                    </tr>`;
                return;
            }

            data.products.forEach(product => {
                tbody.innerHTML += `
                    <tr>
                        <td>${product.ID_Producto}</td>
                        <td>${product.Nombre}</td>
                        <td>${product.Descripcion || ''}</td>
                        <td>$${parseFloat(product.Precio).toFixed(2)}</td>
                        <td>${product.stock_actual || 0}</td>
                        <td>
                            <button onclick="restoreProduct(${product.ID_Producto})" class="btn-success">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                        </td>
                    </tr>`;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar los productos inactivos', 'error');
        });
}

// Make sure these functions are present and not duplicated
function restoreProduct(id) {
    const dialogHtml = `
        <div class="confirm-dialog" id="restoreDialog">
            <div class="confirm-content">
                <p>¿Está seguro de restaurar este producto?</p>
                <div class="confirm-buttons">
                    <button class="btn-secondary" onclick="removeDialog('restoreDialog')">Cancelar</button>
                    <button class="btn-success" onclick="confirmRestore(${id})">Restaurar</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', dialogHtml);
}

function confirmRestore(id) {
    removeDialog('restoreDialog');
    const formData = new FormData();
    formData.append('action', 'restore');
    formData.append('id', id);

    fetch('../controllers/producto_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Producto restaurado exitosamente', 'success');
            loadProducts(currentPage);
            loadInactiveProducts();
        } else {
            showNotification(data.message || 'Error al restaurar el producto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    });
}

function removeDialog(dialogId) {
    const dialog = document.getElementById(dialogId);
    if (dialog) {
        dialog.remove();
    }
}

function updatePagination() {
    const pagination = document.getElementById('pagination');
    let html = '';
    
    if (currentPage > 1) {
        html += `<button onclick="loadProducts(${currentPage - 1})">&laquo; Anterior</button>`;
    }
    
    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="${i === currentPage ? 'active' : ''}" 
                 onclick="loadProducts(${i})">${i}</button>`;
    }
    
    if (currentPage < totalPages) {
        html += `<button onclick="loadProducts(${currentPage + 1})">Siguiente &raquo;</button>`;
    }
    
    pagination.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});