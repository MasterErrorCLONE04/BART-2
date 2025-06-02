<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (?, ?, ?, ?, 'cliente')");
        $stmt->execute([$nombre, $email, $telefono, $password]);
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $error = "Error al registrarse: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Barbería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Registrarse</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Teléfono:</label>
        <input type="text" name="telefono"><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Registrarse</button>
    </form>
    <p><a href="login.php">Volver al login</a></p>
</body>
</html>