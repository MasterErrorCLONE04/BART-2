<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = $_POST['password'];

    // Server-side validation
    if (empty($nombre) || empty($email)) {
        $_SESSION['message'] = "Error: Nombre y email son obligatorios.";
        header("Location: gestion_barberos.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Error: El email no es válido.";
        header("Location: gestion_barberos.php");
        exit();
    }

    try {
        // Check for duplicate email (excluding current barbero)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['message'] = "Error: El email ya está registrado por otro usuario.";
            header("Location: gestion_barberos.php");
            exit();
        }

        if (!empty($password)) {
            // Update with new password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, password = ? WHERE id = ? AND rol = 'barbero'");
            $stmt->execute([$nombre, $email, $telefono, $password_hash, $id]);
        } else {
            // Update without changing password
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ? AND rol = 'barbero'");
            $stmt->execute([$nombre, $email, $telefono, $id]);
        }
        $_SESSION['message'] = "Barbero actualizado exitosamente.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al actualizar barbero: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Error: Solicitud inválida.";
}

header("Location: gestion_barberos.php");
exit();
?>