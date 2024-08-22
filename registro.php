<?php
require 'conexion.php';

$error = ''; // Variable para almacenar el mensaje de error
$success = false; // Variable para almacenar el estado de éxito

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['nPass']) && isset($_POST['vCorreo'])) {
        $username = $_POST['username'];
        $vCorreo = $_POST['vCorreo'];
        $nPass = password_hash($_POST['nPass'], PASSWORD_DEFAULT);

        $Fk_TypeUser = 5;
        $bStatus = 'Activo';

        try {
            // Insertar usuario en la tabla usuarios
            $stmt_user = $conn->prepare("INSERT INTO usuarios (username, vCorreo, nPass, fk_typeuser, bStatus, fecha_inicio, fecha_final) VALUES (:username, :vCorreo, :nPass, :fk_typeuser, :bStatus, NOW(), NOW())");
            $stmt_user->bindParam(':vCorreo', $vCorreo);
            $stmt_user->bindParam(':username', $username);
            $stmt_user->bindParam(':nPass', $nPass);
            $stmt_user->bindParam(':fk_typeuser', $Fk_TypeUser, PDO::PARAM_INT);
            $stmt_user->bindParam(':bStatus', $bStatus);

            if ($stmt_user->execute()) {
                $success = true;

                // Notificación para el tipo de usuario Recursos Humanos (id_TypeUser = 3)
                $mensaje = "Nuevo usuario registrado: {$username}";
                $tipo = "registro_usuario";
                $ID_TypeUser = 3; // ID del tipo de usuario para Recursos Humanos

                $sql = "INSERT INTO mensajes (Mensaje, fk_id_TypeUser) VALUES (:Mensaje, :fk_id_TypeUser)";
                $stmt_notif = $conn->prepare($sql);
                $stmt_notif->bindParam(':Mensaje', $mensaje);
                $stmt_notif->bindParam(':fk_id_TypeUser', $ID_TypeUser, PDO::PARAM_INT);
                $stmt_notif->execute();
            } else {
                $error = 'Error al registrar el usuario.';
                $success = false;
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
            $success = false;
        }
    } else {
        $error = 'Por favor, complete todos los campos.';
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    /* Estilos de modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 10px;
        text-align: center;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card p-4 shadow-lg w-100" style="max-width: 400px;">
            <h2 class="text-center mb-4">Registro</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="registro.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Escribe tu usuario" required>
                </div>
                <div class="mb-3">
                    <label for="vCorreo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="vCorreo" name="vCorreo" placeholder="Escribe tu Correo" required>
                </div>
                <div class="mb-3">
                    <label for="nPass" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="nPass" name="nPass" placeholder="Escribe tu contraseña" required>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary w-100">Registrarme</button>
                </div>
                <div class="text-center">
                    <span class="psw">¿Tienes cuenta? <a href="login.php">Inicia Sesión</a></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="modal" class="modal" style="<?php echo $success ? 'display: block;' : ''; ?>">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Usuario registrado con éxito. <a href="login.php">Inicia Sesión</a></p>
        </div>
    </div>

    <!-- Modal de error -->
    <div id="modale" class="modal" style="<?php echo $error ? 'display: block;' : ''; ?>">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><?php echo $error ? $error : 'Error desconocido.'; ?></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('modal');
            var modale = document.getElementById('modale');
            var closeButtons = document.querySelectorAll('.close');

            closeButtons.forEach(function(btn) {
                btn.onclick = function() {
                    modal.style.display = 'none';
                    modale.style.display = 'none';
                }
            });

            window.onclick = function(event) {
                if (event.target === modal || event.target === modale) {
                    modal.style.display = 'none';
                    modale.style.display = 'none';
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
