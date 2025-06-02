<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

// Servicios realizados por el barbero
$stmt = $pdo->prepare("
    SELECT c.*, s.nombre as servicio_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    WHERE c.barbero_id = ? AND c.estado = 'completada'
");
$stmt->execute([$_SESSION['user_id']]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="mt-4">
    <h3>Servicios Realizados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Servicio</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?php echo htmlspecialchars($servicio['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($servicio['servicio_nombre']); ?></td>
                    <td>$<?php echo number_format($servicio['precio_final'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($servicios)): ?>
                <tr><td colspan="3">No hay servicios realizados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>