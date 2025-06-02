<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Mensaje de retroalimentación
$message = '';

// Crear nuevo barbero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (?, ?, ?, ?, 'barbero')");
        $stmt->execute([$nombre, $email, $telefono, $password]);
        $message = "Barbero creado exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al crear barbero: " . $e->getMessage();
    }
}

// Editar barbero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ? AND rol = 'barbero'");
        $stmt->execute([$nombre, $email, $telefono, $id]);
        $message = "Barbero actualizado exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al actualizar barbero: " . $e->getMessage();
    }
}

// Activar/Desactivar barbero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = $_POST['id'];
    $activo = $_POST['activo'] === '1' ? 0 : 1;

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'barbero'");
        $stmt->execute([$activo, $id]);
        $message = $activo ? "Barbero activado exitosamente." : "Barbero desactivado exitosamente.";
    } catch (PDOException $e) {
        $message = "Error al cambiar estado: " . $e->getMessage();
    }
}

// Obtener lista de barberos
$stmt = $pdo->query("SELECT * FROM usuarios WHERE rol = 'barbero' ORDER BY nombre");
$barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Barberos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Gestionar Barberos</h2>
    <p><a href="dashboard_admin.php">Volver al Dashboard</a> | <a href="logout.php">Cerrar Sesión</a></p>
    
    <?php if ($message) echo "<p style='color: " . (strpos($message, 'Error') === false ? 'green' : 'red') . ";'>$message</p>"; ?>

    <!-- Formulario para crear barbero -->
    <h3>Agregar Nuevo Barbero</h3>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Teléfono:</label>
        <input type="text" name="telefono"><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Crear Barbero</button>
    </form>

    <!-- Lista de barberos -->
    <h3>Lista de Barberos</h3>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($barberos as $barbero): ?>
            <tr>
                <td><?php echo htmlspecialchars($barbero['nombre']); ?></td>
                <td><?php echo htmlspecialchars($barbero['email']); ?></td>
                <td><?php echo htmlspecialchars($barbero['telefono'] ?: '-'); ?></td>
                <td><?php echo $barbero['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                <td>
                    <!-- Formulario para editar -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $barbero['id']; ?>">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($barbero['nombre']); ?>" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($barbero['email']); ?>" required>
                        <input type="text" name="telefono" value="<?php echo htmlspecialchars($barbero['telefono']); ?>">
                        <button type="submit">Actualizar</button>
                    </form>
                    <!-- Formulario para activar/desactivar -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?php echo $barbero['id']; ?>">
                        <input type="hidden" name="activo" value="<?php echo $barbero['activo']; ?>">
                        <button type="submit"><?php echo $barbero['activo'] ? 'Desactivar' : 'Activar'; ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>