<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Ingresos por día
$stmt = $pdo->query("SELECT DATE(fecha_creacion) as dia, SUM(precio_final) as total FROM citas WHERE estado = 'completada' GROUP BY dia");
$ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Ingresos por Día</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Día</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ingresos as $ingreso): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ingreso['dia']); ?></td>
                    <td>$<?php echo number_format($ingreso['total'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($ingresos)): ?>
                <tr><td colspan="2">No hay ingresos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>