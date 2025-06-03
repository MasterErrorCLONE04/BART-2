<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$message = '';
$barbero_id = $_POST['barbero_id'] ?? '';
$current_schedules = [];

// Fetch active barberos
$stmt = $pdo->query("SELECT id, nombre FROM usuarios WHERE rol = 'barbero' AND activo = 1 ORDER BY nombre");
$barberos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Days of the week mapping
$days = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (empty($barbero_id)) {
        $message = "Error: Seleccione un barbero.";
    } else {
        // Get selected days and times
        $selected_days = $_POST['dias'] ?? [];
        $hora_inicio = $_POST['hora_inicio'] ?? [];
        $hora_fin = $_POST['hora_fin'] ?? [];

        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Delete existing schedules for the barbero (to replace with new ones)
            $stmt = $pdo->prepare("DELETE FROM horarios_barberos WHERE barbero_id = ?");
            $stmt->execute([$barbero_id]);

            // Insert new schedules
            foreach ($selected_days as $dia) {
                $dia = (int)$dia;
                if (isset($hora_inicio[$dia], $hora_fin[$dia]) && !empty($hora_inicio[$dia]) && !empty($hora_fin[$dia])) {
                    // Validate times
                    if (strtotime($hora_inicio[$dia]) >= strtotime($hora_fin[$dia])) {
                        throw new Exception("Error: La hora de inicio debe ser anterior a la hora de fin para el {$days[$dia]}.");
                    }

                    $stmt = $pdo->prepare("
                        INSERT INTO horarios_barberos (barbero_id, dia_semana, hora_inicio, hora_fin, activo)
                        VALUES (?, ?, ?, ?, TRUE)
                    ");
                    $stmt->execute([$barbero_id, $dia, $hora_inicio[$dia], $hora_fin[$dia]]);
                }
            }

            $pdo->commit();
            $message = "Horarios actualizados exitosamente.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Error al guardar horarios: " . $e->getMessage();
        }
    }
}

// Fetch current schedules for selected barbero
if (!empty($barbero_id)) {
    $stmt = $pdo->prepare("SELECT dia_semana, hora_inicio, hora_fin FROM horarios_barberos WHERE barbero_id = ? AND activo = TRUE ORDER BY dia_semana");
    $stmt->execute([$barbero_id]);
    $current_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Horarios de Barberos</title>
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
                        <a class="nav-link" href="dashboard_admin.php">Resumen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_barberos.php">Gestión de Barberos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_horarios.php">Horarios de Barberos</a>
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
                            <a class="nav-link" href="dashboard_admin.php">Resumen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_barberos.php">Gestión de Barberos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="gestion_horarios.php">Horarios de Barberos</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-4">Gestión de Horarios de Barberos</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="mt-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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

                    <h5 class="mt-4">Días Laborales</h5>
                    <?php foreach ($days as $dia => $nombre): ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="dias[]" value="<?php echo $dia; ?>" id="dia_<?php echo $dia; ?>"
                                    <?php echo in_array(['dia_semana' => $dia], array_column($current_schedules, 'dia_semana')) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="dia_<?php echo $dia; ?>">
                                    <?php echo $nombre; ?>
                                </label>
                            </div>
                            <div class="row ms-4">
                                <div class="col-md-6">
                                    <label for="hora_inicio_<?php echo $dia; ?>" class="form-label">Hora Inicio</label>
                                    <input type="time" class="form-control" name="hora_inicio[<?php echo $dia; ?>]" id="hora_inicio_<?php echo $dia; ?>"
                                        value="<?php echo ($schedule = array_filter($current_schedules, fn($s) => $s['dia_semana'] == $dia)) ? reset($schedule)['hora_inicio'] : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="hora_fin_<?php echo $dia; ?>" class="form-label">Hora Fin</label>
                                    <input type="time" class="form-control" name="hora_fin[<?php echo $dia; ?>]" id="hora_fin_<?php echo $dia; ?>"
                                        value="<?php echo ($schedule = array_filter($current_schedules, fn($s) => $s['dia_semana'] == $dia)) ? reset($schedule)['hora_fin'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">Guardar Horarios</button>
                    <a href="dashboard_admin.php" class="btn btn-secondary">Volver</a>
                </form>

                <?php if (!empty($current_schedules)): ?>
                    <h3 class="mt-4">Horarios Actuales</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo $days[$schedule['dia_semana']]; ?></td>
                                    <td><?php echo date('h:i A', strtotime($schedule['hora_inicio'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($schedule['hora_fin'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>