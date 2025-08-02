<?php
session_start();

// Verifica que el admin esté logueado, si no, lo manda al login
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Conexión directa a la base de datos
$conexion = new mysqli("", "", "", "");

// Si la conexión falla, muestra error y termina
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta para obtener todos los usuarios con su id, nombre y correo
$sql = "SELECT id, nombre, correo FROM Usuarios";
$result = $conexion->query($sql);

// Si la consulta falla, muestra error y termina
if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f5;
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 30px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #444;
            color: white;
        }
        a {
            text-decoration: none;
            color: #0077cc;
        }
        a:hover {
            text-decoration: underline;
        }
        .volver {
            margin-top: 20px;
            text-align: center;
        }
        .volver a {
            color: #000;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Usuarios Registrados</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($user['correo']); ?></td>
                    <td>
                        <!-- Link para ver los pedidos del usuario -->
                        <a href="admin_ver_pedidos_usuarios.php?usuario_id=<?php echo $user['id']; ?>">📦 Ver Pedidos</a> |
                        <!-- Link para eliminar usuario con confirmación -->
                        <a href="admin_eliminar_usuario.php?id=<?php echo $user['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">❌ Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="volver">
        <!-- Link para volver al panel de administración -->
        <p><a href="dashboard.php">⬅️ Volver al panel</a></p>
    </div>

</body>
</html>
