<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

// Confirmar cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirmar') {
    $cita_id = $_POST['cita_id'];

    try {
        // Obtener el precio del servicio
        $stmt = $pdo->prepare("
            SELECT s.precio 
            FROM citas c 
            JOIN servicios s ON c.servicio_id = s.id 
            WHERE c.id = ? AND c.barbero_id = ?
        ");
        $stmt->execute([$cita_id, $_SESSION['user_id']]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servicio) {
            // Actualizar estado y precio_final
            $stmt = $pdo->prepare("
                UPDATE citas 
                SET estado = 'completada', precio_final = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id = ? AND barbero_id = ?
            ");
            $stmt->execute([$servicio['precio'], $cita_id, $_SESSION['user_id']]);
            $message = "Cita confirmada exitosamente.";
        } else {
            $message = "Error: Cita no encontrada o no autorizada.";
        }
    } catch (PDOException $e) {
        $message = "Error al confirmar cita: " . $e->getMessage();
    }
}

// Servicios realizados por el barbero
$stmt = $pdo->prepare("
    SELECT c.*, s.nombre as servicio_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    WHERE c.barbero_id = ? AND c.estado = 'completada'
");
$stmt->execute([$_SESSION['user_id']]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Barbero</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?> (Barbero)</h2>
    <a href="logout.php">Cerrar Sesión</a>

    <?php if (isset($message)) echo "<p style='color: " . (strpos($message, 'Error') === false ? 'green' : 'red') . ";'>$message</p>"; ?>

    <h3>Citas Pendientes y Confirmadas</h3>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($citas as $cita): ?>
            <tr>
                <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                <td><?php echo htmlspecialchars($cita['cliente_nombre']); ?></td>
                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="confirmar">
                        <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                        <button type="submit">Confirmar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($citas)): ?>
            <tr><td colspan="6">No hay citas pendientes o confirmadas.</td></tr>
        <?php endif; ?>
    </table>

    <h3>Servicios Realizados</h3>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Servicio</th>
            <th>Precio</th>
        </tr>
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
    </table>
</body>
</html>