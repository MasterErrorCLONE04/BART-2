<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicio_id = $_POST['servicio_id'];
    $barbero_id = $_POST['barbero_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    // Validar disponibilidad (básica)
    $stmt = $pdo->prepare("SELECT * FROM citas WHERE barbero_id = ? AND fecha = ? AND hora = ?");
    $stmt->execute([$barbero_id, $fecha, $hora]);
    if ($stmt->rowCount() > 0) {
        $error = "El horario ya está ocupado.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO citas (cliente_id, barbero_id, servicio_id, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$_SESSION['user_id'], $barbero_id, $servicio_id, $fecha, $hora]);
        header('Location: dashboard_cliente.php');
        exit();
    }
}

// Obtener servicios y barberos
$servicios = $pdo->query("SELECT * FROM servicios WHERE activo = TRUE")->fetchAll(PDO::FETCH_ASSOC);
$barberos = $pdo->query("SELECT * FROM usuarios WHERE rol = 'barbero' AND activo = TRUE")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Agendar Cita</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Servicio:</label>
        <select name="servicio_id" required>
            <?php foreach ($servicios as $servicio): ?>
                <option value="<?php echo $servicio['id']; ?>"><?php echo $servicio['nombre']; ?> ($<?php echo $servicio['precio']; ?>)</option>
            <?php endforeach; ?>
        </select><br>
        <label>Barbero:</label>
        <select name="barbero_id" required>
            <?php foreach ($barberos as $barbero): ?>
                <option value="<?php echo $barbero['id']; ?>"><?php echo $barbero['nombre']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <label>Fecha:</label>
        <input type="date" name="fecha" required><br>
        <label>Hora:</label>
        <input type="time" name="hora" required><br>
        <button type="submit">Agendar</button>
    </form>
    <a href="dashboard_cliente.php">Volver</a>
</body>
</html>