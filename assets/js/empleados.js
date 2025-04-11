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
    modal.classList.add('active');
}

function hideModal() {
    modal.classList.remove('active');
    empleadoForm.reset();
    empleadoForm.querySelector('[name="action"]').value = 'create';
    empleadoForm.querySelector('[name="password"]').required = true;
}

// Update these event listeners
span.onclick = hideModal;
window.onclick = function(event) {
    if (event.target == modal) {
        hideModal();
    }
}

// Update ESC key handler
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideModal();
    }
});

// Remove this line as it's not needed and could cause issues
// empleadoForm.reset();

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
        
        // Update form values
        empleadoForm.querySelector('[name="nombre"]').value = empleado.Nombre;
        empleadoForm.querySelector('[name="apellido"]').value = empleado.Apellido;
        empleadoForm.querySelector('[name="email"]').value = empleado.CorreoElectronico;
        empleadoForm.querySelector('[name="telefono"]').value = empleado.Telefono;
        empleadoForm.querySelector('[name="direccion"]').value = empleado.Direccion;
        empleadoForm.querySelector('[name="rol"]').value = empleado.Rol;
        
        // Update hidden fields
        empleadoForm.querySelector('[name="action"]').value = 'update';
        empleadoForm.querySelector('[name="id"]').value = empleado.ID_Empleado;
        empleadoForm.querySelector('[name="id_usuario"]').value = empleado.ID_Usuario;
        
        // Make password optional for updates
        empleadoForm.querySelector('[name="password"]').required = false;
        
        modal.classList.add('active');
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar los datos del empleado', 'error');
    }
}

// Update the form reset in hideModal function
function hideModal() {
    modal.classList.remove('active');
    empleadoForm.reset();
    empleadoForm.querySelector('[name="action"]').value = 'create';
    empleadoForm.querySelector('[name="password"]').required = true;
}

document.addEventListener('DOMContentLoaded', loadEmpleados);