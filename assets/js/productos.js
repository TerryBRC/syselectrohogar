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

function showModal(id = null) {
    const modal = document.getElementById('productoModal');
    const form = document.getElementById('productoForm');
    const modalTitle = document.getElementById('modalTitle');

    if (id) {
        modalTitle.textContent = 'Editar Producto';
        form.action.value = 'update';
        form.id.value = id;
        
        // Fetch product data
        fetch(`../controllers/producto_controller.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                form.nombre.value = data.Nombre;
                form.descripcion.value = data.Descripcion;
                form.precio.value = data.Precio;
            });
    } else {
        modalTitle.textContent = 'Nuevo Producto';
        form.action.value = 'create';
        form.reset();
    }
    
    modal.style.display = 'block';
}

function deleteProduct(id) {
    if (confirm('¿Está seguro de eliminar este producto?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        fetch('../controllers/producto_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadProducts();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        });
    }
}

document.getElementById('productoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../controllers/producto_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('productoModal').style.display = 'none';
            loadProducts();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    });
});

function loadProducts() {
    fetch('../controllers/producto_controller.php?action=list')
        .then(response => response.json())
        .then(products => {
            const tbody = document.getElementById('productosTableBody');
            tbody.innerHTML = '';
            
            products.forEach(product => {
                tbody.innerHTML += `
                    <tr>
                        <td>${product.ID_Producto}</td>
                        <td>${product.Nombre}</td>
                        <td>${product.Descripcion}</td>
                        <td>${product.Precio}</td>
                        <td>${product.stock_actual || 0}</td>
                        <td>
                            <button onclick="showModal(${product.ID_Producto})" class="btn-secondary">
                                <i class="fas fa-edit"></i>Editar
                            </button>
                            <button onclick="deleteProduct(${product.ID_Producto})" class="btn-danger">
                                <i class="fas fa-trash"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            });
        });
}

// Load products when page loads
document.addEventListener('DOMContentLoaded', loadProducts);