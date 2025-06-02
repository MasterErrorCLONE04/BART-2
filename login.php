<?php
require_once 'auth.php';
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password, $pdo)) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Credenciales incorrectas o cuenta inactiva.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Barbería</title>
    <style>
        /* Reset de márgenes y padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }

        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .error {
            color: #d9534f;
            margin-bottom: 20px;
            font-size: 14px;
        }

        label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fafafa;
            color: #555;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
            background-color: #fff;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            font-size: 14px;
            color: #666;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required placeholder="Tu correo electrónico">
            
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required placeholder="Tu contraseña">
            
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <p><a href="register.php">¿No tienes cuenta? Regístrate aquí</a></p>
    </div>

</body>
</html>
