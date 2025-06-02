<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];

    // Server-side validation
    if (empty($nombre) || empty($email) || empty($password)) {
        $_SESSION['message'] = "Error: Todos los campos obligatorios deben completarse.";
        header("Location: gestion_barberos.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Error: El email no es válido.";
        header("Location: gestion_barberos.php");
        exit();
    }

    try {
        // Check for duplicate email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['message'] = "Error: El email ya está registrado.";
            header("Location: gestion_barberos.php");
            exit();
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (?, ?, ?, ?, 'barbero')");
        $stmt->execute([$nombre, $email, $telefono, $password_hash]);
        $_SESSION['message'] = "Barbero creado exitosamente.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al crear barbero: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Error: Solicitud inválida.";
}

header("Location: gestion_barberos.php");
exit();
?>