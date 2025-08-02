<?php
session_start(); // Empiezo la sesión para poder usar variables de sesión

// Verifico si el usuario está logueado (tiene su id guardado)
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, lo mando a la página de login
    header("Location: login.php");
    exit;
}

// Cargo el carrito guardado en sesión, si no hay nada, dejo un arreglo vacío
$carrito = $_SESSION['carrito'] ?? [];

// Si viene un pedido para eliminar un producto del carrito (por URL)
if (isset($_GET['eliminar'])) {
    $eliminar_id = intval($_GET['eliminar']); // Tomo el índice del producto
    unset($_SESSION['carrito'][$eliminar_id]); // Lo borro del carrito
    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reordeno el arreglo para que no queden "huecos"
    header("Location: carrito.php"); // Recargo la página para actualizar el carrito
    exit;
}

// Si vienen datos para actualizar la cantidad de un producto (por formulario)
if (isset($_POST['actualizar'])) {
    $index = intval($_POST['index']); // Tomo la posición del producto a modificar
    $cantidad = intval($_POST['cantidad']); // Tomo la nueva cantidad que puso el usuario

    if ($cantidad > 0) {
        // Si la cantidad es mayor que 0, la actualizo
        $_SESSION['carrito'][$index]['cantidad'] = $cantidad;
    } else {
        // Si puso 0 o menos, elimino el producto del carrito
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reordeno el arreglo
    }
    header("Location: carrito.php"); // Recargo la página para reflejar los cambios
    exit;
}

// Calculo el total sumando cada producto por su cantidad y precio
$total = 0;
foreach ($carrito as $item) {
    $cantidad = $item['cantidad'] ?? 1; // Si no tiene cantidad, pongo 1
    $total += $item['precio'] * $cantidad; // Sumo precio x cantidad al total
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Esto es para que se vean bien las letras con acentos -->
    <title>Carrito de Compras - Tienda Urbana</title> <!-- Título que aparece en la pestaña -->

    <!-- Estilos para que se vea bonito -->
    <style>
        body {
            background-color: #ADD8E6; /* Fondo azul claro */
            font-family: Arial, sans-serif; /* Fuente de letra */
            color: #000; /* Color negro para el texto */
            margin: 0; /* Sin margen alrededor */
            padding: 40px; /* Espacio interno */
            text-align: center; /* Texto centrado */
        }
        h1 {
            font-size: 2.5em; /* Tamaño grande para el título */
            margin-bottom: 20px; /* Espacio abajo del título */
        }
        .carrito-table {
            margin: 0 auto; /* Centrar la tabla */
            width: 90%; /* Que ocupe 90% del ancho de la página */
            max-width: 900px; /* Máximo ancho 900px */
            border-collapse: collapse; /* Bordes de tabla unidos */
            background-color: #fff; /* Fondo blanco */
        }
        .carrito-table th,
        .carrito-table td {
            border: 1px solid #ccc; /* Bordes grises claros */
            padding: 12px; /* Espacio dentro de las celdas */
        }
        .carrito-table th {
            background-color: #333; /* Fondo oscuro para encabezados */
            color: #fff; /* Letras blancas */
        }
        .carrito-table td form {
            display: flex; /* Usar flexbox para ordenar los elementos */
            gap: 10px; /* Espacio entre los elementos */
            justify-content: center; /* Centrar dentro de la celda */
        }
        input[type="number"] {
            width: 60px; /* Ancho pequeño para el input de número */
            padding: 5px; /* Espacio interno */
        }
        .btn-eliminar,
        .btn-pagar,
        button {
            padding: 8px 16px; /* Espacio interno */
            margin: 8px; /* Margen externo */
            background-color: #000; /* Fondo negro */
            color: #fff; /* Texto blanco */
            text-decoration: none; /* Sin subrayado */
            border: none; /* Sin borde */
            border-radius: 4px; /* Bordes redondeados */
            cursor: pointer; /* Cursor mano para que parezca botón */
            transition: 0.3s; /* Transición suave para hover */
        }
        .btn-eliminar:hover,
        .btn-pagar:hover,
        button:hover {
            background-color: #444; /* Cambia a gris oscuro al pasar mouse */
        }
        .total {
            font-size: 1.5em; /* Texto más grande para el total */
            margin-top: 20px; /* Espacio arriba */
        }
        p a {
            text-decoration: none; /* Sin subrayado en links */
            color: #000; /* Negro para los links */
            margin: 0 10px; /* Margen a los lados */
        }
        p a:hover {
            text-decoration: underline; /* Subrayado al pasar mouse */
        }
    </style>
</head>
<body>

    <h1>Tu carrito de compras</h1> <!-- Título en la página -->

    <?php if (count($carrito) > 0): ?> <!-- Si hay productos en el carrito -->

        <table class="carrito-table"> <!-- Empiezo la tabla -->
            <thead>
                <tr>
                    <th>Producto</th> <!-- Columna para nombre -->
                    <th>Precio Unitario</th> <!-- Precio de cada unidad -->
                    <th>Cantidad</th> <!-- Cantidad que quiere el usuario -->
                    <th>Subtotal</th> <!-- Precio por cantidad -->
                    <th>Acción</th> <!-- Para eliminar o actualizar -->
                </tr>
            </thead>
            <tbody>
            <?php foreach ($carrito as $index => $item): ?> <!-- Recorro cada producto -->
                <?php 
                    $nombre = htmlspecialchars($item['nombre'] ?? 'Producto'); // Nombre seguro para mostrar
                    $cantidad = $item['cantidad'] ?? 1; // Cantidad (por defecto 1)
                    $precio = number_format($item['precio'], 2); // Precio con dos decimales
                    $subtotal = number_format($item['precio'] * $cantidad, 2); // Subtotal calculado
                ?>
                <tr>
                    <td><?php echo $nombre; ?></td> <!-- Muestro el nombre -->
                    <td>$<?php echo $precio; ?></td> <!-- Precio unitario -->
                    <td>
                        <form method="POST" action="carrito.php"> <!-- Form para actualizar cantidad -->
                            <input type="hidden" name="index" value="<?php echo $index; ?>"> <!-- Índice para saber qué producto actualizar -->
                            <input type="number" name="cantidad" value="<?php echo $cantidad; ?>" min="1" required> <!-- Input para cantidad -->
                            <button type="submit" name="actualizar">Actualizar</button> <!-- Botón para actualizar -->
                        </form>
                    </td>
                    <td>$<?php echo $subtotal; ?></td> <!-- Subtotal -->
                    <td>
                        <a href="carrito.php?eliminar=<?php echo $index; ?>" class="btn-eliminar">Eliminar</a> <!-- Link para eliminar producto -->
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p class="total"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p> <!-- Muestro total -->

        <p><a href="finalizar_compra.php" class="btn-pagar">Proceder al pago</a></p> <!-- Botón para ir a pagar -->

    <?php else: ?> <!-- Si el carrito está vacío -->
        <p>Tu carrito está vacío.</p> <!-- Mensaje de carrito vacío -->
    <?php endif; ?>

    <!-- Enlaces para seguir comprando o volver al inicio -->
    <p>
        <a href="catalogo.php">Seguir comprando</a> |
        <a href="inicio.php">Inicio</a>
    </p>

</body>
</html>
