<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $id = $_POST['id'];
    $activo = $_POST['activo'] === '1' ? 0 : 1;

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ? AND rol = 'barbero'");
        $stmt->execute([$activo, $id]);
        $_SESSION['message'] = $activo ? "Barbero activado exitosamente." : "Barbero desactivado exitosamente.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al cambiar estado: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Error: Solicitud inválida.";
}

header("Location: gestion_barberos.php");
exit();
?>