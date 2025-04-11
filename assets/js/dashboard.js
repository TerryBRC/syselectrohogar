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
    const tbody = table.querySelector('tbody');
    
    if (!products || !products.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center">No hay productos con stock bajo</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = products.map(product => `
        <tr class="${product.stock_actual <= 5 ? 'critical-stock' : 'low-stock'}">
            <td>${product.Nombre}</td>
            <td class="text-center">${product.stock_actual}</td>
        </tr>
    `).join('');
}

// Add some CSS styles
const style = document.createElement('style');
style.textContent = `
    .low-stock { background-color: #fff3cd; }
    .critical-stock { background-color: #f8d7da; }
    .low-stock-table { width: 100%; border-collapse: collapse; }
    .low-stock-table th, .low-stock-table td { padding: 8px; border: 1px solid #dee2e6; }
    .low-stock-table th { background-color: #f8f9fa; }
`;
document.head.appendChild(style);

// Initialize dashboard
document.addEventListener('DOMContentLoaded', fetchDashboardData);
