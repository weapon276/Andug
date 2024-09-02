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

// Función para obtener todos los clientes
function obtenerClientes($conn) {
    $sql = "SELECT * FROM cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para cambiar el estado del cliente
function cambiarEstadoCliente($conn, $id, $estado) {
    $sql = "UPDATE cliente SET Status=? WHERE ID_Cliente=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$estado, $id]);
}

// Función para eliminar (desactivar) al cliente
function eliminarCliente($conn, $id, $comentarios) {
    // Registrar el cliente en la tabla de log
    $sql_log = "INSERT INTO log_clientes_bajas (ID_Cliente, Nombre, Direccion, Tipo, Linea_Credito, Pago_Contado, Status, fecha_inicio, fecha_final, comentarios)
                SELECT ID_Cliente, Nombre, Direccion, Tipo, Linea_Credito, Pago_Contado, Status, fecha_inicio, NOW(), ? 
                FROM cliente WHERE ID_Cliente=?";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->execute([$comentarios, $id]);

    // Cambiar el estado del cliente a "Baja"
    cambiarEstadoCliente($conn, $id, 'Baja');
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['suspender'])) {
        cambiarEstadoCliente($conn, $_POST['id_cliente'], 'Suspendido');
    } elseif (isset($_POST['eliminar'])) {
        eliminarCliente($conn, $_POST['id_cliente'], $_POST['comentarios']);
    }
}

$clientes = obtenerClientes($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Clientes</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Tipo</th>
                <th>Línea de Crédito</th>
                <th>Pago Contado</th>
                <th>Status</th>
                <th>Fecha de Registro</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo $cliente['ID_Cliente']; ?></td>
                <td><?php echo $cliente['Nombre']; ?></td>
                <td><?php echo $cliente['Direccion']; ?></td>
                <td><?php echo $cliente['Tipo']; ?></td>
                <td><?php echo $cliente['Linea_Credito']; ?></td>
                <td><?php echo $cliente['Pago_Contado'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <?php 
                    if ($cliente['Status'] == 'Activo') {
                        echo '<span class="badge bg-success">Activo</span>';
                    } elseif ($cliente['Status'] == 'Suspendido') {
                        echo '<span class="badge bg-warning">Suspendido</span>';
                    } else {
                        echo '<span class="badge bg-danger">Baja</span>';
                    }
                    ?>
                </td>
                <td><?php echo $cliente['fecha_inicio']; ?></td>
                <td><?php echo $cliente['fecha_final']; ?></td>
                <td>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
                        <button type="submit" name="modificar" class="btn btn-primary btn-sm">Modificar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
                        <button type="submit" name="suspender" class="btn btn-warning btn-sm">Suspender</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
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
