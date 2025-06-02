<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Mensaje de retroalimentación
$message = '';

// Registrar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar_pago') {
    $barbero_id = $_POST['barbero_id'];
    $monto = $_POST['monto'];
    $periodo_inicio = $_POST['periodo_inicio'];
    $periodo_fin = $_POST['periodo_fin'];
    $notas = $_POST['notas'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO pagos_barberos (barbero_id, monto, periodo_inicio, periodo_fin, notas) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$barbero_id, $monto, $periodo_inicio, $periodo_fin, $notas]);
        $message = "Pago registrado exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al registrar pago: " . $e->getMessage();
    }
}

// Obtener comisiones pendientes
$stmt = $pdo->query("SELECT * FROM vista_comisiones_pendientes");
$comisiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Pagos y Comisiones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Gestionar Pagos y Comisiones</h2>
    <p><a href="dashboard_admin.php">Volver al Dashboard</a> | <a href="logout.php">Cerrar Sesión</a></p>
    
    <?php if ($message) echo "<p style='color: " . (strpos($message, 'Error') === false ? 'green' : 'red') . ";'>$message</p>"; ?>

    <h3>Comisiones Pendientes</h3>
    <table>
        <tr>
            <th>Barbero</th>
            <th>Servicios Realizados</th>
            <th>Total Comisiones</th>
            <th>Período</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($comisiones as $comision): ?>
            <tr>
                <td><?php echo htmlspecialchars($comision['barbero_nombre']); ?></td>
                <td><?php echo $comision['servicios_realizados']; ?></td>
                <td>$<?php echo number_format($comision['total_comisiones'], 2); ?></td>
                <td><?php echo htmlspecialchars($comision['fecha_inicio']) . ' a ' . htmlspecialchars($comision['fecha_fin']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="registrar_pago">
                        <input type="hidden" name="barbero_id" value="<?php echo $comision['barbero_id']; ?>">
                        <input type="hidden" name="monto" value="<?php echo $comision['total_comisiones']; ?>">
                        <input type="hidden" name="periodo_inicio" value="<?php echo $comision['fecha_inicio']; ?>">
                        <input type="hidden" name="periodo_fin" value="<?php echo $comision['fecha_fin']; ?>">
                        <input type="text" name="notas" placeholder="Notas (opcional)">
                        <button type="submit">Registrar Pago</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($comisiones)): ?>
            <tr><td colspan="5">No hay comisiones pendientes.</td></tr>
        <?php endif; ?>
    </table>

    <h3>Registrar Pago Manual</h3>
    <form method="POST">
        <input type="hidden" name="action" value="registrar_pago">
        <label>Barbero:</label>
        <select name="barbero_id" required>
            <?php
            $stmt = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'barbero' AND activo = TRUE");
            $barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($barberos as $barbero): ?>
                <option value="<?php echo $barbero['id']; ?>"><?php echo htmlspecialchars($barbero['nombre']); ?></option>
            <?php endforeach; ?>
        </select><br>
        <label>Monto:</label>
        <input type="number" step="0.01" name="monto" required><br>
        <label>Período Inicio:</label>
        <input type="date" name="periodo_inicio" required><br>
        <label>Período Fin:</label>
        <input type="date" name="periodo_fin" required><br>
        <label>Notas:</label>
        <input type="text" name="notas"><br>
        <button type="submit">Registrar Pago</button>
    </form>
</body>
</html>