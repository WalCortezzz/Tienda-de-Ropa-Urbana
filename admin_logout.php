<?php
// agregar comentarios para saber que hace cada cosa
session_start(); 
session_destroy(); // esto hace que quite o destruye la seccion que el usuario creo
header("Location: admin_login.php"); // esto te manda despues de cerrar la sesion a admin_login que es donde ingresa el admin
exit();
?>
