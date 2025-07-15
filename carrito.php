<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];

if (isset($_GET['eliminar'])) {
    $eliminar_id = intval($_GET['eliminar']);
    unset($_SESSION['carrito'][$eliminar_id]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

if (isset($_POST['actualizar'])) {
    $index = intval($_POST['index']);
    $cantidad = intval($_POST['cantidad']);
    
    if ($cantidad > 0) {
        $_SESSION['carrito'][$index]['cantidad'] = $cantidad;
    } else {
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }
    header("Location: carrito.php");
    exit;
}

$total = 0;
foreach ($carrito as $item) {
    $cantidad = $item['cantidad'] ?? 1;
    $total += $item['precio'] * $cantidad;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras - Tienda Urbana</title>
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

        .carrito-table {
            margin: 0 auto;
            width: 90%;
            max-width: 900px;
            border-collapse: collapse;
            background-color: #fff;
        }

        .carrito-table th,
        .carrito-table td {
            border: 1px solid #ccc;
            padding: 12px;
        }

        .carrito-table th {
            background-color: #333;
            color: #fff;
        }

        .carrito-table td form {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
        }

        .btn-eliminar,
        .btn-pagar,
        button {
            padding: 8px 16px;
            margin: 8px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-eliminar:hover,
        .btn-pagar:hover,
        button:hover {
            background-color: #444;
        }

        .total {
            font-size: 1.5em;
            margin-top: 20px;
        }

        p a {
            text-decoration: none;
            color: #000;
            margin: 0 10px;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Tu carrito de compras</h1>

    <?php if (count($carrito) > 0): ?>
        <table class="carrito-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($carrito as $index => $item): ?>
                <?php 
                    $nombre = htmlspecialchars($item['nombre'] ?? 'Producto');
                    $cantidad = $item['cantidad'] ?? 1;
                    $precio = number_format($item['precio'], 2);
                    $subtotal = number_format($item['precio'] * $cantidad, 2);
                ?>
                <tr>
                    <td><?php echo $nombre; ?></td>
                    <td>$<?php echo $precio; ?></td>
                    <td>
                        <form method="POST" action="carrito.php">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <input type="number" name="cantidad" value="<?php echo $cantidad; ?>" min="1" required>
                            <button type="submit" name="actualizar">Actualizar</button>
                        </form>
                    </td>
                    <td>$<?php echo $subtotal; ?></td>
                    <td><a href="carrito.php?eliminar=<?php echo $index; ?>" class="btn-eliminar">Eliminar</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p class="total"><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
        <p><a href="finalizar_compra.php" class="btn-pagar">Proceder al pago</a></p>
    <?php else: ?>
        <p>Tu carrito está vacío.</p>
    <?php endif; ?>

    <p><a href="catalogo.php">Seguir comprando</a> | <a href="inicio.php">Inicio</a></p>

</body>
</html>
