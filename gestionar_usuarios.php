<?php
include 'conexion.php';
include 'index.php';

session_start(); // Asegúrate de iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener usuarios activos
function obtenerUsuariosActivos($conn) {
    $sql = "SELECT * FROM usuarios WHERE Status = 'Activo'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener usuarios no activos (suspendidos o dados de baja)
function obtenerUsuariosNoActivos($conn) {
    $sql = "SELECT * FROM usuarios WHERE Status IN ('Suspendido', 'Baja')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para actualizar el estado del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $id_usuario = $_POST['id_usuario'];
    $accion = $_POST['accion'];
    $nuevo_estado = $accion === 'suspender' ? 'Suspendido' : ($accion === 'eliminar' ? 'Baja' : 'Activo');

    $sql = "UPDATE usuarios SET Status = :nuevo_estado, FechaUp = NOW() WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    header("Location: gestionar_usuarios.php");
    exit();
}

// Función para agregar un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_usuario'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

    $sql = "INSERT INTO usuarios (username, password, email, user_type, Status, created_at, fecha_inicio) VALUES (:username, :password, :email, :user_type, 'Activo', NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_type', $user_type);
    $stmt->execute();

    header("Location: gestionar_usuarios.php");
    exit();
}

// Función para modificar un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_usuario'])) {
    $id_usuario = $_POST['id_usuario'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    $sql = "UPDATE usuarios SET username = :username, email = :email, user_type = :user_type, FechaUp = NOW() WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_type', $user_type);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    header("Location: gestionar_usuarios.php");
    exit();
}

$usuarios_activos = obtenerUsuariosActivos($conn);
$usuarios_no_activos = obtenerUsuariosNoActivos($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Usuarios</h1>

    <!-- Botón para agregar usuario -->
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
        <i class="fa fa-plus"></i> Agregar Usuario
    </button>

    <!-- Tabla de usuarios activos -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Tipo de Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios_activos as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['user_type']); ?></td>
                <td>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuspenderUsuario" data-id="<?php echo $usuario['id']; ?>">
                        <i class="fa fa-exclamation-triangle"></i> Suspender
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario" data-id="<?php echo $usuario['id']; ?>">
                        <i class="fa fa-times"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalModificarUsuario" data-id="<?php echo $usuario['id']; ?>" data-username="<?php echo $usuario['username']; ?>" data-email="<?php echo $usuario['email']; ?>" data-user_type="<?php echo $usuario['user_type']; ?>">
                        <i class="fa fa-book"></i> Modificar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabla de usuarios no activos -->
    <h2>Usuarios No Activos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Tipo de Usuario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios_no_activos as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['user_type']); ?></td>
                <td><?php echo htmlspecialchars($usuario['Status']); ?></td>
                <td>
                    <form method="POST" action="gestionar_usuarios.php" style="display:inline-block;">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                        <input type="hidden" name="accion" value="reactivar">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Reactivar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Agregar Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Agregar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="gestionar_usuarios.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="user_type" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="user_type" name="user_type" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Recursos Humanos">Recursos Humanos</option>
                            <option value="Operador">Operador</option>
                            <option value="Cliente">Cliente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="agregar_usuario">Agregar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Suspender Usuario -->
<div class="modal fade" id="modalSuspenderUsuario" tabindex="-1" aria-labelledby="modalSuspenderUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuspenderUsuarioLabel">Suspender Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="gestionar_usuarios.php">
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas suspender a este usuario?</p>
                    <input type="hidden" id="suspender_id_usuario" name="id_usuario">
                    <input type="hidden" name="accion" value="suspender">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Suspender</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Usuario -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarUsuarioLabel">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="gestionar_usuarios.php">
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar a este usuario?</p>
                    <input type="hidden" id="eliminar_id_usuario" name="id_usuario">
                    <input type="hidden" name="accion" value="eliminar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modificar Usuario -->
<div class="modal fade" id="modalModificarUsuario" tabindex="-1" aria-labelledby="modalModificarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarUsuarioLabel">Modificar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="gestionar_usuarios.php">
                <div class="modal-body">
                    <input type="hidden" id="modificar_id_usuario" name="id_usuario">
                    <div class="mb-3">
                        <label for="modificar_username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="modificar_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="modificar_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="modificar_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="modificar_user_type" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="modificar_user_type" name="user_type" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Recursos Humanos">Recursos Humanos</option>
                            <option value="Operador">Operador</option>
                            <option value="Cliente">Cliente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="modificar_usuario">Modificar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybGotRaEd2AMo6oBdIRiOr0B8qqsTxp62Jfj8J1wWZCKkSmXI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-qnO/jL0gH35EpVYpDgE2QJ4acRmaEqw76pZtv5RI1W/Z3KFuugPWRMQk/foF1xA" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalSuspenderUsuario = document.getElementById('modalSuspenderUsuario');
    modalSuspenderUsuario.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var input = modalSuspenderUsuario.querySelector('#suspender_id_usuario');
        input.value = id;
    });

    var modalEliminarUsuario = document.getElementById('modalEliminarUsuario');
    modalEliminarUsuario.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var input = modalEliminarUsuario.querySelector('#eliminar_id_usuario');
        input.value = id;
    });

    var modalModificarUsuario = document.getElementById('modalModificarUsuario');
    modalModificarUsuario.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var username = button.getAttribute('data-username');
        var email = button.getAttribute('data-email');
        var userType = button.getAttribute('data-user_type');

        var inputId = modalModificarUsuario.querySelector('#modificar_id_usuario');
        var inputUsername = modalModificarUsuario.querySelector('#modificar_username');
        var inputEmail = modalModificarUsuario.querySelector('#modificar_email');
        var inputUserType = modalModificarUsuario.querySelector('#modificar_user_type');

        inputId.value = id;
        inputUsername.value = username;
        inputEmail.value = email;
        inputUserType.value = userType;
    });
});
</script>
</body>
</html>
