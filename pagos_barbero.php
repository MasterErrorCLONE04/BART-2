<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

// Pagos realizados al barbero
$stmt = $pdo->prepare("
    SELECT * 
    FROM pagos_barberos 
    WHERE barbero_id = ? 
    ORDER BY fecha_pago DESC
");
$stmt->execute([$_SESSION['user_id']]);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Pagos Realizados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Monto</th>
                <th>Período</th>
                <th>Fecha de Pago</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                    <td><?php echo htmlspecialchars($pago['periodo_inicio']) . ' a ' . htmlspecialchars($pago['periodo_fin']); ?></td>
                    <td><?php echo htmlspecialchars($pago['fecha_pago']); ?></td>
                    <td><?php echo htmlspecialchars($pago['notas'] ?: '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($pagos)): ?>
                <tr><td colspan="4">No hay pagos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>