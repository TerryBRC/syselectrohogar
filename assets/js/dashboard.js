document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();
});

async function fetchDashboardData() {
    try {
        const response = await fetch('../controllers/dashboard_controller.php?action=get_stats');
        
        // Debug response
        const text = await response.text();
        console.log('Raw response:', text);
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            showNotification('Error al procesar la respuesta del servidor', 'error');
            return;
        }

        if (!data.success) {
            showNotification(data.message || 'Error al cargar datos', 'error');
            return;
        }

        // Update UI
        document.getElementById('totalProductos').textContent = data.totalProductos || '0';
        document.getElementById('facturasDelMes').textContent = data.facturasDelMes || '0';
        
        const lowStockTable = document.getElementById('lowStockTable');
        if (lowStockTable) {
            updateLowStockTable(lowStockTable, data.productosStockBajo || []);
        }

    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Error al conectar con el servidor', 'error');
    }
}

function updateLowStockTable(table, products) {
    if (!products.length) {
        table.innerHTML = `
            <tr>
                <td colspan="2" class="text-center">No hay productos con stock bajo</td>
            </tr>
        `;
        return;
    }

    table.innerHTML = products.map(product => `
        <tr>
            <td>${product.Nombre}</td>
            <td class="text-center">${product.stock_actual}</td>
        </tr>
    `).join('');
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', fetchDashboardData);
