<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando el registro
$usuario_id = $_SESSION['userId'];

// Función para registrar un nuevo empleado
function registrarEmpleado($conn, $nombre, $ApellidoP, $ApellidoM, $Fk_usertype, $departamento, $posicion, $fechaContratacion, $salario, $status, $imagenPath) {
    // Validar el valor de Status para que coincida con los valores del ENUM
    $status = in_array($status, ['Activo', 'ausente', 'Baja']) ? $status : 'Activo'; // Puedes definir un valor por defecto si es necesario

    $sql = "INSERT INTO empleado (Nombre, ApellidoP, ApellidoM, Fk_usertype, Departamento, Posicion, Fecha_Contratacion, Salario, Status, Imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([$nombre, $ApellidoP, $ApellidoM, $Fk_usertype, $departamento, $posicion, $fechaContratacion, $salario, $status, $imagenPath])) {
        $_SESSION['error_message'] = "Error al registrar empleado: " . implode(", ", $stmt->errorInfo());
        header("Location: gestionar_empleados.php");
        exit();
    }
    return $conn->lastInsertId(); // Retornar el ID del empleado registrado
}


function registrarUsuario($conn, $username, $password, $correo, $fk_typeuser) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (username, nPass, vCorreo, fk_typeuser) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([$username, $hashedPassword, $correo, $fk_typeuser])) {
        $_SESSION['error_message'] = "Error al registrar usuario: " . implode(", ", $stmt->errorInfo());
        header("Location: gestionar_empleados.php");
        exit();
    }
}


// Función para registrar en log_movimientos y mensajes
function registrarMovimiento($conn, $user_id, $accion, $descripcion) {
    $sql = "INSERT INTO log_movimientos (user_id, accion, descripcion) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([$user_id, $accion, $descripcion])) {
        print_r($stmt->errorInfo());
    }
}

function registrarMensaje($conn, $user_id, $tipo_mensaje, $mensaje) {
    // Verifica si el tipo de usuario existe
    $sql = "SELECT id_TypeUser FROM typeuser WHERE id_TypeUser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tipo_mensaje]);
    
    if ($stmt->rowCount() == 0) {
        die('El tipo de usuario no existe en la base de datos.');
    }

    // Inserta el mensaje si el tipo de usuario es válido
    $sql = "INSERT INTO mensajes (fk_id_TypeUser, Tipo_Mensaje, Mensaje) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->execute([$tipo_mensaje, $tipo_mensaje, $mensaje])) {
        print_r($stmt->errorInfo());
    }
}

// Obtener tipos de usuarios para el select
function obtenerTiposUsuarios($conn) {
    $sql = "SELECT id_TypeUser, NombreTypeUser FROM typeuser";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $ApellidoP = $_POST['ApellidoP'];
    $ApellidoM = $_POST['ApellidoM'];
    $Fk_usertype = $_POST['Fk_usertype']; // Asegurarse de obtener el tipo de usuario
    $departamento = isset($_POST['departamento']) ? $_POST['departamento'] : null; // Verificar si está definido
    $posicion = $_POST['posicion'];
    $fechaContratacion = $_POST['fecha_contratacion'];
    $salario = $_POST['salario'];
    $status = $_POST['status'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $correo = $_POST['correo'];
    $fk_typeuser = isset($_POST['fk_typeuser']) ? $_POST['fk_typeuser'] : $Fk_usertype; // Usar Fk_usertype si fk_typeuser no está definido

    // Manejo de la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagenTmpPath = $_FILES['imagen']['tmp_name'];
        $imagenName = basename($_FILES['imagen']['name']);
        $imagenPath = 'uploads/' . $imagenName;

        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (!move_uploaded_file($imagenTmpPath, $imagenPath)) {
            die('Error al mover el archivo subido.');
        }
    } else {
        $imagenPath = NULL; // Si no se subió ninguna imagen
    }

    // Asegúrate de pasar el número correcto de argumentos y en el orden correcto
    $empleadoId = registrarEmpleado($conn, $nombre, $ApellidoP, $ApellidoM, $Fk_usertype, $departamento, $posicion, $fechaContratacion, $salario, $status, $imagenPath);
    registrarUsuario($conn, $username, $password,  $correo, $fk_typeuser);
    
    registrarMovimiento($conn, $usuario_id, 'Registro de Empleado', 'Empleado agregado con ID: ' . $empleadoId);
    registrarMensaje($conn, $usuario_id, $fk_typeuser, 'Se ha registrado un nuevo empleado con ID: ' . $empleadoId);

    $_SESSION['registro_exito'] = true;
    header("Location: gestionar_empleados.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
  <!-- Margen de tabla y menu lateral -->
  <div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Registrar Empleado</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="ApellidoP" class="form-label">Apellido Paterno</label>
            <input type="text" class="form-control" id="ApellidoP" name="ApellidoP" required>
        </div>
        <div class="mb-3">
            <label for="ApellidoM" class="form-label">Apellido Materno</label>
            <input type="text" class="form-control" id="ApellidoM" name="ApellidoM" required>
        </div>
        <div class="mb-3">
            <label for="Fk_usertype" class="form-label">Tipo de Usuario</label>
            <select class="form-select" id="Fk_usertype" name="Fk_usertype" required>
                <?php
                $tiposUsuarios = obtenerTiposUsuarios($conn);
                foreach ($tiposUsuarios as $tipo) {
                    echo "<option value=\"{$tipo['id_TypeUser']}\">{$tipo['NombreTypeUser']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="posicion" class="form-label">Posición</label>
            <input type="text" class="form-control" id="posicion" name="posicion" required>
        </div>
        <div class="mb-3">
            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
            <input type="date" class="form-control" id="fecha_contratacion" name="fecha_contratacion" required>
        </div>
        <div class="mb-3">
            <label for="salario" class="form-label">Salario</label>
            <input type="number" class="form-control" id="salario" name="salario" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del Empleado</label>
            <input type="file" class="form-control" id="imagen" name="imagen">
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
