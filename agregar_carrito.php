<?php
session_start();

// Validar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Si no existe el carrito, lo inicializamos
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Recoger los datos del formulario
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;  // Usamos `intval` para asegurarnos de que sea un número
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];

// Validamos que el producto tiene tanto nombre como precio
if (empty($nombre) || empty($precio)) {
    echo "Error: el producto no tiene nombre o precio válido.";
    exit;
}

// Buscar si el producto ya está en el carrito
$producto_encontrado = false;
foreach ($_SESSION['carrito'] as &$item) {
    if ($item['nombre'] === $nombre) {
        // Si ya está en el carrito, solo incrementamos la cantidad
        $item['cantidad'] += $cantidad;
        $producto_encontrado = true;
        break;
    }
}

// Si el producto no está en el carrito, lo agregamos
if (!$producto_encontrado) {
    $_SESSION['carrito'][] = [
        'nombre' => $nombre,
        'precio' => $precio,
        'cantidad' => $cantidad  // Aseguramos que la cantidad se agregue correctamente
    ];
}

// Redirigir al carrito
header("Location: carrito.php");
exit;
?>
