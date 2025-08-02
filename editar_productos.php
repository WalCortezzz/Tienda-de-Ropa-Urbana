<?php
include("conexion.php"); // Incluyo el archivo que conecta con la base de datos

if (isset($_GET['id'])) { // Checo si me llegó el ID del producto para editar
    $id = $_GET['id'];

    // Preparo la consulta para sacar los datos del producto con ese ID
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id); // Le paso el ID como entero
    $stmt->execute(); // Ejecuto la consulta
    $resultado = $stmt->get_result(); // Guardo el resultado
    $producto = $resultado->fetch_assoc(); // Lo convierto en array para usarlo fácil

    // Si el formulario se envió (cuando le das guardar)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Agarro los datos que me mandó el admin para actualizar
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $categoria = $_POST['categoria'];
        $imagen = $_POST['imagen'];

        // Actualizo el producto con los nuevos datos, sin tocar la columna stock
        $stmt_update = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, imagen = ? WHERE id = ?");
        $stmt_update->bind_param("sssssi", $nombre, $descripcion, $precio, $categoria, $imagen, $id);
        $stmt_update->execute();

        // Después de actualizar, regreso a la lista de productos
        header("Location: lista_de_productos.php");
        exit;
    }
} else {
    // Si no me mandaron ID, aviso que no encontré el producto y paro
    echo "Producto no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
</head>
<body>
    <h2>Editar Producto</h2>

    <!-- Formulario para editar el producto -->
    <form method="POST">
        <label for="nombre">Nombre:</label><br>
        <!-- Muestro el nombre actual para que el admin lo pueda cambiar -->
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>"><br><br>

        <label for="descripcion">Descripción:</label><br>
        <!-- Campo grande para la descripción, igual con el valor actual -->
        <textarea name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea><br><br>

        <label for="precio">Precio:</label><br>
        <input type="text" name="precio" value="<?php echo $producto['precio']; ?>"><br><br>

        <label for="categoria">Categoría:</label><br>
        <input type="text" name="categoria" value="<?php echo $producto['categoria']; ?>"><br><br>

        <label for="imagen">Imagen:</label><br>
        <input type="text" name="imagen" value="<?php echo $producto['imagen']; ?>"><br><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
