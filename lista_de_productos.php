<?php
session_start(); // Arranco la sesión para manejar todo lo del admin

// Checo si el admin está logueado, si no, lo mando al login de admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Me conecto a la base de datos con los datos de siempre
$conexion = new mysqli("", "", "", "");

// Si no se conecta, muestro error y paro todo
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Traigo todos los productos para mostrarlos después
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <style>
        /* Aquí le doy estilo pa’ que se vea bien la página y los productos */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .productos {
            display: flex;
            flex-wrap: wrap;      /* Para que se acomoden en filas y no se rompa */
            justify-content: center; /* Que estén centrados */
            gap: 20px;            /* Espacio entre productos */
        }

        .producto {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            width: 250px;          /* Cada producto ocupa un tamaño fijo */
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra chida */
        }

        .producto img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        a {
            color: #000;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            background-color: #4CAF50;  /* Botón verde */
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #45a049; /* Verde más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>

    <!-- Botón para regresar al panel principal del admin -->
    <div style="text-align: center;">
        <a href="dashboard.php" class="btn">🏠 Regresar al Panel de Administración</a>
    </div>

    <h1>Lista de Productos</h1>

    <div class="productos">
        <?php
        // Aquí hago un ciclo para mostrar todos los productos que traje de la base
        while ($row = $resultado->fetch_assoc()) {
            echo "<div class='producto'>";
            // Muestro el nombre y lo limpio por seguridad con htmlspecialchars
            echo "<h3>" . htmlspecialchars($row['nombre']) . "</h3>";
            // La descripción igual la limpio para que no meta cosas raras
            echo "<p>" . htmlspecialchars($row['descripcion']) . "</p>";
            // Muestro el precio y categoría sin formato especial
            echo "<strong>Precio:</strong> $" . $row['precio'] . "<br>";
            echo "<strong>Categoría:</strong> " . $row['categoria'] . "<br>";

            // Si el producto tiene imagen, la muestro con un ancho fijo
            if ($row['imagen']) {
                echo "<img src='" . $row['imagen'] . "' width='150'><br>";
            }

            // Aquí dejo enlaces para que el admin pueda editar o borrar el producto
            // En eliminar pongo un confirm para que no borre sin querer
            echo "<a href='editar_productos.php?id=" . $row['id'] . "'>Editar</a> | ";
            echo "<a href='eliminar_producto.php?id=" . $row['id'] . "' onclick=\"return confirm('¿Eliminar este producto?')\">Eliminar</a>";
            echo "</div>";
        }
        ?>
    </div>

    <br>
    <!-- Link para ir a agregar un producto nuevo -->
    <a href="agregar_productos.php">Agregar Producto</a>

</body>
</html>
