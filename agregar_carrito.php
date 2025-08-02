<?php
// Iniciamos la sesión para manejar datos de usuario y carrito
session_start();

// Verificamos que el usuario esté logueado (tenga el id guardado en sesión)
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, redireccionamos a login.php
    header("Location: login.php");
    exit; // Detenemos ejecución después del redirect
}

// Obtenemos el carrito guardado en la sesión, o un arreglo vacío si no existe
$carrito = $_SESSION['carrito'] ?? [];

// Si el método de la petición es POST y vienen los datos necesarios para agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['precio'], $_POST['cantidad'])) {
    // Guardamos los datos enviados por POST
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);  // Convertimos precio a número decimal
    $cantidad = intval($_POST['cantidad']); // Convertimos cantidad a entero

    // Validamos que los datos sean válidos (nombre no vacío, precio y cantidad positivos)
    if ($nombre && $precio > 0 && $cantidad > 0) {
        $encontrado = false; // Flag para saber si el producto ya está en el carrito

        // Recorremos carrito para buscar si ya está el producto
        foreach ($carrito as &$item) {
            if ($item['nombre'] === $nombre) {
                // Si encontramos el producto, sumamos la cantidad
                $item['cantidad'] += $cantidad;
                $encontrado = true;
                break; // Salimos del loop porque ya actualizamos
            }
        }
        unset($item); // Para evitar referencias futuras no deseadas

        // Si no estaba el producto, lo agregamos nuevo al carrito
        if (!$encontrado) {
            $carrito[] = [
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => $cantidad,
            ];
        }

        // Guardamos el carrito actualizado en la sesión
        $_SESSION['carrito'] = $carrito;
    }

    // Redireccionamos a carrito.php para evitar reenvío de formulario al refrescar
    header("Location: carrito.php");
    exit;
}

// Manejo de eliminación de producto desde enlace GET (?eliminar=indice)
if (isset($_GET['eliminar'])) {
    // Tomamos el índice a eliminar (convertido a entero para seguridad)
    $eliminar_id = intval($_GET['eliminar']);

    // Eliminamos el producto en esa posición del carrito en sesión
    unset($_SESSION['carrito'][$eliminar_id]);

    // Reordenamos el arreglo para evitar "huecos" en índices
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);

    // Redireccionamos para refrescar la vista sin parámetro en URL
    header("Location: carrito.php");
    exit;
}

// Manejo de actualización de cantidad enviada por formulario POST
if (isset($_POST['actualizar'])) {
    // Tomamos índice del producto a actualizar
    $index = intval($_POST['index']);

    // Tomamos la nueva cantidad deseada
    $cantidad = intval($_POST['cantidad']);

    // Si cantidad es positiva, actualizamos la cantidad del producto
    if ($cantidad > 0) {
        $_SESSION['carrito'][$index]['cantidad'] = $cantidad;
    } else {
        // Si cantidad es 0 o menos, eliminamos el producto del carrito
        unset($_SESSION['carrito'][$index]);

        // Reordenamos el arreglo para eliminar huecos
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }

    // Redireccionamos para refrescar página y mostrar cambios
    header("Location: carrito.php");
    exit;
}

// Calculamos el total sumando precio * cantidad de cada producto en el carrito
$total = 0;
foreach ($carrito as $item) {
    $cantidad = $item['cantidad'] ?? 1; // Si no tiene cantidad, ponemos 1 por defecto
    $total += $item['precio'] * $cantidad; // Sumamos subtotal
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Para mostrar caracteres especiales correctamente -->
    <title>Carrito de Compras - Tienda Urbana</title> <!-- Título de la pestaña -->

    <style>
        /* Estilos básicos para que se vea agradable */
        body {
            background-color: #ADD8E6; /* Fondo azul claro */
            font-family: Arial, sans-serif; /* Fuente sans-serif */
            color: #000; /* Texto negro */
            margin: 0; /* Sin margen por defecto */
            padding: 40px; /* Espacio interior */
            text-align: center; /* Centrar texto */
        }
        h1 {
            font-size: 2.5em; /* Título grande */
            margin-bottom: 20px; /* Espacio debajo */
        }
        .carrito-table {
            margin: 0 auto; /* Centrar tabla horizontalmente */
            width: 90%; /* Ancho al 90% */
            max-width: 900px; /* Máximo ancho */
            border-collapse: collapse; /* Bordes colapsados */
            background-color: #fff; /* Fondo blanco */
        }
        .carrito-table th,
        .carrito-table td {
            border: 1px solid #ccc; /* Bordes grises */
            padding: 12px; /* Espacio dentro celdas */
        }
        .carrito-table th {
            background-color: #333; /* Fondo oscuro para encabezados */
            color: #fff; /* Texto blanco */
        }
        .carrito-table td form {
            display: flex; /* Para alinear horizontalmente input y botón */
            gap: 10px; /* Espacio entre elementos */
            justify-content: center; /* Centrar elementos */
        }
        input[type="number"] {
            width: 60px; /* Ancho fijo para cantidad */
            padding: 5px; /* Espacio interno */
        }
        .btn-eliminar,
        .btn-pagar,
        button {
            padding: 8px 16px; /* Relleno */
            margin: 8px; /* Margen */
            background-color: #000; /* Fondo negro */
            color: #fff; /* Texto blanco */
            text-decoration: none; /* Sin subrayado */
            border: none; /* Sin borde */
            border-radius: 4px; /* Bordes redondeados */
            cursor: pointer; /* Cursor pointer al pasar */
            transition: 0.3s; /* Transición suave */
        }
        .btn-eliminar:hover,
        .btn-pagar:hover,
        button:hover {
            background-color: #444; /* Color más claro al hover */
        }
        .total {
            font-size: 1.5em; /* Texto grande para total */
            margin-top: 20px; /* Espacio arriba */
        }
        p a {
            text-decoration: none; /* Sin subrayado */
            color: #000; /* Texto negro */
            margin: 0 10px; /* Margen horizontal */
        }
        p a:hover {
            text-decoration: underline; /* Subrayado al hover */
        }
    </style>
</head>
<body>

    <h1>Tu carrito de compras</h1> <!-- Título principal -->

    <?php if (count($carrito) > 0): ?> <!-- Si hay productos en carrito -->

        <!-- Tabla para listar productos -->
        <table class="carrito-table">
            <thead>
                <tr>
                    <th>Producto</th> <!-- Nombre del producto -->
                    <th>Precio Unitario</th> <!-- Precio por unidad -->
                    <th>Cantidad</th> <!-- Campo para cambiar cantidad -->
                    <th>Subtotal</th> <!-- Precio por cantidad -->
                    <th>Acción</th> <!-- Botón para eliminar -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $index => $item): ?>
                    <?php
                    // Sanitizar datos para mostrar seguro en HTML
                    $nombre = htmlspecialchars($item['nombre'] ?? 'Producto');
                    $cantidad = $item['cantidad'] ?? 1;
                    $precio = number_format($item['precio'], 2);
                    $subtotal = number_format($item['precio'] * $cantidad, 2);
                    ?>
                    <tr>
                        <td><?php echo $nombre; ?></td> <!-- Nombre -->
                        <td>$<?php echo $precio; ?></td> <!-- Precio unitario -->
                        <td>
                            <!-- Formulario para actualizar cantidad -->
                            <form method="POST" action="agregar_carrito.php">
                                <!-- Índice del producto para saber cuál actualizar -->
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <!-- Cantidad actual editable -->
                                <input type="number" name="cantidad" value="<?php echo $cantidad; ?>" min="1" required>
                                <!-- Botón actualizar -->
                                <button type="submit" name="actualizar">Actualizar</button>
                            </form>
                        </td>
                        <td>$<?php echo $subtotal; ?></td> <!-- Subtotal -->
                        <td>
                            <!-- Enlace para eliminar producto del carrito -->
                            <a href="agregar_carrito.php?eliminar=<?php echo $index; ?>" class="btn-eliminar">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Total a pagar -->
        <p class="total"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>

        <!-- Botón para proceder a pago o finalizar compra -->
        <p><a href="finalizar_compra.php" class="btn-pagar">Proceder al pago</a></p>

    <?php else: ?> <!-- Si carrito está vacío -->
        <p>Tu carrito está vacío.</p>
    <?php endif; ?>

    <!-- Enlaces para seguir comprando o volver al inicio -->
    <p>
        <a href="catalogo.php">Seguir comprando</a> |
        <a href="inicio.php">Inicio</a>
    </p>

</body>
</html>
