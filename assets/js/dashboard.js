document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();
});

async function fetchDashboardData() {
        const response = await fetch('../controllers/dashboard_controller.php?action=get_stats');
        const data = await response.json();
        
        document.getElementById('totalProductos').textContent = data.totalProductos;
        document.getElementById('facturasDelMes').textContent = data.facturasDelMes;
        
        const lowStockTable = document.getElementById('lowStockTable');
        lowStockTable.innerHTML = '';
        
        if (data.productosStockBajo.length === 0) {
            lowStockTable.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center">No hay productos con stock bajo</td>
                </tr>
            `;
        } else {
            data.productosStockBajo.forEach(producto => {
                lowStockTable.innerHTML += `
                    <tr>
                        <td>${producto.nombre}</td>
                        <td class="text-center">${producto.stock}</td>
                    </tr>
                `;
            });
        }
}
