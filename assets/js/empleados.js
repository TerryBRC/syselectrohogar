// Modal elements
const modal = document.getElementById('empleadoModal');
const closeBtn = document.getElementsByClassName('close')[0];
const empleadoForm = document.getElementById('empleadoForm');

// Event listeners
closeBtn.onclick = hideModal;
window.onclick = (event) => {
    if (event.target == modal) hideModal();
};

empleadoForm.onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(empleadoForm);
    
    try {
        const response = await fetch('../controllers/empleado_controller.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            showNotification('Empleado guardado exitosamente', 'success');
            hideModal();
            loadEmpleados();
        } else {
            showNotification(result.message || 'Error al guardar el empleado', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    }
};

const span = document.getElementsByClassName('close')[0];

function showModal() {
    modal.style.display = 'block';
}

span.onclick = function() {
    modal.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Close on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        modal.style.display = 'none';
    }
});
empleadoForm.reset();


function hideModal() {
    modal.style.display = 'none';
}

async function loadEmpleados() {
    try {
        const response = await fetch('../controllers/empleado_controller.php?action=list');
        const empleados = await response.json();
        
        const tbody = document.getElementById('empleadosTableBody');
        tbody.innerHTML = '';
        
        empleados.forEach(empleado => {
            tbody.innerHTML += `
                <tr>
                    <td>${empleado.ID_Empleado}</td>
                    <td>${empleado.Nombre || ''}</td>
                    <td>${empleado.Apellido || ''}</td>
                    <td>${empleado.CorreoElectronico || ''}</td>
                    <td>${empleado.Telefono || ''}</td>
                    <td>${empleado.Direccion || ''}</td>
                    <td>${empleado.Rol || ''}</td>
                    <td class="actions">
                        <button onclick="editarEmpleado(${empleado.ID_Empleado})" class="btn-secondary">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button onclick="confirmarEliminar(${empleado.ID_Empleado})" class="btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar los empleados', 'error');
    }
}

async function editarEmpleado(id) {
    try {
        const response = await fetch(`../controllers/empleado_controller.php?action=get&id=${id}`);
        const empleado = await response.json();
        
        document.getElementById('nombre').value = empleado.Nombre;
        document.getElementById('apellido').value = empleado.Apellido;
        document.getElementById('dni').value = empleado.DNI;
        document.getElementById('email').value = empleado.CorreoElectronico;
        document.getElementById('telefono').value = empleado.Telefono;
        document.getElementById('direccion').value = empleado.Direccion;
        document.getElementById('rol').value = empleado.Rol;
        
        showModal();
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los datos del empleado');
    }
}

// Load empleados when page loads
document.addEventListener('DOMContentLoaded', loadEmpleados);