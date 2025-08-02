<?php 
session_start(); // Iniciamos la sesión para poder trabajar con variables como 'carrito' o 'usuario_id'
?>
<!DOCTYPE html> <!-- Declaramos que el documento es HTML5 -->
<html> <!-- Inicia la estructura del documento HTML -->
<head>
    <meta charset="UTF-8"> <!-- Configura la codificación para que se muestren correctamente los caracteres especiales -->
    <title>Tienda Urbana</title> <!-- Título que aparecerá en la pestaña del navegador -->

    <!-- Enlazamos una hoja de estilos externa llamada estilos.css -->
    <link rel="stylesheet" href="estilos.css">
</head>
<body> <!-- Comienza el cuerpo visible de la página -->

<header> <!-- Sección de encabezado que contiene el título y la navegación -->
    <h1>Tienda Urbana</h1> <!-- Título principal que se muestra en la parte superior de la página -->

    <nav> <!-- Menú de navegación con enlaces hacia otras páginas del sitio -->
        <a href="inicio.php">Inicio</a> | <!-- Enlace a la página de inicio -->
        <a href="catalogo.php">Catálogo</a> | <!-- Enlace al catálogo de productos -->
        <a href="carrito.php">Carrito 🛒</a> | <!-- Enlace al carrito de compras -->
        <a href="logout.php">Cerrar sesión</a> <!-- Enlace para cerrar la sesión del usuario -->
    </nav>
</header>

<main> <!-- Comienza la sección principal del contenido -->
