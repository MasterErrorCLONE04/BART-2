<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

// Citas pendientes y confirmadas del barbero
$stmt = $pdo->prepare("
    SELECT c.*, s.nombre as servicio_nombre, u.nombre as cliente_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.cliente_id = u.id 
    WHERE c.barbero_id = ? AND c.estado IN ('pendiente', 'confirmada') 
    ORDER BY c.fecha, c.hora
");
$stmt->execute([$_SESSION['user_id']]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Citas Pendientes y Confirmadas</h3>
    <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                    <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                    <td>
                        <form method="POST" action="confirmar_cita.php" style="display:inline;">
                            <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Confirmar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($citas)): ?>
                <tr><td colspan="6">No hay citas pendientes o confirmadas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>