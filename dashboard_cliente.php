<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

// Cancelar cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
    $cita_id = $_POST['cita_id'];
    $stmt = $pdo->prepare("UPDATE citas SET estado = 'cancelada', fecha_actualizacion = CURRENT_TIMESTAMP 
                           WHERE id = ? AND cliente_id = ? AND estado IN ('pendiente', 'confirmada')");
    $stmt->execute([$cita_id, $_SESSION['user_id']]);
    $message = $stmt->rowCount() > 0 ? "Cita cancelada exitosamente." : "Error: No se pudo cancelar la cita.";
}

// Obtener citas
$stmt = $pdo->prepare("
    SELECT c.*, s.nombre as servicio_nombre, u.nombre as barbero_nombre 
    FROM citas c 
    JOIN servicios s ON c.servicio_id = s.id 
    JOIN usuarios u ON c.barbero_id = u.id 
    WHERE c.cliente_id = ?
    ORDER BY c.fecha DESC, c.hora DESC
");
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
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> (Cliente)</h2>
    
    <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    <a href="agendar_cita.php">Agendar Cita</a>

    <?php if (isset($message)): ?>
        <p style="color: <?php echo strpos($message, 'Error') === false ? 'green' : 'red'; ?>;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h3>Tus Citas</h3>
    <table>
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Servicio</th>
            <th>Barbero</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>

        <?php if (!empty($citas)): ?>
            <?php foreach ($citas as $cita): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                    <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['barbero_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                    <td>
                        <?php if (in_array($cita['estado'], ['pendiente', 'confirmada'])): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="cancelar">
                                <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                                <button type="submit" class="action-btn" style="background-color: #e74c3c;">Cancelar</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No tienes citas registradas.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
