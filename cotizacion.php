<?php
include 'conexion.php';
include 'index.php';

// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener clientes
function obtenerClientes($conn) {
    $sql = "SELECT ID_Cliente, Nombre FROM cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener el nombre del empleado que inició sesión
function obtenerNombreEmpleado($conn, $usuario_id) {
    $sql = "SELECT ID_Empleado, CONCAT(Nombre, ' ', ApellidoP) AS NombreCompleto FROM empleado WHERE fk_idUsuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el empleado
    if (!$resultado) {
        return null; // Retornar null si no se encontró el empleado
    }

    return $resultado;
}

// Consulta para obtener los municipios de origen y destino únicos de las rutas disponibles
$sql = "SELECT DISTINCT Municipio_Origen, Municipio_Destino FROM rutas WHERE Estatus = 'Disponible'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para obtener camiones libres
function obtenerCamionesLibres($conn) {
    $sql = "SELECT ID_Camion, Placas FROM camion WHERE Status = 'Libre'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerRutas($conn, $estadoOrigen, $municipioOrigen, $estadoDestino, $municipioDestino) {
    try {
        $sql = "SELECT ID_Ruta, Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino, Km 
                FROM rutas 
                WHERE Estado_Origen = :estadoOrigen 
                AND Municipio_Origen = :municipioOrigen 
                AND Estado_Destino = :estadoDestino 
                AND Municipio_Destino = :municipioDestino
                AND Estatus = 'Disponible'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':estadoOrigen', $estadoOrigen);
        $stmt->bindParam(':municipioOrigen', $municipioOrigen);
        $stmt->bindParam(':estadoDestino', $estadoDestino);
        $stmt->bindParam(':municipioDestino', $municipioDestino);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}

$clientes = obtenerClientes($conn);
$empleado = obtenerNombreEmpleado($conn, $usuario_id); // Obtener nombre completo del empleado que inició sesión
$camiones_libres = obtenerCamionesLibres($conn);

// Manejar la solicitud de la cotización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id_cliente = $_POST['cliente'];
    $id_empleado = $_POST['empleado'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $vigencia = $_POST['vigencia'];
    $puntoA_origen = $_POST['puntoA_origen'];
    $puntoB_destino = $_POST['puntoB_destino'];
    $fecha_traslado = $_POST['fecha_traslado'];
    $horario_carga = $_POST['horario_carga'];
    $horario_descarga = $_POST['horario_descarga'];
    $tipo_mercancia = $_POST['tipo_mercancia'];
    $condiciones_mercancia = $_POST['condiciones_mercancia'];
    $servicio_adicional = $_POST['servicio_adicional'];
    $camiones_libres = $_POST['camiones_libres']; // Array de camiones seleccionados
    $numero_camiones = $_POST['numero_camiones'];
    $capacidad_camiones = $_POST['capacidad_camiones'];
    $dias_credito = $_POST['dias_credito'];
    $peso = $_POST['peso'];

    // Validar Tipo_Camiones
    $id_ruta = isset($_POST['ruta']) ? $_POST['ruta'] : null; // Ruta seleccionada, si está presente

    $tipo_camiones = isset($_POST['tipo_camiones']) && !empty($_POST['tipo_camiones']) ? $_POST['tipo_camiones'] : 'DefaultType';

    // Insertar la cotización
    $sql = "INSERT INTO cotizacion 
            (ID_Cliente, fk_idEmpleado, Descripcion, Monto, Fecha, Vigencia, PuntoA_Origen, PuntoB_Destino, Fecha_Traslado, Horario_Carga, Horario_Descarga, Tipo_Mercancia, Condiciones_Mercancia, Servicio_Adicional, Tipo_Camiones, Numero_Camiones, Capacidad_Camiones, Fk_IdRutas, fecha_inicio, fecha_final) 
            VALUES 
            (:id_cliente, :id_empleado, :descripcion, :monto, :fecha, :vigencia, :puntoA_origen, :puntoB_destino, :fecha_traslado, :horario_carga, :horario_descarga, :tipo_mercancia, :condiciones_mercancia, :servicio_adicional, :tipo_camiones, :numero_camiones, :capacidad_camiones, :id_ruta, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':id_empleado', $id_empleado);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':vigencia', $vigencia);
    $stmt->bindParam(':puntoA_origen', $puntoA_origen);
    $stmt->bindParam(':puntoB_destino', $puntoB_destino);
    $stmt->bindParam(':fecha_traslado', $fecha_traslado);
    $stmt->bindParam(':horario_carga', $horario_carga);
    $stmt->bindParam(':horario_descarga', $horario_descarga);
    $stmt->bindParam(':tipo_mercancia', $tipo_mercancia);
    $stmt->bindParam(':condiciones_mercancia', $condiciones_mercancia);
    $stmt->bindParam(':servicio_adicional', $servicio_adicional);
    $stmt->bindParam(':tipo_camiones', $tipo_camiones);
    $stmt->bindParam(':numero_camiones', $numero_camiones);
    $stmt->bindParam(':capacidad_camiones', $capacidad_camiones);
    $stmt->bindParam(':id_ruta', $id_ruta);
    $stmt->execute();

    $id_cotizacion = $conn->lastInsertId(); // Obtener el ID de la cotización insertada

    // Insertar el viaje
// Insertar el viaje
$sql = "INSERT INTO viaje 
        (ID_Camion, ID_Operador, ID_Cliente, Fk_idRuta, Fk_IdCotizacion, Fecha_Despacho, Fecha_Llegada, Pedimentos, Contenedores, Gastos, fecha_inicio, fecha_final, Toneladas, Comentarios) 
        VALUES 
        (:id_camion, :id_operador, :id_cliente, :id_ruta, :id_cotizacion, :fecha_despacho, :fecha_llegada, :pedimentos, :contenedores, :monto, NOW(), NOW(), :toneladas, :comentarios)";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_camion', $id_camion);
$stmt->bindParam(':id_operador', $id_operador);
$stmt->bindParam(':id_cliente', $id_cliente);
$stmt->bindParam(':id_ruta', $id_ruta); // Vinculación correcta de `:id_ruta`
$stmt->bindParam(':id_cotizacion', $id_cotizacion);
$stmt->bindParam(':fecha_despacho', $horario_carga); // Verifica que `horario_carga` es correcto para `Fecha_Despacho`
$stmt->bindParam(':fecha_llegada', $horario_descarga);
$stmt->bindParam(':pedimentos', $pedimentos);
$stmt->bindParam(':contenedores', $contenedores);
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':toneladas', $toneladas);
$stmt->bindParam(':comentarios', $comentarios);

// Ejecutar la consulta
$stmt->execute();

// Obtener el ID del viaje insertado
$id_viaje = $conn->lastInsertId(); 


    // Insertar en la tabla de unión cotizacion_camion
    $sql_insert_cotizacion_camion = "INSERT INTO cotizacion_camion (ID_Cotizacion, ID_Camion) VALUES (:id_cotizacion, :id_camion)";
    $stmt_insert_cotizacion_camion = $conn->prepare($sql_insert_cotizacion_camion);
    foreach ($camiones_libres as $id_camion) {
        $stmt_insert_cotizacion_camion->bindParam(':id_cotizacion', $id_cotizacion);
        $stmt_insert_cotizacion_camion->bindParam(':id_camion', $id_camion);
        $stmt_insert_cotizacion_camion->execute();
    }

    // Actualizar el estado del camión a "Ocupado"
    $sql_update_camion = "UPDATE camion SET Status = 'Ocupado' WHERE ID_Camion = :id_camion";
    $stmt_update_camion = $conn->prepare($sql_update_camion);
    foreach ($camiones_libres as $id_camion) {
        $stmt_update_camion->bindParam(':id_camion', $id_camion);
        $stmt_update_camion->execute();
    }

    // Insertar un mensaje en la tabla mensajes
    $mensaje_texto = "Nueva cotización creada para el cliente ID: $id_cliente con la descripción: '$descripcion' en la fecha $fecha.";
    $tipo_mensaje = 'Cotizacion';
    $sql_mensaje = "INSERT INTO mensajes (Tipo_Mensaje, Mensaje, Fecha_Envio) VALUES (:tipo_mensaje, :mensaje_texto, NOW())";
    $stmt_mensaje = $conn->prepare($sql_mensaje);
    $stmt_mensaje->bindParam(':tipo_mensaje', $tipo_mensaje);
    $stmt_mensaje->bindParam(':mensaje_texto', $mensaje_texto);
    $stmt_mensaje->execute();

     // Registrar el movimiento en la tabla de log
     $descripcion_log = "Cotizacion creada para Cliente ID: $id_cliente con ID de cotizacion $id_cotizacion. ";
     $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) VALUES (:userId, 'Creación de Factura', :descripcion)";
     $stmt_log = $conn->prepare($sql_log);
     $stmt_log->bindParam(':userId', $usuario_id);
     $stmt_log->bindParam(':descripcion', $descripcion_log);
     $stmt_log->execute();


    echo '<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">¡Éxito!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Cotización creada con éxito.
                </div>
            </div>
        </div>
    </div>';

    echo '<script>
        $(document).ready(function() {
            $("#successModal").modal("show");
            setTimeout(function() {
                window.location.href = "gestionar_cotizacion.php";
            }, 3000); // Redirigir después de 3 segundos
        });
    </script>';
}

?>



<!DOCTYPE html>
<html>
<head>
    <title>Elaboración de Cotización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container">
    <h2>Crear Cotización</h2>

        <?php if (isset($_GET['success'])) : ?>
            <div class="alert alert-success" role="alert">
                La cotización se ha creado con éxito.
            </div>
        <?php endif; ?>

<div class="container mt-5">
    <h1></h1>
    <form method="POST" action="cotizacion.php">
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente</label>
            <select class="form-select" id="cliente" name="cliente" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                <option value="<?php echo $cliente['ID_Cliente']; ?>"><?php echo htmlspecialchars($cliente['Nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
        <div class="form-group">
                <label for="empleado">Empleado</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($empleado['NombreCompleto']); ?>" disabled>
                <input type="hidden" name="empleado" value="<?php echo htmlspecialchars($empleado['ID_Empleado']); ?>">
            </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <div class="mb-3">
            <label for="vigencia" class="form-label">Vigencia</label>
            <input type="date" class="form-control" id="vigencia" name="vigencia" required>
        </div>
        <div class="mb-3">
            <label for="dias_credito" class="form-label">Días de Crédito</label>
            <input type="number" class="form-control" id="dias_credito" name="dias_credito" required>
        </div>
        <div class="mb-3">
            <label for="peso" class="form-label">Peso</label>
            <input type="number" step="0.01" class="form-control" id="peso" name="peso" required>
        </div>
        <h2>Detalles del Traslado</h2>
        <div class="mb-3">
                <label for="puntoA_origen" class="form-label">Punto A de Origen</label>
                <select class="form-control" id="puntoA_origen" name="puntoA_origen" required>
                    <option value="">Seleccione un municipio de origen</option>
                    <?php foreach ($rutas as $ruta): ?>
                        <option value="<?php echo htmlspecialchars($ruta['Municipio_Origen']); ?>">
                            <?php echo htmlspecialchars($ruta['Municipio_Origen']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="puntoB_destino" class="form-label">Punto B de Destino</label>
                <select class="form-control" id="puntoB_destino" name="puntoB_destino" required>
                    <option value="">Seleccione un municipio de destino</option>
                    <?php foreach ($rutas as $ruta): ?>
                        <option value="<?php echo htmlspecialchars($ruta['Municipio_Destino']); ?>">
                            <?php echo htmlspecialchars($ruta['Municipio_Destino']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
    </div>

        <div class="mb-3">
            <label for="fecha_traslado" class="form-label">Fecha del Traslado</label>
            <input type="date" class="form-control" id="fecha_traslado" name="fecha_traslado" required>
        </div>
        <div class="mb-3">
            <label for="horario_carga" class="form-label">Horario de Carga</label>
            <input type="datetime-local" class="form-control" id="horario_carga" name="horario_carga" required>
        </div>
        <div class="mb-3">
            <label for="horario_descarga" class="form-label">Horario de Descarga</label>
            <input type="datetime-local" class="form-control" id="horario_descarga" name="horario_descarga" required>
        </div>
        <div class="mb-3">
            <label for="tipo_mercancia" class="form-label">Tipo de Mercancía</label>
            <textarea class="form-control" id="tipo_mercancia" name="tipo_mercancia" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="condiciones_mercancia" class="form-label">Condiciones de la Mercancía</label>
            <textarea class="form-control" id="condiciones_mercancia" name="condiciones_mercancia" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="servicio_adicional" class="form-label">Servicio Adicional</label>
            <textarea class="form-control" id="servicio_adicional" name="servicio_adicional" rows="3" required></textarea>
        </div>
        <h2>Detalles de la Flotilla</h2>
        <div class="mb-3">
    <label for="camiones_libres" class="form-label">Seleccionar Camiones Libres</label>
    <select class="form-select" id="camiones_libres" name="camiones_libres[]" required>
    <?php foreach ($camiones_libres as $camion): ?>
        <option value="<?php echo $camion['ID_Camion']; ?>"><?php echo htmlspecialchars($camion['Placas']); ?></option>
    <?php endforeach; ?>
    </select>
</div>

        <div class="mb-3">
            <label for="numero_camiones" class="form-label">Número de Camiones</label>
            <input type="number" class="form-control" id="numero_camiones" name="numero_camiones" required>
        </div>
        <div class="mb-3">
            <label for="capacidad_camiones" class="form-label">Capacidad de Carga del Camion</label>
            <input type="text" class="form-control" id="capacidad_camiones" name="capacidad_camiones" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Cotización</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Incluye jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>