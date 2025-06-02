<?php
$host = '127.0.0.1';
$db = 'barberia_bart';
$user = 'root'; // Ajusta según tu configuración
$pass = ''; // Ajusta según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>