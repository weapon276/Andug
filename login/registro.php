<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>
        <form action="registrodb.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Registrar</button>
        </form>
        <p><a href="Login.php">Inicia sesión</a>.</p>
    </div>
</body>
</html>
