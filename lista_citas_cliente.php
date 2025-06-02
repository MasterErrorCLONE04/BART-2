<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

// Handle search and sort
$search_date = isset($_GET['search_date']) ? trim($_GET['search_date']) : '';
$search_estado = isset($_GET['search_estado']) ? trim($_GET['search_estado']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'fecha_desc';
$sort_options = ['fecha_asc' => 'c.fecha ASC, c.hora ASC', 'fecha_desc' => 'c.fecha DESC, c.hora DESC', 'estado' => 'c.estado ASC'];

$query = "
    SELECT c.*, s.nombre as servicio_nombre, u.nombre as barbero_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.barbero_id = u.id 
    WHERE c.cliente_id = ?
";
$params = [$_SESSION['user_id']];

if ($search_date) {
    $query .= " AND c.fecha = ?";
    $params[] = $search_date;
}
if ($search_estado) {
    $query .= " AND c.estado = ?";
    $params[] = $search_estado;
}

$query .= " ORDER BY " . ($sort_options[$sort] ?? $sort_options['fecha_desc']);

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Tus Citas</h3>
    <!-- Search and Sort Form -->
    <form method="GET" class="mb-3">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="date" class="form-control" name="search_date" value="<?php echo htmlspecialchars($search_date); ?>">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="search_estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" <?php echo $search_estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="confirmada" <?php echo $search_estado === 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                    <option value="completada" <?php echo $search_estado === 'completada' ? 'selected' : ''; ?>>Completada</option>
                    <option value="cancelada" <?php echo $search_estado === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="sort">
                    <option value="fecha_desc" <?php echo $sort === 'fecha_desc' ? 'selected' : ''; ?>>Fecha (Más reciente)</option>
                    <option value="fecha_asc" <?php echo $sort === 'fecha_asc' ? 'selected' : ''; ?>>Fecha (Más antigua)</option>
                    <option value="estado" <?php echo $sort === 'estado' ? 'selected' : ''; ?>>Estado</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
        <a href="dashboard_cliente.php" class="btn btn-secondary mt-2">Limpiar</a>
    </form>
    <!-- Agendar Cita Button -->
    <a href="agendar_cita.php" class="btn btn-success mb-3">Agendar Nueva Cita</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <th>Barbero</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                    <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['barbero_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                    <td>
                        <?php if (in_array($cita['estado'], ['pendiente', 'confirmada'])): ?>
                            <form method="POST" action="cancelar_cita.php" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="cancelar">
                                <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                            </form>
                            <a href="agendar_cita.php?cita_id=<?php echo $cita['id']; ?>" class="btn btn-warning btn-sm">Re-agendar</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($citas)): ?>
                <tr><td colspan="6">No tienes citas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>