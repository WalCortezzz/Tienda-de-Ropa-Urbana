<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar el producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: lista_de_productos.php"); // Redirigir después de la eliminación
    exit;
} else {
    echo "No se especificó el producto a eliminar.";
    exit;
}
?>
