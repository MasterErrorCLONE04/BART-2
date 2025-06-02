<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cita_id'])) {
    $cita_id = $_POST['cita_id'];

    try {
        // Obtener el precio del servicio
        $stmt = $pdo->prepare("
            SELECT s.precio 
            FROM citas c 
            JOIN servicios s ON c.servicio_id = s.id 
            WHERE c.id = ? AND c.barbero_id = ?
        ");
        $stmt->execute([$cita_id, $_SESSION['user_id']]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($servicio) {
            // Actualizar estado y precio_final
            $stmt = $pdo->prepare("
                UPDATE citas 
                SET estado = 'completada', precio_final = ?, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id = ? AND barbero_id = ?
            ");
            $stmt->execute([$servicio['precio'], $cita_id, $_SESSION['user_id']]);
            $message = "Cita confirmada exitosamente.";
        } else {
            $message = "Error: Cita no encontrada o no autorizada.";
        }
    } catch (PDOException $e) {
        $message = "Error al confirmar cita: " . $e->getMessage();
    }

    // Store message in session and redirect
    $_SESSION['message'] = $message;
    header("Location: dashboard_barbero.php?section=citas");
    exit();
}
?>