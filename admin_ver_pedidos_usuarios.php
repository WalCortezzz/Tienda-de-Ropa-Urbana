<?php
// Aquí prendo todos los errores para ver si algo anda mal, así me entero rápido
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Inicio sesión pa’ manejar al admin que esté entrando

// Si no hay admin logueado, lo mando directo al login de admin, no hay de otra
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Aquí checo que me hayan pasado el ID del usuario, si no, paro todo y aviso
if (!isset($_GET['usuario_id'])) {
    die("No me dijiste qué usuario quieres ver.");
}

// Guardo el ID que me pasaron, y lo aseguro para que no metan cosas raras
$usuario_id = intval($_GET['usuario_id']);

// Me conecto a la base de datos, ya sabes, con los datos de siempre
$conexion = new mysqli("", "", "", "");

// Si la conexión falla, corto todo y muestro qué pasó
if ($conexion->connect_error) {
    die("No pude conectar a la base de datos: " . $conexion->connect_error);
}

// Ahora agarro el nombre del usuario pa’ mostrarlo en la página
$usuario_result = $conexion->query("SELECT nombre FROM Usuarios WHERE id = $usuario_id");

// Si no encuentro al usuario, ya valió, así que paro todo
if ($usuario_result->num_rows === 0) {
    die("Ese usuario no existe.");
}

// Guardo la info del usuario para usarla después
$usuario = $usuario_result->fetch_assoc();

// Ahora saco todos los pedidos que hizo ese usuario, del más nuevo al más viejo
$pedidos_result = $conexion->query("SELECT producto, cantidad, estado, fecha FROM pedidos WHERE id_usuario = $usuario_id ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- Pongo el nombre del usuario en el título pa’ que se vea chido -->
    <title>Pedidos de <?php echo htmlspecialchars($usuario['nombre']); ?></title> 
    <style>
        /* Aquí le doy estilo para que se vea bonito y ordenado */
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f5;
            max-width: 900px;
            margin: auto;
            padding: 40px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        .volver {
            text-align: center;
            margin-top: 30px;
        }
        .volver a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }
        .volver a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Aquí va el título, con el nombre del usuario bien visible -->
    <h1>Pedidos de <?php echo htmlspecialchars($usuario['nombre']); ?></h1>

    <?php if ($pedidos_result->num_rows > 0): ?>
        <!-- Si tiene pedidos, los pongo en una tabla pa’ que se vean chidos -->
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <!-- Recorro cada pedido y lo muestro en la tabla -->
                <?php while ($pedido = $pedidos_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['producto']); ?></td>
                        <td><?php echo $pedido['cantidad']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                        <td><?php echo $pedido['fecha']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Si no tiene pedidos, le digo que aún no ha comprado nada -->
        <p style="text-align: center;">Este usuario no ha hecho ningún pedido.</p>
    <?php endif; ?>

    <!-- Botón pa’ regresar a la lista de usuarios, porque uno se puede perder -->
    <div class="volver">
        <p><a href="admin_usuarios.php">⬅️ Volver a la lista de usuarios</a></p>
    </div>

</body>
</html>
