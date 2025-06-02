<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

// Citas del cliente
$stmt = $pdo->prepare("SELECT c.*, s.nombre as servicio_nombre, u.nombre as barbero_nombre FROM citas c JOIN servicios s ON c.servicio_id = s.id JOIN usuarios u ON c.barbero_id = u.id WHERE c.cliente_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Cliente</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?> (Cliente)</h2>
    <a href="logout.php">Cerrar Sesión</a>
    <a href="agendar_cita.php">Agendar Cita</a>
    <h3>Tus Citas</h3>
    <table>
        <tr><th>Fecha</th><th>Hora</th><th>Servicio</th><th>Barbero</th><th>Estado</th></tr>
        <?php foreach ($citas as $cita): ?>
            <tr>
                <td><?php echo $cita['fecha']; ?></td>
                <td><?php echo $cita['hora']; ?></td>
                <td><?php echo $cita['servicio_nombre']; ?></td>
                <td><?php echo $cita['barbero_nombre']; ?></td>
                <td><?php echo $cita['estado']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>