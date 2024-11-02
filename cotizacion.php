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
function obtenerContenedores($conn) {
    $sql = "SELECT ID_Contenedor, Tipo, Peso, Dimensiones FROM contenedor";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function obtenerRutasDisponibles($conn) {
    $sql = "SELECT ID_Ruta, Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino, Km FROM rutas WHERE Estatus = 'Disponible'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener camiones libres
function obtenerDollyLibres($conn) {
    $sql = "SELECT ID_Dolly, PesoDolly,Marca,Placas FROM dolly WHERE estado = 'en servicio'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Función para obtener camiones libres
function obtenerRemolque($conn) {
    $sql = "SELECT id_remolque, tipo_remolque,placas FROM remolque WHERE estado = 'en servicio'";
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
$empleado = obtenerNombreEmpleado($conn, $usuario_id);
$camiones_libres = obtenerCamionesLibres($conn);
$Dolly_libres = obtenerDollyLibres($conn);
$remolques_libres = obtenerRemolque($conn);
$contenedores = obtenerContenedores($conn);
$rutas_disponibles = obtenerRutasDisponibles($conn);

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




    // Consultar el camión seleccionado
$camion_id = $_POST['camiones_libres'];
$sql_camion = "SELECT Peso FROM camion WHERE ID_Camion = $camion_id";
$result_camion = $conn->query($sql_camion);
$camion = $result_camion->fetch_assoc();

// Consultar el remolque seleccionado
$remolque_id = $_POST['remolques_libres']; // Asume que tienes un select para el remolque
$sql_remolque = "SELECT PesoR FROM remolque WHERE id_remolque = $remolque_id";
$result_remolque = $conn->query($sql_remolque);
$remolque = $result_remolque->fetch_assoc();

// Consultar el dolly seleccionado
$dolly_id = $_POST['Dolly_libres']; // Asume que tienes un select para el dolly
$sql_dolly = "SELECT PesoDolly FROM dolly WHERE ID_Dolly = $dolly_id";
$result_dolly = $conn->query($sql_dolly);
$dolly = $result_dolly->fetch_assoc();

// Consultar el contenedor seleccionado
$contenedor_id = $_POST['contenedores'];
$sql_contenedor = "SELECT Peso FROM contenedor WHERE ID_Contenedor = $contenedor_id";
$result_contenedor = $conn->query($sql_contenedor);
$contenedor = $result_contenedor->fetch_assoc();
    
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
        <<div class="mb-3"> 
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
    <input type="number" step="0.01" class="form-control" id="peso" name="peso" required readonly>
</div>


<script>
    // Pasar los valores de PHP a JavaScript
    var pesoCamion = <?= $camion['Peso'] ?>;
    var pesoRemolque = <?= $remolque['PesoR'] ?>;
    var pesoDolly = <?= $dolly['PesoDolly'] ?>;
    var pesoContenedor = <?= $contenedor['Peso'] ?>;

    // Función para calcular el peso total
    function calcularPeso() {
        // Calcular el total de peso: sumar los pesos y restar 75 toneladas (75,000 kg)
        var totalPeso = pesoCamion + pesoRemolque + pesoDolly + pesoContenedor - (75 * 1000); // Convertir toneladas a kg
        document.getElementById('peso').value = totalPeso.toFixed(2); // Mostrar el resultado con 2 decimales
    }

    // Ejecutar la función cuando se carga la página
    window.onload = calcularPeso;
</script>

<!-- Select para "Tipo de pago" -->
<div class="mb-3">
    <label for="tipo_pago" class="form-label">Tipo de Pago</label>
    <select class="form-control" id="tipo_pago" name="tipo_pago" required>
        <option value="PUE">PUE - Pago en una sola exhibición</option>
        <option value="CREDITO">CREDITO - Crédito</option>
    </select>
</div>

<!-- Select para "Categoría de mercancía" -->
<div class="mb-3">
    <label for="categoria_mercancia" class="form-label">Categoría de Mercancía</label>
    <select class="form-control" id="categoria_mercancia" name="categoria_mercancia" required>
    <option value="Contenerizada">Contenerizada</option>
        <option value="productos_manufacturados">Productos manufacturados: Electrónica, textiles, muebles, autopartes</option>
        <option value="productos_consumo_masivo">Productos de consumo masivo: Alimentos enlatados, bebidas, productos de limpieza</option>
        <option value="articulos_personales">Artículos personales: Mudanzas, envíos de hogar</option>
        <option value="productos_agricolas">Productos agrícolas: Granos, semillas, fertilizantes, frutas, verduras</option>
        <option value="productos_quimicos">Productos químicos: Ácidos, bases, solventes, alimentos líquidos (vino, zumos, aceites), gases</option>
        <option value="maquinaria_equipos">Maquinaria y equipos: Vehículos, maquinaria pesada, equipos de construcción, maquinaria agrícola, transformadores, turbinas</option>
        <option value="materiales_construccion">Materiales de construcción: Tubos, vigas de acero, bobinas de acero, placas de madera</option>
        <option value="otros">Otros: Flores y plantas, medicamentos, vacunas, barcos, aeronaves (desarmadas), yates</option>
        <option value="N/A">N/a: No Aplica</option>
    </select>
</div>

        <h2>Detalles del Traslado</h2>

<!-- Selección de rutas -->
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

<!-- Botón para agregar la ruta seleccionada -->
<button type="button" class="btn btn-primary" id="agregarRuta">Agregar Ruta</button>

<!-- Tabla para mostrar las rutas seleccionadas -->
<h3>Rutas Seleccionadas</h3>
<table class="table table-bordered" id="tablaRutas">
    <thead>
        <tr>
            <th>Punto A de Origen</th>
            <th>Punto B de Destino</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="rutasBody">
        <!-- Aquí se añadirán las rutas seleccionadas -->
    </tbody>
</table>

<form method="POST" action="">
    <h2>Detalles de la Flotilla</h2>
    <div class="mb-3">
        <label for="camiones_libres" class="form-label">Seleccionar Camiones Libres</label>
        <select class="form-select" id="camiones_libres" name="camiones_libres" required>
            <?php foreach ($camiones_libres as $camion): ?>
                <option value="<?php echo $camion['ID_Camion']; ?>"><?php echo htmlspecialchars($camion['Placas']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="remolque" class="form-label">Seleccionar Remolque</label>
        <select class="form-select" id="remolque" name="remolque" required>
            <?php foreach ($remolques_libres as $remolque): ?>
                <option value="<?php echo $remolque['id_remolque']; ?>"><?php echo htmlspecialchars($remolque['tipo_remolque']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="dolly" class="form-label">Seleccionar Dolly</label>
        <select class="form-select" id="dolly" name="dolly" required>
            <?php foreach ($Dolly_libres as $dolly): ?>
                <option value="<?php echo $dolly['ID_Dolly']; ?>"><?php echo htmlspecialchars($dolly['Marca']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="tipo_camiones">Tipo de Contenedor</label>
        <select id="tipo_camiones" name="tipo_camiones" class="form-control">
            <?php foreach ($contenedores as $contenedor) { ?>
                <option value="<?= $contenedor['ID_Contenedor'] ?>"><?= $contenedor['Tipo'] ?> (Peso: <?= $contenedor['Peso'] ?>, Dimensiones: <?= $contenedor['Dimensiones'] ?>)</option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Calcular Capacidad</button>
</form>
        <button type="submit" class="btn btn-primary">Crear Cotización</button>
    </form>
</div>
        <!-- Modal para agregar rutas -->
        <div class="modal fade" id="rutasModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Seleccionar Ruta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Estado Origen</th>
                                    <th>Municipio Origen</th>
                                    <th>Estado Destino</th>
                                    <th>Municipio Destino</th>
                                    <th>Km</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rutas_disponibles as $ruta) { ?>
                                    <tr>
                                        <td><?= $ruta['Estado_Origen'] ?></td>
                                        <td><?= $ruta['Municipio_Origen'] ?></td>
                                        <td><?= $ruta['Estado_Destino'] ?></td>
                                        <td><?= $ruta['Municipio_Destino'] ?></td>
                                        <td><?= $ruta['Km'] ?></td>
                                        <td><input type="checkbox" name="rutas_seleccionadas[]" value="<?= $ruta['ID_Ruta'] ?>"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
                <!-- Cerrar Modal para agregar rutas -->
<script>
document.getElementById('agregarRuta').addEventListener('click', function() {
    // Obtener los valores seleccionados de los puntos de origen y destino
    var puntoA = document.getElementById('puntoA_origen').value;
    var puntoB = document.getElementById('puntoB_destino').value;

    // Verificar que ambos valores hayan sido seleccionados
    if (puntoA && puntoB) {
        // Crear una nueva fila para la tabla
        var tablaBody = document.getElementById('rutasBody');
        var nuevaFila = document.createElement('tr');
        
        // Crear celdas para la fila
        var celdaPuntoA = document.createElement('td');
        celdaPuntoA.textContent = puntoA;

        var celdaPuntoB = document.createElement('td');
        celdaPuntoB.textContent = puntoB;

        // Celda para el botón de eliminar
        var celdaAcciones = document.createElement('td');
        var botonEliminar = document.createElement('button');
        botonEliminar.textContent = 'Eliminar';
        botonEliminar.className = 'btn btn-danger btn-sm';
        botonEliminar.addEventListener('click', function() {
            // Eliminar la fila cuando se haga clic en "Eliminar"
            nuevaFila.remove();
        });

        celdaAcciones.appendChild(botonEliminar);

        // Añadir las celdas a la nueva fila
        nuevaFila.appendChild(celdaPuntoA);
        nuevaFila.appendChild(celdaPuntoB);
        nuevaFila.appendChild(celdaAcciones);

        // Añadir la nueva fila a la tabla
        tablaBody.appendChild(nuevaFila);

        // Limpiar las selecciones del formulario
        document.getElementById('puntoA_origen').value = '';
        document.getElementById('puntoB_destino').value = '';
    } else {
        alert('Por favor seleccione tanto el Punto A de Origen como el Punto B de Destino.');
    }
});
    </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Incluye jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>