<?php

include '../modelo/conexion.php';
include '../index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener todos los viajes
function obtenerViajes($conn) {
    $sql = "SELECT viaje.*, camion.Placas, operador.Nombre as Nombre_Operador, cliente.Nombre as Nombre_Cliente, rutas.Estado_Origen, rutas.Municipio_Origen, rutas.Estado_Destino, rutas.Municipio_Destino
            FROM viaje
            LEFT JOIN camion ON viaje.ID_Camion = camion.ID_Camion
            LEFT JOIN operador ON viaje.ID_Operador = operador.ID_Operador
            LEFT JOIN cliente ON viaje.ID_Cliente = cliente.ID_Cliente
            LEFT JOIN rutas ON viaje.Fk_idRuta = rutas.ID_Ruta";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para cambiar el estado del viaje
function cambiarEstadoViaje($conn, $id, $estado) {
    $sql = "UPDATE viaje SET Status=? WHERE ID_Viaje=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$estado, $id]);
}

// Función para eliminar (cancelar) el viaje
function eliminarViaje($conn, $id, $comentarios) {
    // Registrar el viaje en la tabla de log
    $sql_log = "INSERT INTO log_viajes_cancelados (ID_Viaje, ID_Camion, ID_Operador, ID_Cliente, ID_Ruta, Fecha_Despacho, Fecha_Llegada, Pedimentos, Contenedores, Gastos, Status, fecha_inicio, fecha_final, comentarios)
                SELECT ID_Viaje, ID_Camion, ID_Operador, ID_Cliente, ID_Ruta, Fecha_Despacho, Fecha_Llegada, Pedimentos, Contenedores, Gastos, Status, fecha_inicio, NOW(), ? 
                FROM viaje WHERE ID_Viaje=?";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->execute([$comentarios, $id]);

    // Cambiar el estado del viaje a "Cancelado"
    cambiarEstadoViaje($conn, $id, 'Cancelado');
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['completar'])) {
        cambiarEstadoViaje($conn, $_POST['id_viaje'], 'Completado');
    } elseif (isset($_POST['cancelar'])) {
        eliminarViaje($conn, $_POST['id_viaje'], $_POST['comentarios']);
    }
}

$viajes = obtenerViajes($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Viajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Viajes</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Placas del Camión</th>
                <th>Nombre del Operador</th>
                <th>Nombre del Cliente</th>
                <th>Ruta Origen</th>
                <th>Ruta Destino</th>
                <th>Fecha de Despacho</th>
                <th>Fecha de Llegada</th>
                <th>Gastos</th>
                <th>Status</th>
                <th>Fecha de Registro</th>
                <th>Fecha Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viajes as $viaje): ?>
            <tr>
                <td><?php echo $viaje['ID_Viaje']; ?></td>
                <td><?php echo $viaje['Placas']; ?></td>
                <td><?php echo $viaje['Nombre_Operador']; ?></td>
                <td><?php echo $viaje['Nombre_Cliente']; ?></td>
                <td><?php echo $viaje['Estado_Origen'] . ', ' . $viaje['Municipio_Origen']; ?></td>
                <td><?php echo $viaje['Estado_Destino'] . ', ' . $viaje['Municipio_Destino']; ?></td>
                <td><?php echo $viaje['Fecha_Despacho']; ?></td>
                <td><?php echo $viaje['Fecha_Llegada']; ?></td>
                <td><?php echo $viaje['Gastos']; ?></td>
                <td>
                    <?php 
                    if ($viaje['Status'] == 'En Curso') {
                        echo '<span class="badge bg-info">En Curso</span>';
                    } elseif ($viaje['Status'] == 'Completado') {
                        echo '<span class="badge bg-success">Completado</span>';
                    } else {
                        echo '<span class="badge bg-danger">Cancelado</span>';
                    }
                    ?>
                </td>
                <td><?php echo $viaje['fecha_inicio']; ?></td>
                <td><?php echo $viaje['fecha_final']; ?></td>
                <td>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_viaje" value="<?php echo $viaje['ID_Viaje']; ?>">
                        <button type="submit" name="modificar" class="btn btn-primary btn-sm">Modificar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_viaje" value="<?php echo $viaje['ID_Viaje']; ?>">
                        <button type="submit" name="completar" class="btn btn-success btn-sm">Completar</button>
                    </form>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id_viaje" value="<?php echo $viaje['ID_Viaje']; ?>">
                        <input type="text" name="comentarios" placeholder="Comentarios" required>
                        <button type="submit" name="cancelar" class="btn btn-danger btn-sm">Cancelar</button>
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
