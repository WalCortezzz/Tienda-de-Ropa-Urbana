<?php
session_start();
include("conexion.php");

$mensaje = '';
$mostrar_form = 'registro'; // Por defecto mostramos registro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['registro'])) {
        // Proceso registro
        $nombre = trim($_POST['nombre']);
        $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
        $contrasena = $_POST['contrasena'];
        $contrasena_confirm = $_POST['contrasena_confirm'];

        if ($contrasena !== $contrasena_confirm) {
            $mensaje = "⚠️ Las contraseñas no coinciden.";
            $mostrar_form = 'registro';
        } else {
            // Validar correo no exista ya
            $sql_check = "SELECT id FROM Usuarios WHERE correo = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $correo);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $mensaje = "⚠️ El correo ya está registrado.";
                $mostrar_form = 'registro';
            } else {
                // Insertar usuario
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO Usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sss", $nombre, $correo, $hash);
                if ($stmt_insert->execute()) {
                    $mensaje = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
                    $mostrar_form = 'login'; // Cambiamos para mostrar login
                } else {
                    $mensaje = "❌ Error al registrar: " . $conn->error;
                    $mostrar_form = 'registro';
                }
                $stmt_insert->close();
            }
            $stmt_check->close();
        }

    } elseif (isset($_POST['login'])) {
        // Proceso login
        $correo = filter_var($_POST['correo_login'], FILTER_SANITIZE_EMAIL);
        $contrasena = $_POST['contrasena_login'];

        $sql = "SELECT id, nombre, contrasena FROM Usuarios WHERE correo = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $nombre, $hash);
                $stmt->fetch();

                if (password_verify($contrasena, $hash)) {
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['nombre'] = $nombre;

                    header("Location: inicio.php");
                    exit;
                } else {
                    $mensaje = "⚠️ Contraseña incorrecta.";
                    $mostrar_form = 'login';
                }
            } else {
                $mensaje = "⚠️ Correo no registrado.";
                $mostrar_form = 'login';
            }

            $stmt->close();
        } else {
            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
            $mostrar_form = 'login';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Registro e Inicio de Sesión - Tienda Urbana</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f0f0;
        margin: 0; padding: 0;
        display: flex; justify-content: center; align-items: center;
        height: 100vh;
    }
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
    form {
        display: none;
        flex-direction: column;
    }
    form.active {
        display: flex;
    }
    label {
        margin-top: 10px;
        font-weight: bold;
        color: #555;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 8px;
        margin-top: 5px;
        border: 1px solid #bbb;
        border-radius: 4px;
        font-size: 14px;
    }
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
    .mensaje {
        margin: 15px 0;
        text-align: center;
        color: red;
        font-weight: bold;
    }
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
    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

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

        <p class="toggle-link" onclick="toggleForms()">¿Ya tienes cuenta? Iniciar sesión</p>
    </form>

    <form id="login-form" method="POST" class="<?php echo $mostrar_form === 'login' ? 'active' : ''; ?>">
        <h2>Iniciar Sesión</h2>

        <label for="correo_login">Correo electrónico:</label>
        <input type="email" name="correo_login" id="correo_login" required>

        <label for="contrasena_login">Contraseña:</label>
        <input type="password" name="contrasena_login" id="contrasena_login" required>

        <input type="submit" name="login" value="Iniciar Sesión">

        <p class="toggle-link" onclick="toggleForms()">¿No tienes cuenta? Regístrate</p>
    </form>
</div>

<script>
function toggleForms() {
    const regForm = document.getElementById('registro-form');
    const loginForm = document.getElementById('login-form');
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
