<?php
session_start();



function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkRole($requiredRole) {
    if (!isLoggedIn() || $_SESSION['rol'] !== $requiredRole) {
        header('Location: login.php');
        exit();
    }
}

function login($email, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = TRUE");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        return true;
    }
    return false;
}
?>