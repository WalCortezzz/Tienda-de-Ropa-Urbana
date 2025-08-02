<?php
session_start(); // Arrancamos la sesión para poder guardar datos mientras navegas

if ($_SERVER["REQUEST_METHOD"] === "POST") { // Solo hacemos esto cuando envías el formulario
    // Nos conectamos a la base de datos
    $conexion = new mysqli("", "", "", "");

    // Guardamos lo que escribiste en los inputs
    $usuario = $_POST['nombreadmin'];
    $contrasena = $_POST['contrasena'];

    // Buscamos en la tabla Admin si existe ese usuario que escribiste
    $sql = "SELECT * FROM Admin WHERE nombreadmin = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario); // Evitamos broncas de seguridad con esta forma de pasar datos
    $stmt->execute();
    $resultado = $stmt->get_result(); // Traemos los datos que encontró la consulta

    // Si encontró justo uno con ese nombre de admin
    if ($resultado->num_rows === 1) {
        $admin = $resultado->fetch_assoc(); // Guardamos sus datos en una variable

        // Aquí verificamos que la contraseña que pusiste coincida con la que está en la base (que está cifrada)
        if (password_verify($contrasena, $admin['contrasena'])) {
            $_SESSION['admin'] = $admin['nombreadmin']; // Guardamos el nombre en sesión para que quede logueado
            header("Location: dashboard.php"); // Lo mandamos directo al panel de admin
            exit(); // Paramos el script para que no haga más cosas
        } else {
            $error = "Contraseña incorrecta."; // Si no coincide la contraseña, avisamos
        }
    } else {
        $error = "Administrador no encontrado."; // Si no existe ese usuario, también avisamos
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Administrador</title>
</head>
<body>
    <h2>Iniciar sesión como administrador</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?> <!-- Si hay error, lo mostramos en rojo -->
    <form method="POST">
        <label>Usuario:</label>
        <input type="text" name="nombreadmin" required><br> <!-- Aquí pones tu usuario -->
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required><br> <!-- Aquí tu contraseña -->
        <button type="submit">Entrar</button> <!-- Botón para enviar todo -->
    </form>
</body>
</html>
