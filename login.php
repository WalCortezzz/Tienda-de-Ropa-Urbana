<?php
session_start(); // Inicio la sesión para poder guardar datos del usuario

include("conexion.php"); // Conecto con la base de datos desde otro archivo

$mensaje = ''; // Aquí guardo los mensajes que quiero mostrarle al usuario
$mostrar_form = 'registro'; // Por defecto muestro el formulario de registro

// Verifico si el usuario envió el formulario (ya sea de registro o login)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Si se presionó el botón de registro
    if (isset($_POST['registro'])) {
        $nombre = trim($_POST['nombre']); // Quito espacios extras del nombre
        $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL); // Limpio el correo
        $contrasena = $_POST['contrasena']; // Guardo la contraseña
        $contrasena_confirm = $_POST['contrasena_confirm']; // Guardo la confirmación

        // Verifico que las dos contraseñas sean iguales
        if ($contrasena !== $contrasena_confirm) {
            $mensaje = "⚠️ Las contraseñas no coinciden.";
            $mostrar_form = 'registro';
        } else {
            // Me aseguro que el correo no esté repetido en la base de datos
            $sql_check = "SELECT id FROM Usuarios WHERE correo = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $correo);
            $stmt_check->execute();
            $stmt_check->store_result();

            // Si ya existe ese correo
            if ($stmt_check->num_rows > 0) {
                $mensaje = "⚠️ El correo ya está registrado.";
                $mostrar_form = 'registro';
            } else {
                // Encripto la contraseña para que quede segura
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);

                // Inserto el nuevo usuario en la base de datos
                $sql_insert = "INSERT INTO Usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sss", $nombre, $correo, $hash);

                // Si todo sale bien
                if ($stmt_insert->execute()) {
                    $mensaje = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
                    $mostrar_form = 'login'; // Cambio al formulario de login
                } else {
                    $mensaje = "❌ Error al registrar: " . $conn->error;
                    $mostrar_form = 'registro';
                }

                $stmt_insert->close(); // Cierro consulta de inserción
            }

            $stmt_check->close(); // Cierro consulta de verificación
        }

    } elseif (isset($_POST['login'])) {
        // Si se presionó el botón de iniciar sesión
        $correo = filter_var($_POST['correo_login'], FILTER_SANITIZE_EMAIL); // Limpio el correo
        $contrasena = $_POST['contrasena_login']; // Guardo la contraseña ingresada

        // Busco el usuario con ese correo
        $sql = "SELECT id, nombre, contrasena FROM Usuarios WHERE correo = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $stmt->store_result();

            // Si existe el usuario
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $nombre, $hash);
                $stmt->fetch();

                // Verifico que la contraseña ingresada sea correcta
                if (password_verify($contrasena, $hash)) {
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['nombre'] = $nombre;

                    header("Location: inicio.php"); // Redirijo a la página principal
                    exit;
                } else {
                    $mensaje = "⚠️ Contraseña incorrecta.";
                    $mostrar_form = 'login';
                }
            } else {
                $mensaje = "⚠️ Correo no registrado.";
                $mostrar_form = 'login';
            }

            $stmt->close(); // Cierro la consulta
        } else {
            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
            $mostrar_form = 'login';
        }
    }
}

$conn->close(); // Cierro la conexión con la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Registro e Inicio de Sesión - Tienda Urbana</title>
<style>
    /* Diseño general de la página */
    body {
        font-family: Arial, sans-serif;
        background: #f0f0f0;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    /* Cuadro que contiene los formularios */
    .container {
        background: white;
        padding: 30px 40px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        width: 350px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    /* Oculto los formularios por defecto */
    form {
        display: none;
        flex-direction: column;
    }

    /* Solo muestro el que tenga la clase "active" */
    form.active {
        display: flex;
    }

    label {
        margin-top: 10px;
        font-weight: bold;
        color: #555;
    }

    /* Estilo para los campos de texto */
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #bbb;
        border-radius: 4px;
        font-size: 14px;
    }

    /* Botón para enviar el formulario */
    input[type="submit"] {
        margin-top: 20px;
        background: #333;
        color: white;
        border: none;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
        background: #555;
    }

    /* Mensaje de error o éxito */
    .mensaje {
        margin: 15px 0;
        text-align: center;
        color: red;
        font-weight: bold;
    }

    /* Enlace para cambiar entre registro y login */
    .toggle-link {
        margin-top: 15px;
        text-align: center;
        cursor: pointer;
        color: #007BFF;
        text-decoration: underline;
        font-size: 14px;
    }

    .toggle-link:hover {
        color: #0056b3;
    }
</style>
</head>
<body>

<div class="container">
    <!-- Si hay mensaje, lo muestro aquí -->
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <!-- Formulario de registro -->
    <form id="registro-form" method="POST" class="<?php echo $mostrar_form === 'registro' ? 'active' : ''; ?>">
        <h2>Registro</h2>
        <label for="nombre">Nombre completo:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="correo">Correo electrónico:</label>
        <input type="email" name="correo" id="correo" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" id="contrasena" required>

        <label for="contrasena_confirm">Confirmar contraseña:</label>
        <input type="password" name="contrasena_confirm" id="contrasena_confirm" required>

        <input type="submit" name="registro" value="Registrarse">

        <!-- Enlace para cambiar al formulario de login -->
        <p class="toggle-link" onclick="toggleForms()">¿Ya tienes cuenta? Iniciar sesión</p>
    </form>

    <!-- Formulario de inicio de sesión -->
    <form id="login-form" method="POST" class="<?php echo $mostrar_form === 'login' ? 'active' : ''; ?>">
        <h2>Iniciar Sesión</h2>

        <label for="correo_login">Correo electrónico:</label>
        <input type="email" name="correo_login" id="correo_login" required>

        <label for="contrasena_login">Contraseña:</label>
        <input type="password" name="contrasena_login" id="contrasena_login" required>

        <input type="submit" name="login" value="Iniciar Sesión">

        <!-- Enlace para cambiar al formulario de registro -->
        <p class="toggle-link" onclick="toggleForms()">¿No tienes cuenta? Regístrate</p>
    </form>
</div>

<!-- Script para cambiar de formulario sin recargar la página -->
<script>
function toggleForms() {
    const regForm = document.getElementById('registro-form');
    const loginForm = document.getElementById('login-form');

    // Si se está mostrando el registro, muestro login
    if (regForm.classList.contains('active')) {
        regForm.classList.remove('active');
        loginForm.classList.add('active');
    } else {
        loginForm.classList.remove('active');
        regForm.classList.add('active');
    }
}
</script>

</body>
</html>
