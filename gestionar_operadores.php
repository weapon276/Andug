<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener todos los operadores
function obtenerOperadores($conn) {
    $sql = "SELECT * FROM operador";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para eliminar (desactivar) al operador
function eliminarOperador($conn, $id, $comentarios) {
    // Registrar el operador en la tabla de log
    $sql_log = "INSERT INTO log_operadores_bajas (ID_Operador, Nombre, Licencia, Vigencia_Licencia, CURP, Seguro_Social, fecha_inicio, fecha_final, comentarios)
                SELECT ID_Operador, Nombre, Licencia, Vigencia_Licencia, CURP, Seguro_Social, fecha_inicio, NOW(), ? 
                FROM operador WHERE ID_Operador=?";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->execute([$comentarios, $id]);

    // Aquí podrías cambiar el estado del operador si la tabla tuviera un campo de estado
    // Por ejemplo: cambiarEstadoOperador($conn, $id, 'Baja');
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['eliminar'])) {
        eliminarOperador($conn, $_POST['id_operador'], $_POST['comentarios']);
    }
}

$operadores = obtenerOperadores($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Operadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Operadores</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Licencia</th>
                <th>Vigencia de Licencia</th>
                <th>CURP</th>
                <th>Seguro Social</th>
                <th>Fecha de Registro</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($operadores as $operador): ?>
            <tr>
                <td><?php echo $operador['ID_Operador']; ?></td>
                <td><?php echo $operador['Nombre']; ?></td>
                <td><?php echo $operador['Licencia']; ?></td>
                <td><?php echo $operador['Vigencia_Licencia']; ?></td>
                <td><?php echo $operador['CURP']; ?></td>
                <td><?php echo $operador['Seguro_Social']; ?></td>
                <td><?php echo $operador['fecha_inicio']; ?></td>
                <td><?php echo $operador['fecha_final']; ?></td>
                <td>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_operador" value="<?php echo $operador['ID_Operador']; ?>">
                        <button type="submit" name="modificar" class="btn btn-primary btn-sm">Modificar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_operador" value="<?php echo $operador['ID_Operador']; ?>">
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
