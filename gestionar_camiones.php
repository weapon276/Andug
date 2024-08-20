<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

// Función para obtener todos los camiones
function obtenerCamiones($conn) {
    $sql = "SELECT * FROM camion";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para cambiar el estado del camión
function cambiarEstadoCamion($conn, $id, $estado) {
    $sql = "UPDATE camion SET Status=? WHERE ID_Camion=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$estado, $id]);
}

// Función para eliminar (desactivar) al camión
function eliminarCamion($conn, $id, $comentarios) {
    // Registrar el camión en la tabla de log
    $sql_log = "INSERT INTO log_camiones_bajas (ID_Camion, Placas, Peso, Unidad, Status, Tipo, Poliza_Seguro, GPS, fecha_inicio, fecha_final, comentarios)
                SELECT ID_Camion, Placas, Peso, Unidad, Status, Tipo, Poliza_Seguro, GPS, fecha_inicio, NOW(), ? 
                FROM camion WHERE ID_Camion=?";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->execute([$comentarios, $id]);

    // Cambiar el estado del camión a "Mantenimiento"
    cambiarEstadoCamion($conn, $id, 'Mantenimiento');
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mantenimiento'])) {
        cambiarEstadoCamion($conn, $_POST['id_camion'], 'Mantenimiento');
    } elseif (isset($_POST['eliminar'])) {
        eliminarCamion($conn, $_POST['id_camion'], $_POST['comentarios']);
    }
}

$camiones = obtenerCamiones($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Camiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Camiones</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Placas</th>
                <th>Peso</th>
                <th>Unidad</th>
                <th>Status</th>
                <th>Tipo</th>
                <th>Póliza de Seguro</th>
                <th>GPS</th>
                <th>Fecha de Registro</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($camiones as $camion): ?>
            <tr>
                <td><?php echo $camion['ID_Camion']; ?></td>
                <td><?php echo $camion['Placas']; ?></td>
                <td><?php echo $camion['Peso']; ?></td>
                <td><?php echo $camion['Unidad']; ?></td>
                <td>
                    <?php 
                    if ($camion['Status'] == 'Libre') {
                        echo '<span class="badge bg-success">Libre</span>';
                    } elseif ($camion['Status'] == 'Ocupado') {
                        echo '<span class="badge bg-warning">Ocupado</span>';
                    } else {
                        echo '<span class="badge bg-danger">Mantenimiento</span>';
                    }
                    ?>
                </td>
                <td><?php echo $camion['Tipo']; ?></td>
                <td><?php echo $camion['Poliza_Seguro']; ?></td>
                <td><?php echo $camion['GPS']; ?></td>
                <td><?php echo $camion['fecha_inicio']; ?></td>
                <td><?php echo $camion['fecha_final']; ?></td>
                <td>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_camion" value="<?php echo $camion['ID_Camion']; ?>">
                        <button type="submit" name="modificar" class="btn btn-primary btn-sm">Modificar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_camion" value="<?php echo $camion['ID_Camion']; ?>">
                        <button type="submit" name="mantenimiento" class="btn btn-warning btn-sm">Mantenimiento</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_camion" value="<?php echo $camion['ID_Camion']; ?>">
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
