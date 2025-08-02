<?php
session_start(); // Arrancamos la sesión para poder usar las variables de sesión

// Revisamos si la variable 'admin' está definida en la sesión
// Si no está, significa que no está logueado como admin, así que lo mandamos al login del admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php"); // Redirige al login de admin
    exit(); // Para que no siga ejecutando nada más
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Configuramos que el charset sea UTF-8 -->
    <title>Panel de Control - Dashboard</title> <!-- Título que aparece en la pestaña -->
    <style>
        /* Estilos para que se vea decente el panel */
        body {
            font-family: Arial, sans-serif; /* Letra sencilla y común */
            background-color: #f0f4f8; /* Fondo gris clarito */
            color: #333; /* Color de texto oscuro pero no negro fuerte */
            padding: 40px; /* Espacio interno para que no quede pegado a los bordes */
            max-width: 900px; /* Ancho máximo para que no sea muy ancho en pantallas grandes */
            margin: auto; /* Centrar la página horizontalmente */
        }
        h1 {
            text-align: center; /* Centrar el título */
            margin-bottom: 40px; /* Espacio debajo del título */
        }
        section {
            background: #fff; /* Fondo blanco para las secciones */
            padding: 25px; /* Espacio dentro de las secciones */
            margin-bottom: 30px; /* Espacio debajo de cada sección */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1); /* Sombra leve para dar profundidad */
        }
        ul {
            list-style: none; /* Quita los puntos de la lista */
            padding-left: 0; /* Quita el espacio a la izquierda */
        }
        li {
            margin: 10px 0; /* Espacio arriba y abajo de cada ítem */
        }
        a {
            color: #0077cc; /* Color azul para los links */
            text-decoration: none; /* Sin subrayado */
            font-weight: 600; /* Letra un poco más gruesa */
        }
        a:hover {
            text-decoration: underline; /* Subrayado al pasar el mouse */
        }
        .logout {
            text-align: center; /* Centra el contenedor del link de cerrar sesión */
            margin-top: 40px; /* Espacio arriba */
        }
    </style>
</head>
<body>

    <!-- Aquí saludamos al admin con su nombre que guardamos en sesión -->
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin']); ?> 👋</h1>

    <!-- Sección para la gestión de usuarios -->
    <section>
        <h2>Gestión de usuarios</h2>
        <ul>
            <li><a href="admin_usuarios.php">👥 Ver y gestionar usuarios</a></li>
        </ul>
    </section>

    <!-- Sección para la gestión de productos -->
    <section>
        <h2>Gestión de productos</h2>
        <ul>
            <li><a href="lista_de_productos.php">📦 Ver productos</a></li>
            <li><a href="agregar_productos.php">➕ Agregar producto</a></li>
        </ul>
    </section>

    <!-- Sección para la gestión de pedidos -->
    <section>
        <h2>Gestión de pedidos</h2>
        <ul>
            <li><a href="pedidos.php">📋 Ver pedidos</a></li>
        </ul>
    </section>

    <!-- Aquí está el link para cerrar sesión, aunque solo es un link, no destruye sesión -->
    <div class="login">
        <a href="admin_login.php">🔓 Cerrar sesión</a>
    </div>

</body>
</html>
