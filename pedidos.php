<?php
session_start(); // Iniciamos la sesión para poder usar variables como 'admin'

// Verificamos si el usuario está logueado como administrador
if (!isset($_SESSION['admin'])) {
    // Si no está logueado como admin, lo redirigimos al login de administrador
    header("Location: login_admin.php");
    exit(); // Terminamos la ejecución del script
}

// Conexión a la base de datos usando MySQLi
$conexion = new mysqli("", "", "", "");

// Verificamos si hubo un error al conectar
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error); // Si falla, detenemos todo y mostramos el error
}

// Consulta para obtener todos los pedidos y el nombre del usuario correspondiente
$sql = "SELECT p.id, p.id_usuario, p.cantidad, p.estado, p.fecha, u.nombre AS usuario_nombre 
        FROM pedidos p 
        INNER JOIN Usuarios u ON p.id_usuario = u.id";

// Ejecutamos la consulta
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html> <!-- Indicamos que es un documento HTML5 -->
<html lang="es"> <!-- Idioma en español -->
<head>
    <meta charset="UTF-8"> <!-- Configuración de caracteres -->
    <title>Pedidos - Tienda Urbana</title> <!-- Título de la pestaña -->
    <style>
        /* Estilos para la tabla */
        table {
            width: 100%; /* Ocupa todo el ancho */
            border-collapse: collapse; /* Une los bordes de las celdas */
            margin-top: 20px; /* Espacio arriba de la tabla */
        }
        table, th, td {
            border: 1px solid #ddd; /* Borde gris claro */
        }
        th, td {
            padding: 10px; /* Espaciado interno */
            text-align: center; /* Centra el texto */
        }
        th {
            background-color: #f2f2f2; /* Fondo gris claro para encabezados */
        }
        /* Estilo para el botón de regresar */
        .btn-regresar {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #007BFF; /* Azul */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-regresar:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h1>Pedidos del Administrador</h1> <!-- Título principal en la página -->

<!-- Enlace para regresar al dashboard -->
<a href="dashboard.php" class="btn-regresar">← Volver al panel</a>

<?php
// Verificamos si hay resultados
if ($resultado->num_rows > 0) {
    // Si hay pedidos, empezamos a generar la tabla
    echo "<table>";
    echo "<tr><th>ID Pedido</th><th>Usuario</th><th>Cantidad</th><th>Estado</th><th>Fecha</th></tr>";

    // Recorremos cada fila de resultados
    while ($pedido = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $pedido['id'] . "</td>"; // ID del pedido
        echo "<td>" . $pedido['usuario_nombre'] . "</td>"; // Nombre del usuario
        echo "<td>" . $pedido['cantidad'] . "</td>"; // Cantidad de productos
        echo "<td>" . $pedido['estado'] . "</td>"; // Estado del pedido
        echo "<td>" . $pedido['fecha'] . "</td>"; // Fecha del pedido
        echo "</tr>";
    }

    echo "</table>"; // Cerramos la tabla
} else {
    // Si no hay pedidos, mostramos un mensaje
    echo "<p>No hay pedidos registrados.</p>";
}

// Cerramos la conexión a la base de datos
$conexion->close();
?>

</body>
</html> <!-- Fin del documento HTML -->
