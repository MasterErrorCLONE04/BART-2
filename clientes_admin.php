<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Clientes atendidos
$stmt = $pdo->query("SELECT COUNT(DISTINCT cliente_id) as total_clientes FROM citas WHERE estado = 'completada'");
$total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total_clientes'];
?>

<div class="mt-4">
    <h3>Clientes Atendidos</h3>
    <p>Total: <?php echo htmlspecialchars($total_clientes); ?></p>
</div>