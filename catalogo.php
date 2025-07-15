<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include("conexion.php");

// Obtener todas las categorías distintas para mostrar
$sql_cat = "SELECT DISTINCT categoria FROM productos";
$result_cat = $conn->query($sql_cat);

// Obtener categoría seleccionada
$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$categorias_info = [
    "Mujer" => "https://i.pinimg.com/564x/46/17/d1/4617d1746549f56ae7afcc5585e2b6b6.jpg",
    "Hombre" => "https://as1.ftcdn.net/jpg/05/68/92/20/1000_F_568922003_R0ijRlYtRx5RZwRwOJn0CiyRVShg3w7L.jpg",
    "Ninos" => "https://img.freepik.com/foto-gratis/ninos-tiro-completo-posando-juntos_23-2149853383.jpg?semt=ais_hybrid&w=740",
    "Accesorios" => "https://cdn.shopify.com/s/files/1/1047/2058/articles/5-accesorios-de-moda-basicos-para-tu-guardarropa.jpg?v=1668642202"
];

if ($categoria_seleccionada) {
    $stmt = $conn->prepare("SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE categoria = ?");
    $stmt->bind_param("s", $categoria_seleccionada);
    $stmt->execute();
    $result_prod = $stmt->get_result();
} else {
    $result_prod = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - Tienda Urbana</title>
    <style>
        body {
            background-color: #ADD8E6;
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        .categorias, .productos {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .categoria {
            background-color: #fff;
            border-radius: 8px;
            width: 180px;
            text-align: center;
            border: 1px solid #000;
            padding: 10px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .categoria:hover {
            transform: scale(1.05);
        }

        .categoria img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
        }

        .categoria a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        .producto {
            background-color: #ffffffb0;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            width: 250px;
            text-align: center;
        }

        .producto img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        form {
            margin-top: 10px;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
        }

        button {
            background: none;
            border: 1px solid #000;
            padding: 6px 12px;
            cursor: pointer;
        }

        button:hover {
            background-color: #000;
            color: #fff;
        }

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
    <h2>Categorías</h2>
    <div class="categorias">
        <?php
        foreach ($categorias_info as $cat => $img_url) {
            echo "<div class='categoria'>";
            echo "<a href='catalogo.php?categoria=" . urlencode($cat) . "'>";
            echo "<img src='$img_url' alt='$cat'>";
            echo "<span>$cat</span>";
            echo "</a>";
            echo "</div>";
        }
        ?>
    </div>

    <p style="text-align: center;">Por favor selecciona una categoría para ver los productos.</p>
<?php else: ?>
    <h2>Productos en categoría: <?php echo htmlspecialchars($categoria_seleccionada); ?></h2>

    <?php if ($result_prod && $result_prod->num_rows > 0): ?>
        <div class="productos">
            <?php while ($prod = $result_prod->fetch_assoc()): ?>
                <div class="producto">
                    <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong><br>
                    <img src="<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>"><br>
                    <p><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($prod['precio'], 2); ?></p>

                    <form method="POST" action="agregar_carrito.php">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($prod['precio']); ?>">
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="1" min="1" required>
                        <button type="submit">Agregar al carrito 🛒</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="navegacion">
            <a href="catalogo.php">🔁 Ver todas las categorías</a>
        </div>
    <?php else: ?>
        <p style="text-align: center;">No hay productos en esta categoría.</p>
    <?php endif; ?>
<?php endif; ?>

<div class="navegacion">
    <a href="inicio.php">🏠 Inicio</a> |
    <a href="logout.php">Cerrar sesión</a> |
    <a href="carrito.php">🛒 Ver carrito</a>
</div>

</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
