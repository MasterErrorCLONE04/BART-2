<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check for feedback message
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
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
                        <a class="nav-link active" href="dashboard_admin.php">Resumen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_barberos.php">Gestión de Barberos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_horarios.php">Horarios de Barberos</a>
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
                            <a class="nav-link active" href="dashboard_admin.php">Resumen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_barberos.php">Gestión de Barberos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_horarios.php">Horarios de Barberos</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> (Administrador)</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <div class="mt-4">
                    <h3>Resumen</h3>
                    <!-- Existing summary content (e.g., stats, charts) -->
                    <p>Gestione los horarios de los barberos desde <a href="gestion_horarios.php">Horarios de Barberos</a>.</p>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>