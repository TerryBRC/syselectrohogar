<?php 
require_once '../includes/security.php';
checkSession();
checkSuperAdminRole(); // Solo superadmin puede gestionar empleados/usuarios
include_once '../includes/header.php';
require_once '../config/database.php';
require_once '../models/empleado.php';

$database = new Database();
$db = $database->getConnection();
$empleado = new Empleado($db);
?>

<div class="content-header">
    <h2>Gestión de Empleados</h2>
    <button class="btn-primary" onclick="showModal()">Nuevo Empleado</button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="empleadosTableBody">
            <!-- JavaScript will populate this -->
        </tbody>
    </table>
</div>

    <div id="empleadoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nuevo Empleado</h2>
            <form id="empleadoForm">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" id="empleadoId">
                
                <input type="hidden" name="id_usuario" id="empleadoUserId">
                
                <div class="modal-form-grid">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Apellido:</label>
                        <input type="text" name="apellido" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="tel" name="telefono" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Rol:</label>
                        <select name="rol" required>
                            <option value="">Seleccione un rol</option>
                            <option value="Vendedor">Vendedor</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="email" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Contraseña:</label>
                        <input type="password" name="password" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Dirección:</label>
                        <input type="text" name="direccion" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/empleados.js"></script>
<?php include_once '../includes/footer.php'; ?>