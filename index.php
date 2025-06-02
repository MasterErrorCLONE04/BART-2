<?php
require_once 'auth.php';

if (isLoggedIn()) {
    switch ($_SESSION['rol']) {
        case 'administrador':
            header('Location: dashboard_admin.php');
            break;
        case 'barbero':
            header('Location: dashboard_barbero.php');
            break;
        case 'cliente':
            header('Location: dashboard_cliente.php');
            break;
    }
} else {
    header('Location: login.php');
}
exit();
?>