<?php
// Prendo los errores para ver todo lo que pueda salir mal, por si ando depurando
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Empiezo la sesión pa’ controlar que sólo admin pueda entrar

// Si no hay admin en sesión, lo mando al login para que se identifique
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Si la petición es POST (o sea, que enviaron el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Me conecto a la base de datos con los datos de siempre
    $conexion = new mysqli("", "", "", "");

    // Si no conecta, paro todo y aviso
    if ($conexion->connect_error) {
        die("No pude conectar a la base de datos: " . $conexion->connect_error);
    }

    // Aquí agarro lo que me mandaron desde el formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $imagen_url = $_POST['imagen']; // Esto es el link a la imagen, lo tomo tal cual

    // Preparo la consulta para insertar un producto nuevo
    $sql = "INSERT INTO productos (nombre, precio, descripcion, categoria, imagen) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    // Aquí uno las variables con los signos ? para evitar problemas de seguridad
    $stmt->bind_param("sssss", $nombre, $precio, $descripcion, $categoria, $imagen_url);
    $stmt->execute(); // Lo mando a la base de datos

    // Ya que terminó, lo mando a la página donde se ven todos los productos
    header("Location: lista_de_productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
</head>
<body>
    <h1>Agregar Producto</h1>

    <!-- Formulario para meter datos del producto nuevo -->
    <form method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label for="precio">Precio:</label><br>
        <input type="text" name="precio" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <input type="text" name="descripcion" required><br><br>

        <label for="categoria">Categoría:</label><br>
        <input type="text" name="categoria" required><br><br>

        <label for="imagen">URL de la Imagen:</label><br>
        <input type="text" name="imagen" placeholder="https://ejemplo.com/imagen.jpg"><br><br>

        <button type="submit">Agregar Producto</button>
    </form>

    <br>
    <!-- Link pa’ volver a la lista de productos si no quieres agregar nada -->
    <a href="lista_de_productos.php">Volver a la lista de productos</a>
</body>
</html>
