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
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" required></textarea>
                </div>
                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select id="rol" name="rol" required>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Guardar</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/empleados.js"></script>
<?php include_once '../includes/footer.php'; ?>