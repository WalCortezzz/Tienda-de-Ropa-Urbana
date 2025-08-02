<?php
session_start(); // Iniciamos sesión pa’ poder usar $_SESSION

// Si el usuario no ha iniciado sesión, lo mandamos al login
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php"); // Lo redirigimos si no hay nombre guardado
    exit(); // Cerramos aquí pa’ que no siga ejecutando nada más
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Soporte pa’ acentos y caracteres especiales -->
    <title>Inicio - Tienda Urbana</title> <!-- Nombre que sale en la pestaña -->

    <!-- Estilos básicos pa’ que se vea bonito -->
    <style>
        body {
            background-color: #ADD8E6; /* Fondo azul clarito */
            font-family: Arial, sans-serif; /* Letra limpia y moderna */
            color: #000; /* Texto en negro */
            margin: 0;
            padding: 40px;
            text-align: center; /* Todo centradito */
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
            max-width: 400px; /* No pasa de 400px */
            width: 100%; /* Se adapta al contenedor */
            height: auto; /* Respeta la proporción */
            margin: 20px auto 0 auto;
            display: block;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2); /* Sombra suave */
        }
    </style>
</head>
<body>

    <!-- Título grandote arriba -->
    <h1>Bienvenido a tu tienda urbana</h1>

    <!-- Aquí saludamos al usuario con su nombre (sacado de la sesión) -->
    <p>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 🔥</p>

    <!-- Imagen urbana de adorno -->
    <img class="graffiti" src="https://static.vecteezy.com/system/resources/previews/002/470/927/non_2x/graffiti-urban-style-poster-with-set-icons-vector.jpg" alt="Graffiti Urban Style" />

    <!-- Botón para ir a ver el catálogo de productos -->
    <a href="catalogo.php">Ver catálogo</a>

    <!-- Botón para cerrar sesión -->
    <a href="logout.php">Cerrar sesión</a>

</body>
</html>
