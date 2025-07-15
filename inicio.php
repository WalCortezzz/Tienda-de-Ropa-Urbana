<?php
session_start();

// Validar que el usuario haya iniciado sesión y que exista el nombre
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Tienda Urbana</title>
    <style>
        body {
            background-color: #ADD8E6;
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 40px;
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            color: #000;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            background-color: #000;
            color: #fff;
        }

        img.graffiti {
            max-width: 400px;
            width: 100%;
            height: auto;
            margin: 20px auto 0 auto;
            display: block;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <h1>Bienvenido a tu tienda urbana</h1>
    <p>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 🔥</p>

    <img class="graffiti" src="https://static.vecteezy.com/system/resources/previews/002/470/927/non_2x/graffiti-urban-style-poster-with-set-icons-vector.jpg" alt="Graffiti Urban Style" />

    <a href="catalogo.php">Ver catálogo</a>
    <a href="logout.php">Cerrar sesión</a>

</body>
</html>
