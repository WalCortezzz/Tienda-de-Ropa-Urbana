<?php
session_start();

// Verifica si el carrito está vacío
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    header("Location: carrito.php");
    exit;
}

// Vaciar el carrito después de "procesar" la compra
unset($_SESSION['carrito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra Finalizada - Tienda Urbana</title>
    <style>
        body {
            background-color: #ADD8E6; /* Fondo azul claro */
            color: #000; /* Texto negro */
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }

        .btn {
            color: #000;
            text-decoration: none;
            font-size: 1rem;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn:hover {
            text-decoration: underline;
        }

        .gracias-img {
            max-width: 300px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <h1>¡Gracias por tu compra! 🔥</h1>
    <p>Tu pago ha sido procesado exitosamente. Pronto recibirás un correo con los detalles de tu pedido.</p>

    <!-- Imagen de agradecimiento -->
    <img src="https://media.tenor.com/vTLEBddctWYAAAAe/muchas-gracias.png" alt="Gracias" class="gracias-img">

    <div class="btn-container">
        <a href="inicio.php" class="btn">Volver al inicio</a>
        <a href="catalogo.php" class="btn">Seguir comprando</a>
    </div>

</body>
</html>
