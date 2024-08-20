<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['last_activity'] = time();
        header("Location: index.php");
        exit();
    } else {
        $error = "Nombre de usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    /* Ajustes generales para el body */
    body {
        font-family: 'Raleway', sans-serif;
    }

    /* Estilo para el contenedor principal */
    .container {
        max-width: 100%;
        padding: 20px;
    }

    /* Estilo para la tarjeta de inicio de sesión */
    .card {
        border-radius: 10px;
    }

    /* Estilo para los botones */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    /* Estilo para el enlace de registro */
    .psw a {
        color: #007bff;
    }

    .psw a:hover {
        text-decoration: underline;
    }
</style>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card p-4 shadow-lg w-100" style="max-width: 400px;">
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Correo Electrónico</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                </div>
                <div class="text-center">
                    <span class="psw">¿No tienes cuenta? <a href="registro.php">Registrarte</a></span>
                </div>
                <div class="text-center">
                    <span class="psw">Regresar al <a href="index.php">Inicio</a></span>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
