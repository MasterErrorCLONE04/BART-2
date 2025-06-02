<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['SESSION']['csrf_token'] && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
    $cita_id = $_POST['cita_id'];

    try {
        $stmt = $pdo->prepare("UPDATE citas SET estado = 'cancelada', fecha_actualizacion = CURRENT_TIMESTAMP 
                               WHERE id = ? AND cliente_id = ? AND estado IN ('pendiente', 'confirmada')");
        $stmt->execute([$cita_id, $_SESSION['user_id']]);
        $_SESSION['message'] = $stmt->rowCount() > 0 ? "Cita cancelada exitosamente." : "Error: No se pudo cancelar la cita.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al cancelar cita: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Error: Solicitud inválida.";
}

header("Location: dashboard_cliente.php");
exit();
?>