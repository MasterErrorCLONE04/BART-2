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

    <h3>Citas Pendientes y Confirmadas</h3>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Barbero</th>
            <th>Servicio</th>
            <th>Estado</th>
        </tr>
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
    </table>

    <h3>Acciones</h3>
    <a href="gestion_barberos.php">Gestionar Barberos</a><br>
    <a href="gestion_pagos.php">Gestionar Pagos y Comisiones</a>
</body>
</html>