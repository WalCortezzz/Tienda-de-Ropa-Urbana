<?php
// Arrancamos sesión para manejar la sesión del usuario
session_start();

// Si no hay usuario logueado (no hay 'usuario_id' en sesión), lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Redirige a login.php
    exit; // Detiene ejecución para que no siga el script
}

// Incluimos el archivo de conexión a la base de datos
include("conexion.php");

// Primero sacamos todas las categorías distintas que hay en la tabla 'productos'
// Esto nos sirve para mostrar las categorías disponibles al usuario
$sql_cat = "SELECT DISTINCT categoria FROM productos";
$result_cat = $conn->query($sql_cat);

// Ahora vemos si el usuario ya eligió alguna categoría por GET (ej: catalogo.php?categoria=Mujer)
$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Arreglo con info extra: para cada categoría le asignamos una imagen para mostrar en pantalla
$categorias_info = [
    "Mujer" => "https://i.pinimg.com/564x/46/17/d1/4617d1746549f56ae7afcc5585e2b6b6.jpg",
    "Hombre" => "https://as1.ftcdn.net/jpg/05/68/92/20/1000_F_568922003_R0ijRlYtRx5RZwRwOJn0CiyRVShg3w7L.jpg",
    "Ninos" => "https://img.freepik.com/foto-gratis/ninos-tiro-completo-posando-juntos_23-2149853383.jpg?semt=ais_hybrid&w=740",
    "Accesorios" => "https://cdn.shopify.com/s/files/1/1047/2058/articles/5-accesorios-de-moda-basicos-para-tu-guardarropa.jpg?v=1668642202"
];

// Si hay categoría seleccionada, hacemos consulta para sacar los productos de esa categoría
if ($categoria_seleccionada) {
    // Preparamos consulta segura con "prepare" para evitar inyección SQL
    $stmt = $conn->prepare("SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE categoria = ?");
    $stmt->bind_param("s", $categoria_seleccionada); // Le pasamos la categoría como parámetro
    $stmt->execute(); // Ejecutamos la consulta
    $result_prod = $stmt->get_result(); // Guardamos el resultado
} else {
    // Si no hay categoría, no mostramos productos
    $result_prod = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Para mostrar acentos y caracteres especiales -->
    <title>Catálogo - Tienda Urbana</title>

    <style>
        /* Estilos para que la página se vea limpia y ordenada */
        body {
            background-color: #ADD8E6; /* Fondo azul clarito */
            font-family: Arial, sans-serif; /* Fuente clara y legible */
            color: #000; /* Texto negro */
            margin: 0; /* Sin margen externo */
            padding: 20px; /* Espacio interno */
        }

        h1, h2 {
            text-align: center; /* Titulos centrados */
        }

        /* Contenedores flexibles para categorías y productos */
        .categorias, .productos {
            display: flex;
            flex-wrap: wrap; /* Que se ajusten en varias filas */
            justify-content: center; /* Centramos horizontalmente */
            gap: 20px; /* Separación entre items */
            margin-top: 20px;
        }

        /* Cada tarjeta de categoría */
        .categoria {
            background-color: #fff; /* Fondo blanco */
            border-radius: 8px; /* Bordes redondeados */
            width: 180px; /* Ancho fijo */
            text-align: center; /* Texto centrado */
            border: 1px solid #000; /* Borde negro */
            padding: 10px;
            cursor: pointer; /* Cursor puntero al pasar */
            transition: transform 0.2s; /* Animación suave al hover */
        }

        .categoria:hover {
            transform: scale(1.05); /* Agranda un poco al pasar el mouse */
        }

        /* Imagen dentro de cada categoría */
        .categoria img {
            width: 100%; /* Ocupa todo el ancho */
            height: 140px; /* Alto fijo */
            object-fit: cover; /* Para que la imagen se ajuste bien */
            border-radius: 8px; /* Bordes redondeados */
        }

        /* Link para categoría */
        .categoria a {
            text-decoration: none; /* Sin subrayado */
            color: #000; /* Negro */
            font-weight: bold; /* Negrita */
            display: block;
            margin-top: 10px;
        }

        /* Tarjeta para cada producto */
        .producto {
            background-color: #ffffffb0; /* Blanco semi-transparente */
            border: 1px solid #ccc; /* Borde gris suave */
            border-radius: 8px;
            padding: 15px;
            width: 250px;
            text-align: center;
        }

        .producto img {
            max-width: 100%; /* Que no se pase del contenedor */
            height: auto;
            border-radius: 5px;
        }

        /* Formulario para cantidad y botón */
        form {
            margin-top: 10px;
        }

        input[type="number"] {
            width: 60px; /* Campo pequeño para número */
            padding: 5px;
        }

        button {
            background: none;
            border: 1px solid #000;
            padding: 6px 12px;
            cursor: pointer;
        }

        button:hover {
            background-color: #000; /* Fondo negro */
            color: #fff; /* Texto blanco */
        }

        /* Navegación de links al pie */
        .navegacion {
            text-align: center;
            margin-top: 40px;
        }

        .navegacion a {
            margin: 0 10px;
            color: #000;
            text-decoration: none;
        }

        .navegacion a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Catálogo de Ropa Urbana</h1>

<?php if (!$categoria_seleccionada): ?>
    <!-- Si no eligió categoría mostramos las categorías disponibles -->
    <h2>Categorías</h2>
    <div class="categorias">
        <?php
        // Recorremos el arreglo de categorías para mostrar cada una con su imagen y link
        foreach ($categorias_info as $cat => $img_url) {
            echo "<div class='categoria'>";
            // Link que lleva a esta misma página con la categoría seleccionada por GET
            echo "<a href='catalogo.php?categoria=" . urlencode($cat) . "'>";
            echo "<img src='$img_url' alt='$cat'>"; // Imagen categoría
            echo "<span>$cat</span>"; // Nombre categoría
            echo "</a>";
            echo "</div>";
        }
        ?>
    </div>

    <p style="text-align: center;">Por favor selecciona una categoría para ver los productos.</p>
<?php else: ?>
    <!-- Si ya eligió categoría mostramos los productos que haya en esa categoría -->
    <h2>Productos en categoría: <?php echo htmlspecialchars($categoria_seleccionada); ?></h2>

    <?php if ($result_prod && $result_prod->num_rows > 0): ?>
        <!-- Si hay productos, mostramos cada uno en su caja -->
        <div class="productos">
            <?php while ($prod = $result_prod->fetch_assoc()): ?>
                <div class="producto">
                    <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong><br>
                    <img src="<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>"><br>
                    <p><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($prod['precio'], 2); ?></p>

                    <!-- Formulario para agregar producto al carrito -->
                    <form method="POST" action="agregar_carrito.php">
                        <!-- Mandamos el nombre y precio ocultos -->
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($prod['precio']); ?>">
                        <!-- Cantidad que quiere agregar -->
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="1" min="1" required>
                        <!-- Botón para agregar al carrito -->
                        <button type="submit">Agregar al carrito 🛒</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Link para volver a la vista de categorías -->
        <div class="navegacion">
            <a href="catalogo.php">🔁 Ver todas las categorías</a>
        </div>
    <?php else: ?>
        <!-- Si no hay productos en esa categoría -->
        <p style="text-align: center;">No hay productos en esta categoría.</p>
    <?php endif; ?>
<?php endif; ?>

<!-- Enlaces útiles para el usuario en el pie -->
<div class="navegacion">
    <a href="inicio.php">🏠 Inicio</a> |
    <a href="logout.php">Cerrar sesión</a> |
    <a href="carrito.php">🛒 Ver carrito</a>
</div>

</body>
</html>

<?php
// Cerramos el statement y la conexión para liberar recursos
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
