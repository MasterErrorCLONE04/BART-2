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
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Barbería Tu Estilo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #0a0a0a;
            --accent: #e67e22;
            --text: #fff;
            --gray: #999;
            --bg-light: #f9f9f9;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--primary);
            color: var(--text);
        }

        /* Navbar */
        nav {
            background: #111;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav h1 {
            font-size: 1.8rem;
            color: var(--accent);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        nav ul li a {
            color: var(--text);
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: var(--accent);
        }

        /* Hero Section */
        .hero {
            background: url('https://images.unsplash.com/photo-1604779360885-f8a9c33f44a1') no-repeat center center/cover;
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #fff;
            text-shadow: 2px 2px #000;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #eee;
        }

        .hero .buttons a {
            background: var(--accent);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            margin: 0 10px;
            transition: 0.3s;
            font-weight: bold;
        }

        .hero .buttons a:hover {
            background: #d35400;
        }

        /* Services */
        .services {
            background: #fff;
            color: #333;
            padding: 60px 20px;
            text-align: center;
        }

        .services h3 {
            font-size: 2rem;
            margin-bottom: 40px;
        }

        .service-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
        }

        .service {
            background: #f4f4f4;
            padding: 30px;
            border-radius: 10px;
            width: 280px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .service h4 {
            margin-bottom: 10px;
            color: #111;
        }

        .service p {
            color: #666;
        }

        /* Call to Action */
        .cta {
            background: var(--accent);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .cta a {
            background: #fff;
            color: var(--accent);
            padding: 12px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
        }

        .cta a:hover {
            background: #f4f4f4;
        }

        /* Social */
        .social {
            background: #111;
            padding: 30px 0;
            text-align: center;
        }

        .social a {
            color: #fff;
            margin: 0 10px;
            font-size: 1.5rem;
            text-decoration: none;
            transition: 0.3s;
        }

        .social a:hover {
            color: var(--accent);
        }

        /* Footer */
        footer {
            background: #000;
            text-align: center;
            color: #aaa;
            padding: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- NAV -->
    <nav>
        <h1>BART-2</h1>
        <ul>
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#servicios">Servicios</a></li>
            <li><a href="/BART3/login.php">Iniciar sesión</a></li>
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero" id="inicio">
        <h2>Barbería Tu Estilo</h2>
        <p>Estilo moderno, atención personalizada. Agenda hoy tu cita.</p>
        <div class="buttons">
            <a href="register.php">Regístrate</a>
            <a href="login.php" id="login">Inicia Sesión</a>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section class="services" id="servicios">
        <h3>Servicios Profesionales</h3>
        <div class="service-list">
            <div class="service">
                <h4>Corte Clásico</h4>
                <p>Estilo limpio, elegante y profesional para toda ocasión.</p>
            </div>
            <div class="service">
                <h4>Afeitado Tradicional</h4>
                <p>Con toallas calientes y productos de lujo para tu piel.</p>
            </div>
            <div class="service">
                <h4>Diseño de Barba</h4>
                <p>Déjala en manos de expertos para un look impecable.</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <p>¿Primera vez con nosotros? ¡Regístrate y recibe tu primer corte con 20% de descuento!</p>
        <a href="register.php">Registrarme</a>
    </section>

    <!-- REDES -->
    <section class="social">
        <a href="#">🐦</a>
        <a href="#">📸</a>
        <a href="#">📘</a>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2025 Barbería Tu Estilo. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
