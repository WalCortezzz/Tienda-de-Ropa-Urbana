<?php
// Iniciamos la sesión para poder usar variables de sesión
session_start();

// Verificamos si el carrito está vacío o no existe
// Si no hay productos en el carrito, redirigimos al usuario al carrito
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    header("Location: carrito.php"); // Lo mandamos de nuevo al carrito
    exit; // Detenemos la ejecución del script
}

// Conectamos a la base de datos con los datos del hosting (InfinityFree en este caso)
$conexion = new mysqli(
    "",    // Servidor de la base de datos
    "",               // Usuario de la base de datos
    "",            // Contraseña del usuario
    ""         // Nombre de la base de datos
);

// Verificamos si hubo un error al conectar
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error); // Si falla, mostramos el error y detenemos
}

// Verificamos si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    die("Debes iniciar sesión para finalizar la compra."); // Si no lo está, mostramos mensaje y detenemos
}

// Guardamos el ID del usuario que inició sesión
$id_usuario = $_SESSION['usuario_id'];

// Establecemos el estado inicial del pedido y la fecha actual
$estado = "pendiente"; // Todos los pedidos nuevos se guardan como "pendiente"
$fecha = date("Y-m-d H:i:s"); // Fecha y hora actual

// Recorremos el carrito (guardado en la sesión) y guardamos cada producto en la base de datos
foreach ($_SESSION['carrito'] as $item) {
    // Escapamos el nombre del producto por seguridad
    $producto = $conexion->real_escape_string($item['nombre']);

    // Convertimos la cantidad a número entero
    $cantidad = (int)$item['cantidad'];

    // Creamos la consulta para insertar el pedido
    $sql = "INSERT INTO pedidos (id_usuario, producto, cantidad, estado, fecha) VALUES (?, ?, ?, ?, ?)";

    // Preparamos y ejecutamos la consulta para evitar inyección SQL
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isiss", $id_usuario, $producto, $cantidad, $estado, $fecha);
    $stmt->execute();
}

// Vaciamos el carrito para dejarlo limpio después de la compra
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
            font-family: Arial, sans-serif; /* Fuente moderna y fácil de leer */
            text-align: center; /* Todo centrado */
            padding: 50px; /* Espacio alrededor del contenido */
        }

        h1 {
            font-size: 2.5rem; /* Tamaño grande para el título */
            margin-bottom: 20px; /* Espacio debajo del título */
        }

        p {
            font-size: 1.1rem; /* Tamaño estándar para el texto */
            margin-bottom: 30px; /* Espacio debajo del párrafo */
        }

        .btn-container {
            display: flex; /* Usamos flexbox para alinear los botones */
            justify-content: center; /* Centramos los botones horizontalmente */
            gap: 30px; /* Espacio entre los botones */
            margin-top: 30px; /* Espacio arriba de los botones */
        }

        .btn {
            color: #000; /* Texto negro */
            text-decoration: none; /* Quitamos el subrayado */
            font-size: 1rem; /* Tamaño del texto */
            border: none; /* Sin borde */
            background: none; /* Sin fondo */
            cursor: pointer; /* Cursor de mano al pasar */
        }

        .btn:hover {
            text-decoration: underline; /* Subrayado al pasar el mouse */
        }

        .gracias-img {
            max-width: 300px; /* Tamaño máximo de la imagen */
            margin-top: 30px; /* Espacio arriba de la imagen */
        }
    </style>
</head>
<body>

    <!-- Título principal de la página -->
    <h1>¡Gracias por tu compra! 🔥</h1>

    <!-- Mensaje de confirmación -->
    <p>Tu pago ha sido procesado exitosamente. Pronto recibirás un correo con los detalles de tu pedido.</p>

    <!-- Imagen de agradecimiento -->
    <img src="https://media.tenor.com/vTLEBddctWYAAAAe/muchas-gracias.png" alt="Gracias" class="gracias-img">

    <!-- Botones para volver al inicio o seguir comprando -->
    <div class="btn-container">
        <a href="inicio.php" class="btn">Volver al inicio</a>
        <a href="catalogo.php" class="btn">Seguir comprando</a>
    </div>

</body>
</html>
