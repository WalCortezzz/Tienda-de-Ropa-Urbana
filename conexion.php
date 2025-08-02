<?php
// Datos para conectarse a la base de datos (los que da InfinityFree)
$servername = "";  // Servidor de la base de datos
$username = "";               // Usuario de la base de datos
$password = "";            // Contraseña del usuario
$dbname = "";          // Nombre de la base de datos

// Creamos la conexión usando MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificamos si la conexión falló, y si es así, detenemos todo y mostramos el error
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
