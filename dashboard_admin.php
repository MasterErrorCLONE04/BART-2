<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Ingresos por día
$stmt = $pdo->query("SELECT DATE(fecha_creacion) as dia, SUM(precio_final) as total FROM citas WHERE estado = 'completada' GROUP BY dia");
$ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Clientes atendidos
$stmt = $pdo->query("SELECT COUNT(DISTINCT cliente_id) as total_clientes FROM citas WHERE estado = 'completada'");
$total_clientes = $stmt->fetch(PDO::FETCH_ASSOC)['total_clientes'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?> (Administrador)</h2>
    <a href="logout.php">Cerrar Sesión</a>
    <h3>Ingresos por Día</h3>
    <table>
        <tr><th>Día</th><th>Total</th></tr>
        <?php foreach ($ingresos as $ingreso): ?>
            <tr>
                <td><?php echo $ingreso['dia']; ?></td>
                <td>$<?php echo number_format($ingreso['total'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h3>Clientes Atendidos</h3>
    <p>Total: <?php echo $total_clientes; ?></p>
    <h3>Acciones</h3>
    <a href="gestion_barberos.php">Gestionar Barberos</a>
</body>
</html>