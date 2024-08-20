<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Función para obtener clientes
function obtenerClientes($conn) {
    $sql = "SELECT ID_Cliente, Nombre FROM cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = obtenerClientes($conn);

// Manejar la solicitud de la cotización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['cliente'];
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
    $tipo_camiones = $_POST['tipo_camiones'];
    $numero_camiones = $_POST['numero_camiones'];
    $capacidad_camiones = $_POST['capacidad_camiones'];

    $sql = "INSERT INTO cotizacion 
            (ID_Cliente, Descripcion, Monto, Fecha, Vigencia, PuntoA_Origen, PuntoB_Destino, Fecha_Traslado, Horario_Carga, Horario_Descarga, Tipo_Mercancia, Condiciones_Mercancia, Servicio_Adicional, Tipo_Camiones, Numero_Camiones, Capacidad_Camiones) 
            VALUES 
            (:id_cliente, :descripcion, :monto, :fecha, :vigencia, :puntoA_origen, :puntoB_destino, :fecha_traslado, :horario_carga, :horario_descarga, :tipo_mercancia, :condiciones_mercancia, :servicio_adicional, :tipo_camiones, :numero_camiones, :capacidad_camiones)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
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

    header("Location: cotizacion.php");
    exit();
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
            <input type="text" class="form-control" id="horario_carga" name="horario_carga" required>
        </div>
        <div class="mb-3">
            <label for="horario_descarga" class="form-label">Horario de Descarga</label>
            <input type="text" class="form-control" id="horario_descarga" name="horario_descarga" required>
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
            <label for="tipo_camiones" class="form-label">Tipo de Camiones</label>
            <textarea class="form-control" id="tipo_camiones" name="tipo_camiones" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="numero_camiones" class="form-label">Número de Camiones</label>
            <input type="number" class="form-control" id="numero_camiones" name="numero_camiones" required>
        </div>
        <div class="mb-3">
            <label for="capacidad_camiones" class="form-label">Capacidad de Carga de los Camiones</label>
            <input type="text" class="form-control" id="capacidad_camiones" name="capacidad_camiones" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Cotización</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
