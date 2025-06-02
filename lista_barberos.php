<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM usuarios WHERE rol = 'barbero'";
$params = [];

if ($search) {
    $query .= " AND (nombre LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$query .= " ORDER BY nombre";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Lista de Barberos</h3>
    <!-- Search Form -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Buscar por nombre o email" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        </div>
    </form>
    <!-- Create Barbero Button -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createBarberoModal">Agregar Nuevo Barbero</button>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($barberos as $barbero): ?>
                <tr>
                    <td><?php echo htmlspecialchars($barbero['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($barbero['email']); ?></td>
                    <td><?php echo htmlspecialchars($barbero['telefono'] ?: '-'); ?></td>
                    <td><?php echo $barbero['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editBarberoModal"
                                data-id="<?php echo $barbero['id']; ?>"
                                data-nombre="<?php echo htmlspecialchars($barbero['nombre']); ?>"
                                data-email="<?php echo htmlspecialchars($barbero['email']); ?>"
                                data-telefono="<?php echo htmlspecialchars($barbero['telefono']); ?>">
                            Editar
                        </button>
                        <form method="POST" action="toggle_barbero.php" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $barbero['id']; ?>">
                            <input type="hidden" name="activo" value="<?php echo $barbero['activo']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $barbero['activo'] ? 'btn-danger' : 'btn-success'; ?>">
                                <?php echo $barbero['activo'] ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($barberos)): ?>
                <tr><td colspan="5">No hay barberos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>