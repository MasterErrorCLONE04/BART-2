<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Citas pendientes y confirmadas
$stmt = $pdo->query("
    SELECT c.*, s.nombre as servicio_nombre, u.nombre as cliente_nombre, b.nombre as barbero_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.cliente_id = u.id 
    JOIN usuarios b ON c.barbero_id = b.id 
    WHERE c.estado IN ('pendiente', 'confirmada') 
    ORDER BY c.fecha, c.hora
");
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Citas Pendientes y Confirmadas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Barbero</th>
                <th>Servicio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                    <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['barbero_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($citas)): ?>
                <tr><td colspan="6">No hay citas pendientes o confirmadas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>