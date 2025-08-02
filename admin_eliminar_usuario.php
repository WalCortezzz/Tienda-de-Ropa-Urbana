<?php
// Empezamos la sesión para poder usar variables de sesión como el admin
session_start();

// Revisamos si el admin está logueado, si no lo está lo mandamos al login
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit(); // Salimos del script
}

// Verificamos si viene el ID del usuario por URL, si no viene, mostramos error
if (!isset($_GET['id'])) {
    die("ID de usuario no especificado.");
}

// Guardamos ese ID y nos aseguramos que sea un número entero por seguridad
$id_usuario = intval($_GET['id']);

// Conectamos a la base de datos con los datos que ya usamos en el resto del proyecto
$conexion = new mysqli("", "", "", "");

// Si falla la conexión, mostramos un error y detenemos todo
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// (Opcional) Si el admin intenta eliminarse a sí mismo, no lo dejamos
if (isset($_SESSION['admin_id']) && $id_usuario == $_SESSION['admin_id']) {
    die("No puedes eliminar tu propia cuenta.");
}

// Creamos la consulta para eliminar al usuario por su ID
$sql = "DELETE FROM Usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario); // 'i' indica que es un entero

// Ejecutamos la consulta
if ($stmt->execute()) {
    // Si se eliminó correctamente, cerramos todo y redirigimos al panel de usuarios
    $stmt->close();
    $conexion->close();
    header("Location: admin_usuarios.php?mensaje=usuario_eliminado");
    exit();
} else {
    // Si hubo un error al eliminar, lo mostramos
    die("Error al eliminar usuario: " . $conexion->error);
}
?>
