<?php
require_once 'auth.php';
require_once 'conexion.php';
checkRole('administrador');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check for feedback message in session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Barberos</title>
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
                        <a class="nav-link" href="dashboard_admin.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_barberos.php">Gestionar Barberos</a>
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
                            <a class="nav-link" href="dashboard_admin.php?section=citas">Citas Pendientes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_admin.php?section=ingresos">Ingresos por Día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard_admin.php?section=clientes">Clientes Atendidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="gestion_barberos.php">Gestionar Barberos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestion_pagos.php">Gestionar Pagos</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-4">Gestionar Barberos</h2>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <?php include 'lista_barberos.php'; ?>
            </main>
        </div>
    </div>

    <!-- Modal for Creating Barbero -->
    <div class="modal fade" id="createBarberoModal" tabindex="-1" aria-labelledby="createBarberoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBarberoModalLabel">Agregar Nuevo Barbero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="crear_barbero.php" id="createBarberoForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear Barbero</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Barbero -->
    <div class="modal fade" id="editBarberoModal" tabindex="-1" aria-labelledby="editBarberoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBarberoModalLabel">Editar Barbero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="actualizar_barbero.php" id="editBarberoForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="edit_telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Nueva Contraseña (opcional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Barbero</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate edit modal with barbero data
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const email = this.getAttribute('data-email');
                const telefono = this.getAttribute('data-telefono');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nombre').value = nombre;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_telefono').value = telefono;
                document.getElementById('edit_password').value = '';
            });
        });
    </script>
</body>
</html>