<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('cliente');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$message = '';
$fecha = $_POST['fecha'] ?? '';
$barbero_id = $_POST['barbero_id'] ?? '';
$servicio_id = $_POST['servicio_id'] ?? '';
$available_slots = [];

// Fetch services and barberos
$stmt = $pdo->query("SELECT id, nombre FROM servicios ORDER BY nombre");
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'barbero' AND activo = 1 ORDER BY nombre");
$barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check barbero availability
function isBarberoAvailable($pdo, $barbero_id, $fecha, $hora, $duracion) {
    $dia_semana = date('N', strtotime($fecha)); // 1=Monday, 7=Sunday
    $hora_inicio = $hora;
    $hora_fin = date('H:i:s', strtotime("$hora + $duracion minutes"));

    // Check if barbero is active
    $stmt = $pdo->prepare("SELECT activo FROM usuarios WHERE id = ? AND rol = 'barbero'");
    $stmt->execute([$barbero_id]);
    if (!$stmt->fetchColumn()) {
        return "El barbero no está disponible.";
    }

    // Check barbero's schedule
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM horarios_barberos 
        WHERE barbero_id = ? 
        AND dia_semana = ? 
        AND hora_inicio <= ? 
        AND hora_fin >= ?
    ");
    $stmt->execute([$barbero_id, $dia_semana, $hora_inicio, $hora_fin]);
    if ($stmt->fetchColumn() == 0) {
        return "El barbero no está disponible en ese horario.";
    }

    // Check for conflicting citas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE barbero_id = ? 
        AND fecha = ? 
        AND estado IN ('pendiente', 'confirmada')
        AND (
            (hora <= ? AND ADDTIME(hora, SEC_TO_TIME((SELECT duracion * 60 FROM servicios WHERE id = servicio_id)) > ?) 
            OR (hora < ? AND ADDTIME(hora, SEC_TO_TIME((SELECT duracion * 60 FROM servicios WHERE id = servicio_id)) >= ?)
        )
    ");
    $stmt->execute([$barbero_id, $fecha, $hora_inicio, $hora_inicio, $hora_fin, $hora_fin]);
    if ($stmt->fetchColumn() > 0) {
        return "El barbero ya tiene una cita en ese horario.";
    }

    return true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (empty($fecha) || empty($barbero_id) || empty($servicio_id)) {
        $message = "Error: Todos los campos son obligatorios.";
    } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        $message = "Error: No se pueden consultar fechas pasadas.";
    } else {
        // Get service duration
        $stmt = $pdo->prepare("SELECT duracion FROM servicios WHERE id = ?");
        $stmt->execute([$servicio_id]);
        $duracion = $stmt->fetchColumn();

        if (!$duracion) {
            $message = "Error: Servicio no válido.";
        } else {
            // Get barbero's schedule
            $dia_semana = date('N', strtotime($fecha));
            $stmt = $pdo->prepare("
                SELECT hora_inicio, hora_fin 
                FROM horarios_barberos 
                WHERE barbero_id = ? AND dia_semana = ?
            ");
            $stmt->execute([$barbero_id, $dia_semana]);
            $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($schedule) {
                $start = strtotime($schedule['hora_inicio']);
                $end = strtotime($schedule['hora_fin']);
                $interval = 15 * 60; // 15-minute intervals

                for ($time = $start; $time <= $end - ($duracion * 60); $time += $interval) {
                    $hora = date('H:i:s', $time);
                    if (isBarberoAvailable($pdo, $barbero_id, $fecha, $hora, $duracion) === true) {
                        $available_slots[] = $hora;
                    }
                }

                if (empty($available_slots)) {
                    $message = "No hay horarios disponibles para el barbero en esa fecha.";
                }
            } else {
                $message = "El barbero no tiene horarios definidos para ese día.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Disponibilidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Barbería</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_cliente.php">Mis Citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agendar_cita.php">Agendar Cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="disponibilidad_citas.php">Consultar Disponibilidad</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content with Sidebar -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_cliente.php">Mis Citas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="agendar_cita.php">Agendar Cita</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="disponibilidad_citas.php">Consultar Disponibilidad</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-4">Consultar Disponibilidad</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" class="mt-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="servicio_id" class="form-label">Servicio</label>
                        <select class="form-select" id="servicio_id" name="servicio_id" required>
                            <option value="">Seleccione un servicio</option>
                            <?php foreach ($servicios as $servicio): ?>
                                <option value="<?php echo $servicio['id']; ?>" <?php echo $servicio_id == $servicio['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($servicio['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="barbero_id" class="form-label">Barbero</label>
                        <select class="form-select" id="barbero_id" name="barbero_id" required>
                            <option value="">Seleccione un barbero</option>
                            <?php foreach ($barberos as $barbero): ?>
                                <option value="<?php echo $barbero['id']; ?>" <?php echo $barbero_id == $barbero['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($barbero['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Consultar</button>
                    <a href="dashboard_cliente.php" class="btn btn-secondary">Volver</a>
                </form>

                <?php if (!empty($available_slots)): ?>
                    <h3 class="mt-4">Horarios Disponibles</h3>
                    <ul class="list-group">
                        <?php foreach ($available_slots as $slot): ?>
                            <li class="list-group-item">
                                <?php echo date('h:i A', strtotime($slot)); ?>
                                <a href="agendar_cita.php?fecha=<?php echo urlencode($fecha); ?>&hora=<?php echo urlencode($slot); ?>&servicio_id=<?php echo urlencode($servicio_id); ?>&barbero_id=<?php echo urlencode($barbero_id); ?>" class="btn btn-sm btn-success ms-2">Agendar</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>