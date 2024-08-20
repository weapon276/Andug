<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

// Función para obtener todos los empleados
function obtenerEmpleados($conn) {
    $sql = "SELECT * FROM empleado";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para cambiar el estado del empleado
function cambiarEstadoEmpleado($conn, $id, $estado) {
    $sql = "UPDATE empleado SET Status=? WHERE ID_Empleado=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$estado, $id]);
}

// Función para eliminar (desactivar) al empleado
function eliminarEmpleado($conn, $id, $comentarios) {
    // Registrar el empleado en la tabla de log
    $sql_log = "INSERT INTO log_bajas (ID_Empleado, Nombre, Departamento, Posicion, Fecha_Contratacion, Salario, Status, fecha_inicio, fecha_final, comentarios)
                SELECT ID_Empleado, Nombre, Departamento, Posicion, Fecha_Contratacion, Salario, Status, fecha_inicio, NOW(), ? 
                FROM empleado WHERE ID_Empleado=?";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->execute([$comentarios, $id]);

    // Cambiar el estado del empleado a "Baja"
    cambiarEstadoEmpleado($conn, $id, 'Baja');
        // Cambiar el estado del empleado a "Activo"
        cambiarEstadoEmpleado($conn, $id, 'Activo');
}



// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ausentar'])) {
        cambiarEstadoEmpleado($conn, $_POST['id_empleado'], 'Ausente');
    } elseif (isset($_POST['eliminar'])) {
        eliminarEmpleado($conn, $_POST['id_empleado'], $_POST['comentarios']);
    }  elseif (isset($_POST['activar'])) 
        cambiarEstadoEmpleado($conn, $_POST['id_empleado'], 'Activar');
    
}

$empleados = obtenerEmpleados($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Usuarios</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Posición</th>
                <th>Fecha de Contratación</th>
                <th>Salario</th>
                <th>Status</th>
                <th>Fecha de Registro</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?php echo $empleado['ID_Empleado']; ?></td>
                <td><?php echo $empleado['Nombre']; ?></td>
                <td><?php echo $empleado['Departamento']; ?></td>
                <td><?php echo $empleado['Posicion']; ?></td>
                <td><?php echo $empleado['Fecha_Contratacion']; ?></td>
                <td><?php echo $empleado['Salario']; ?></td>
                <td>
                    <?php 
                    if ($empleado['Status'] == 'Activo') {
                        echo '<span class="badge bg-success">Activo</span>';
                    } elseif ($empleado['Status'] == 'Ausente') {
                        echo '<span class="badge bg-warning">Ausente</span>';
                    } else {
                        echo '<span class="badge bg-danger">Baja</span>';
                    }
                    ?>
                </td>
                <td><?php echo $empleado['fecha_inicio']; ?></td>
                <td><?php echo $empleado['fecha_final']; ?></td>
                <td>
                <form method="post" class="d-inline">
                        <input type="hidden" name="id_empleado" value="<?php echo $empleado['ID_Empleado']; ?>">
                        <button type="submit" name="activo" class="btn btn-primary btn-sm">Activo</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_empleado" value="<?php echo $empleado['ID_Empleado']; ?>">
                        <button type="submit" name="modificar" class="btn btn-primary btn-sm">Modificar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_empleado" value="<?php echo $empleado['ID_Empleado']; ?>">
                        <button type="submit" name="ausentar" class="btn btn-warning btn-sm">Ausentar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_empleado" value="<?php echo $empleado['ID_Empleado']; ?>">
                        <input type="text" name="comentarios" placeholder="Comentarios" required>
                        <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>