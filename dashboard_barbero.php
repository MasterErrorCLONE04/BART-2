<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('barbero');

// Determine which section to display
$section = isset($_GET['section']) ? $_GET['section'] : 'citas';
$valid_sections = ['citas', 'servicios', 'pagos'];
if (!in_array($section, $valid_sections)) {
    $section = 'citas';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Barbero</title>
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
                        <a class="nav-link <?php echo $section === 'citas' ? 'active' : ''; ?>" href="?section=citas">Citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'servicios' ? 'active' : ''; ?>" href="?section=servicios">Servicios Realizados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $section === 'pagos' ? 'active' : ''; ?>" href="?section=pagos">Pagos</a>
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
                            <a class="nav-link <?php echo $section === 'citas' ? 'active' : ''; ?>" href="?section=citas">Citas Pendientes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $section === 'servicios' ? 'active' : ''; ?>" href="?section=servicios">Servicios Realizados</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $section === 'pagos' ? 'active' : ''; ?>" href="?section=pagos">Pagos Realizados</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> (Barbero)</h2>
                <?php
                // Include the appropriate section
                switch ($section) {
                    case 'citas':
                        include 'citas_barbero.php';
                        break;
                    case 'servicios':
                        include 'servicios_barbero.php';
                        break;
                    case 'pagos':
                        include 'pagos_barbero.php';
                        break;
                }
                ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>