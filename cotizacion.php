<?php
include 'conexion.php';
include 'index.php';

// Iniciar la sesión

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

// Función para obtener empleados
function obtenerEmpleados($conn) {
    $sql = "SELECT ID_Empleado, Nombre FROM empleado";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener camiones libres
function obtenerCamionesLibres($conn) {
    $sql = "SELECT ID_Camion, Placas FROM camion WHERE Status = 'Libre'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = obtenerClientes($conn);
$empleados = obtenerEmpleados($conn);
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
    $tipo_camiones = isset($_POST['tipo_camiones']) && !empty($_POST['tipo_camiones']) ? $_POST['tipo_camiones'] : 'DefaultType';

    // Insertar la cotización
    $sql = "INSERT INTO cotizacion 
            (ID_Cliente, fk_idEmpleado, Descripcion, Monto, Fecha, Vigencia, PuntoA_Origen, PuntoB_Destino, Fecha_Traslado, Horario_Carga, Horario_Descarga, Tipo_Mercancia, Condiciones_Mercancia, Servicio_Adicional, Tipo_Camiones, Numero_Camiones, Capacidad_Camiones, fecha_inicio, fecha_final) 
            VALUES 
            (:id_cliente, :id_empleado, :descripcion, :monto, :fecha, :vigencia, :puntoA_origen, :puntoB_destino, :fecha_traslado, :horario_carga, :horario_descarga, :tipo_mercancia, :condiciones_mercancia, :servicio_adicional, :tipo_camiones, :numero_camiones, :capacidad_camiones, NOW(), NOW())";
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
    $stmt->execute();

    $id_cotizacion = $conn->lastInsertId(); // Obtener el ID de la cotización insertada

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
               $sql_mensaje = "INSERT INTO mensajes (Tipo_Mensaje, Mensaje, Fecha_Envio) VALUES (:tipo_mensaje, :mensaje_texto, :fecha)";
               $stmt_mensaje = $conn->prepare($sql_mensaje);
               $stmt_mensaje->bindParam(':tipo_mensaje', $tipo_mensaje);
               $stmt_mensaje->bindParam(':mensaje_texto', $mensaje_texto);
               $stmt_mensaje->bindParam(':fecha', $fecha);
               $stmt_mensaje->execute();

    // Insertar en la tabla log_movimientos
    $descripcion_log = "Cotización creada para el cliente ID: $id_cliente con la descripción: '$descripcion'.";
    $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) VALUES (:user_id, 'Creación de Cotización', :descripcion_log)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bindParam(':user_id', $usuario_id); // Suponiendo que el empleado que crea la cotización es quien realiza la acción
    $stmt_log->bindParam(':descripcion_log', $descripcion_log);
    $stmt_log->execute();
          
} 
?>


<!DOCTYPE html>
<html>
<head>
    <title>Elaboración de Cotización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Elaboración de Cotización</h1>
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
            <label for="empleado" class="form-label">Empleado</label>
            <select class="form-select" id="empleado" name="empleado" required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                <option value="<?php echo $empleado['ID_Empleado']; ?>"><?php echo htmlspecialchars($empleado['Nombre']); ?></option>
                <?php endforeach; ?>
            </select>
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
            <input type="text" class="form-control" id="puntoA_origen" name="puntoA_origen" required>
        </div>
        <div class="mb-3">
            <label for="puntoB_destino" class="form-label">Punto B de Destino</label>
            <input type="text" class="form-control" id="puntoB_destino" name="puntoB_destino" required>
        </div>
        <div class="mb-3">
            <label for="fecha_traslado" class="form-label">Fecha del Traslado</label>
            <input type="date" class="form-control" id="fecha_traslado" name="fecha_traslado" required>
        </div>
        <div class="mb-3">
            <label for="horario_carga" class="form-label">Horario de Carga</label>
            <input type="time" class="form-control" id="horario_carga" name="horario_carga" required>
        </div>
        <div class="mb-3">
            <label for="horario_descarga" class="form-label">Horario de Descarga</label>
            <input type="time" class="form-control" id="horario_descarga" name="horario_descarga" required>
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
            <label for="capacidad_camiones" class="form-label">Capacidad de Carga de los Camiones</label>
            <input type="text" class="form-control" id="capacidad_camiones" name="capacidad_camiones" required>
        </div>
        <h2>Detalles de Servicio</h2>
        <div class="mb-3">
            <label for="rutas" class="form-label">Rutas (Formato JSON)</label>
            <textarea class="form-control" id="rutas" name="rutas[]" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="detalles" class="form-label">Detalles de Servicio (Formato JSON)</label>
            <textarea class="form-control" id="detalles" name="detalles[]" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear Cotización</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>