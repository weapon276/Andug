<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];
    $user_type = $_POST['user_type'];

    if ($password !== $password_repeat) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (username, password, email, user_type, Status, created_at, fecha_inicio) VALUES (:username, :password, :email, :user_type, 'Pendiente', NOW(), NOW())");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_type', $user_type);

        if ($stmt->execute()) {
            // Enviar notificación al administrador
            $mensaje = "Un nuevo usuario se ha registrado y está pendiente de aprobación.";
            $tipo_mensaje = "General";
            $id_destinatario = 3; // ID del administrador

            $sql = "INSERT INTO mensajes (ID_Cliente, Tipo_Mensaje, Mensaje, ID_Destinatario) 
                    VALUES (NULL, :tipo_mensaje, :mensaje, :id_destinatario)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo_mensaje', $tipo_mensaje);
            $stmt->bindParam(':mensaje', $mensaje);
            $stmt->bindParam(':id_destinatario', $id_destinatario);
            $stmt->execute();

            $success = "Usuario registrado exitosamente. Esperando aprobación del administrador.";
        } else {
            $error = "Error al registrar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <style>
body {font-family: Arial, Helvetica, sans-serif;}
* {box-sizing: border-box}

/* Full-width input fields */
input[type=text], input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

/* Set a style for all buttons */
button {
  background-color: #04AA6D;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
}

button:hover {
  opacity:1;
}

/* Extra styles for the cancel button */
.cancelbtn {
  padding: 14px 20px;
  background-color: #f44336;
}

/* Float cancel and signup buttons and add an equal width */
.cancelbtn, .signupbtn {
  float: left;
  width: 50%;
}

/* Add padding to container elements */
.container {
  padding: 16px;
}

/* Clear floats */
.clearfix::after {
  content: "";
  clear: both;
  display: table;
}

/* Change styles for cancel button and signup button on extra small screens */
@media screen and (max-width: 300px) {
  .cancelbtn, .signupbtn {
     width: 100%;
  }
}    </style>
</head>
<body>

<form action="registro.php" method="post" style="border:1px solid #ccc">
    <div class="container">
        <h1>Registrarse</h1>
        <p>Por favor, complete este formulario para crear una cuenta.</p>
        <hr>

        <label for="username"><b>Usuario</b></label>
        <input type="text" placeholder="Ingrese usuario" name="username" required>

        <label for="email"><b>Email</b></label>
        <input type="text" placeholder="Ingrese Email" name="email" required>

        <label for="password"><b>Contraseña</b></label>
        <input type="password" placeholder="Ingrese Contraseña" name="password" required>

        <label for="password_repeat"><b>Repetir Contraseña</b></label>
        <input type="password" placeholder="Repita Contraseña" name="password_repeat" required>

        <label for="user_type"><b>Tipo de Usuario</b></label>
        <select name="user_type" required>
            <option value="Administrador">Administrador</option>
            <option value="Contabilidad">Contabilidad</option>
            <option value="Recursos Humanos">Recursos Humanos</option>
            <option value="Operador">Operador</option>
            <option value="Cliente">Cliente</option>
        </select>
        <span class="psw">¿Tienes cuenta? <a href="login.php">Inicia Sesion</a></span>

        <div class="clearfix">
            <button type="button" class="cancelbtn">Cancelar</button>
            <button type="submit" class="signupbtn">Registrarse</button>
        </div>

        <?php
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        if (isset($success)) {
            echo "<p style='color:green;'>$success</p>";
        }
        ?>
    </div>
</form>

</body>
</html>
