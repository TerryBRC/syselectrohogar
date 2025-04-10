const reportForm = document.getElementById('reportForm');
const reportContent = document.getElementById('reportContent');
const reportType = document.getElementById('reportType');
const dateRange = document.querySelector('.date-range');

// Show/hide date range based on report type
reportType.addEventListener('change', function() {
    dateRange.style.display = this.value === 'inventario' ? 'none' : 'block';
});

// Generate report
reportForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../controllers/reporte_controller.php?' + new URLSearchParams(formData));
        const data = await response.json();
        displayReport(data, formData.get('action'));
    } catch (error) {
        console.error('Error:', error);
        alert('Error al generar el reporte');
    }
});

// Export report to Excel
function exportReport() {
    const formData = new FormData(reportForm);
    const queryString = new URLSearchParams(formData).toString();
    window.location.href = `../controllers/reporte_controller.php?${queryString}&export=1`;
}

// Display report data
function displayReport(data, reportType) {
    let html = '<table><thead><tr>';
    
    // Define columns based on report type
    const columns = getReportColumns(reportType);
    
    // Add headers
    columns.forEach(column => {
        html += `<th>${column.label}</th>`;
    });
    html += '</tr></thead><tbody>';
    
    // Add data rows
    data.forEach(row => {
        html += '<tr>';
        columns.forEach(column => {
            let value = row[column.field];
            if (column.format === 'currency') {
                value = formatCurrency(value);
            } else if (column.format === 'date') {
                value = formatDate(value);
            }
            html += `<td>${value}</td>`;
        });
        html += '</tr>';
    });
    
    html += '</tbody></table>';
    
    // Add summary if applicable
    if (reportType === 'ventas' || reportType === 'productos_vendidos') {
        const total = data.reduce((sum, row) => sum + parseFloat(row.Total || row.TotalIngresos), 0);
        html += `<div class="report-summary">Total: ${formatCurrency(total)}</div>`;
    }
    
    reportContent.innerHTML = html;
}

// Define columns for each report type
function getReportColumns(reportType) {
    switch(reportType) {
        case 'ventas':
            return [
                { field: 'Fecha', label: 'Fecha', format: 'date' },
                { field: 'NumeroSAP', label: 'Número SAP' },
                { field: 'Cliente', label: 'Cliente' },
                { field: 'Producto', label: 'Producto' },
                { field: 'Cantidad', label: 'Cantidad' },
                { field: 'PrecioUnitario', label: 'Precio Unitario', format: 'currency' },
                { field: 'Total', label: 'Total', format: 'currency' }
            ];
            
        case 'inventario':
            return [
                { field: 'Nombre', label: 'Producto' },
                { field: 'Descripcion', label: 'Descripción' },
                { field: 'StockActual', label: 'Stock Actual' },
                { field: 'Precio', label: 'Precio', format: 'currency' }
            ];
            
        case 'productos_vendidos':
            return [
                { field: 'Nombre', label: 'Producto' },
                { field: 'TotalVendido', label: 'Cantidad Vendida' },
                { field: 'TotalIngresos', label: 'Total Ingresos', format: 'currency' }
            ];
            
        case 'movimientos':
            return [
                { field: 'Fecha', label: 'Fecha', format: 'date' },
                { field: 'Producto', label: 'Producto' },
                { field: 'TipoMovimiento', label: 'Tipo' },
                { field: 'Cantidad', label: 'Cantidad' },
                { field: 'Usuario', label: 'Usuario' },
                { field: 'NumeroFactura', label: 'N° Factura' }
            ];
    }
}

// Utility functions
function formatCurrency(value) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP'
    }).format(value);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('es-CO');
}

let ventasChart = null;
let productosChart = null;

async function generarReporteVentas() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    
    try {
        const response = await fetch(`../controllers/reporte_controller.php?action=ventas_periodo&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
        const data = await response.json();
        
        const ctx = document.getElementById('ventasChart').getContext('2d');
        
        if (ventasChart) {
            ventasChart.destroy();
        }
        
        ventasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.fecha),
                datasets: [{
                    label: 'Ventas por Día',
                    data: data.map(item => item.total),
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error:', error);
        alert('Error al generar el reporte de ventas');
    }
}

async function cargarProductosMasVendidos() {
    try {
        const response = await fetch('../controllers/reporte_controller.php?action=productos_mas_vendidos');
        const data = await response.json();
        
        const ctx = document.getElementById('productosChart').getContext('2d');
        
        if (productosChart) {
            productosChart.destroy();
        }
        
        productosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.nombre),
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: data.map(item => item.cantidad),
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los productos más vendidos');
    }
}

// Set default dates
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    document.getElementById('fecha_inicio').value = firstDay.toISOString().split('T')[0];
    document.getElementById('fecha_fin').value = today.toISOString().split('T')[0];
    
    generarReporteVentas();
    cargarProductosMasVendidos();
});

async function generarReporte() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    
    if (!fechaInicio || !fechaFin) {
        showNotification('Por favor seleccione ambas fechas', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`../controllers/reporte_controller.php?action=generar&inicio=${fechaInicio}&fin=${fechaFin}`);
        const result = await response.json();
        
        if (result.success) {
            showNotification('Reporte generado exitosamente', 'success');
            actualizarGraficos(result.data);
        } else {
            showNotification(result.message || 'Error al generar el reporte', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
}

async function exportarReporte(tipo) {
    try {
        const response = await fetch(`../controllers/reporte_controller.php?action=exportar&tipo=${tipo}`);
        const result = await response.json();
        
        if (result.success) {
            showNotification(`Reporte exportado como ${tipo.toUpperCase()}`, 'success');
            window.location.href = result.file;
        } else {
            showNotification(result.message || 'Error al exportar el reporte', 'error');
        }
    } catch (error) {
        showNotification('Error al procesar la solicitud', 'error');
    }
}

document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`../controllers/reporte_controller.php?action=sales&startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('reportResults');
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>N° SAP</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            data.forEach(row => {
                html += `
                    <tr>
                        <td>${row.Fecha}</td>
                        <td>${row.NumeroSAP}</td>
                        <td>${row.Cliente}</td>
                        <td>${row.Producto}</td>
                        <td>${row.Cantidad}</td>
                        <td>$${row.PrecioUnitario}</td>
                        <td>$${row.Total}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al generar el reporte', 'error');
        });
});