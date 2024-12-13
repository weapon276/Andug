<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="db.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" 
                   value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>" 
                   required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" 
                   value="<?php echo isset($_GET['password']) ? htmlspecialchars($_GET['password']) : ''; ?>" 
                   required>
            <button type="submit">Ingresar</button>
        </form>
        <div class="text-center">
            <span class="psw"><a href="registro.php">Registrarte</a></span>
        </div>
    </div>
</body>
</html>
