<?php
include 'conexion.php';
include 'index.php';
session_start(); // Asegúrate de iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener todas las cotizaciones
function obtenerCotizaciones($conn) {
    $sql = "SELECT c.ID_Cotizacion, c.Descripcion, c.Monto, cl.Nombre as ClienteNombre, cl.Tipo as TipoCliente, cl.Linea_Credito 
            FROM cotizacion c 
            JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todos los clientes
function obtenerClientes($conn) {
    $sql = "SELECT ID_Cliente, Nombre FROM cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$cotizaciones = obtenerCotizaciones($conn);
$clientes = obtenerClientes($conn);

// Inicializar variables para mensajes
$mensaje = '';
$tipo_alerta = '';

// Manejar la solicitud de creación de factura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cotizacion = $_POST['cotizacion'];
    $id_cliente = $_POST['cliente'];
    $tipo_cliente = $_POST['tipo_cliente'];
    $tipo_pago = $_POST['tipo_pago'];
    $impuesto = $_POST['impuesto'];
    $monto = $_POST['monto'];
    $fecha = date('Y-m-d');
    $descripcion_productos = $_POST['descripcion_productos'];

    // Calcular el total con el impuesto aplicado
    $total = $monto + ($monto * $impuesto / 100);

    // Obtener la línea de crédito del cliente
    $sql_credito = "SELECT Linea_Credito, Nombre FROM cliente WHERE ID_Cliente = :id_cliente";
    $stmt_credito = $conn->prepare($sql_credito);
    $stmt_credito->bindParam(':id_cliente', $id_cliente);
    $stmt_credito->execute();
    $cliente = $stmt_credito->fetch(PDO::FETCH_ASSOC);

    // Verificar si el cliente existe y tiene una línea de crédito válida
    if ($cliente && isset($cliente['Linea_Credito'])) {
        $credito_disponible = $cliente['Linea_Credito'];
        $nombre_cliente = $cliente['Nombre'];

        // Verificar y descontar la línea de crédito
        if ($tipo_pago == 'linea_credito') {
            if ($credito_disponible >= $total) {
                $nuevo_credito = $credito_disponible - $total;
            } else {
                $nuevo_credito = 0;
                $diferencia = $total - $credito_disponible;
                $mensaje = "El crédito disponible es insuficiente. El resto ($diferencia) se cubrirá con otro tipo de pago.";
                $tipo_alerta = 'warning';
            }

            // Actualizar la línea de crédito del cliente
            $sql_update_credito = "UPDATE cliente SET Linea_Credito = :nuevo_credito WHERE ID_Cliente = :id_cliente";
            $stmt_update_credito = $conn->prepare($sql_update_credito);
            $stmt_update_credito->bindParam(':nuevo_credito', $nuevo_credito);
            $stmt_update_credito->bindParam(':id_cliente', $id_cliente);
            $stmt_update_credito->execute();
        }

        // Insertar la factura en la base de datos
        $sql = "INSERT INTO factura (ID_Cotizacion, fk_id_Cliente, fk_idEmpleado, Fecha, Monto, Impuesto, Total, Tipo_Cliente, Tipo_Pago, Descripcion_Productos) 
                VALUES (:id_cotizacion, :id_cliente, :fk_idEmpleado, :fecha, :monto, :impuesto, :total, :tipo_cliente, :tipo_pago, :descripcion_productos)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cotizacion', $id_cotizacion);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->bindParam(':fk_idEmpleado', $usuario_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':impuesto', $impuesto);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':tipo_cliente', $tipo_cliente);
        $stmt->bindParam(':tipo_pago', $tipo_pago);
        $stmt->bindParam(':descripcion_productos', $descripcion_productos);

        try {
            $stmt->execute();
            
            // Obtener el ID de la factura recién creada
            $id_factura = $conn->lastInsertId();
            
            // Registrar el movimiento en la tabla de log
            $descripcion_log = "Factura creada para Cliente ID: $id_cliente con Cotización ID: $id_cotizacion. Total: $total.";
            $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) VALUES (:userId, 'Creación de Factura', :descripcion)";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bindParam(':userId', $usuario_id);
            $stmt_log->bindParam(':descripcion', $descripcion_log);
            $stmt_log->execute();

            // Insertar un mensaje en la tabla mensajes
            $mensaje_texto = "Se ha creado la factura ID: $id_factura para el cliente ID: $id_cliente con el nombre '$nombre_cliente' en la fecha $fecha.";
            $tipo_mensaje = 'Facturas';
            $sql_mensaje = "INSERT INTO mensajes (Tipo_Mensaje, Mensaje, Fecha_Envio) VALUES (:tipo_mensaje, :mensaje_texto, :fecha)";
            $stmt_mensaje = $conn->prepare($sql_mensaje);
            $stmt_mensaje->bindParam(':tipo_mensaje', $tipo_mensaje);
            $stmt_mensaje->bindParam(':mensaje_texto', $mensaje_texto);
            $stmt_mensaje->bindParam(':fecha', $fecha);
            $stmt_mensaje->execute();
            
            $mensaje = 'Factura creada exitosamente.';
            $tipo_alerta = 'success';
        } catch (PDOException $e) {
            // Manejar el error de la base de datos
            $mensaje = "Error: " . $e->getMessage();
            $tipo_alerta = 'error';
        }
    } else {
        // Manejar el caso en que el cliente no existe o no se puede obtener la línea de crédito
        $mensaje = 'Error: Cliente no encontrado o no tiene línea de crédito.';
        $tipo_alerta = 'error';
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Generar Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
  <!-- Margen de tabla y menu lateral -->
  <div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Generar Factura</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="facturas.php">
        <div class="mb-3">
            <label for="cotizacion" class="form-label">Cotización</label>
            <select class="form-select" id="cotizacion" name="cotizacion" required>
                <option value="">Seleccione una cotización</option>
                <?php foreach ($cotizaciones as $cotizacion): ?>
                <option value="<?php echo $cotizacion['ID_Cotizacion']; ?>">
                    <?php echo htmlspecialchars($cotizacion['ClienteNombre']) . ' - ' . htmlspecialchars($cotizacion['Descripcion']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente</label>
            <select class="form-select" id="cliente" name="cliente" required>
                <option value="">Seleccione un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                <option value="<?php echo $cliente['ID_Cliente']; ?>">
                    <?php echo htmlspecialchars($cliente['Nombre']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" class="form-control" id="monto" name="monto" required oninput="calcularTotal()">
        </div>
        <div class="mb-3">
            <label for="impuesto" class="form-label">Impuesto (%)</label>
            <input type="number" step="0.01" class="form-control" id="impuesto" name="impuesto" required oninput="calcularTotal()">
        </div>
        <div class="mb-3">
            <label for="total" class="form-label">Total con IVA</label>
            <input type="number" step="0.01" class="form-control" id="total" name="total" readonly>
        </div>
        <div class="mb-3">
            <label for="tipo_cliente" class="form-label">Tipo de Cliente</label>
            <select class="form-select" id="tipo_cliente" name="tipo_cliente" required>
                <option value="Fisica">Persona Física</option>
                <option value="Moral">Persona Moral</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tipo_pago" class="form-label">Tipo de Pago</label>
            <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                <option value="transferencia">Transferencia</option>
                <option value="linea_credito">Línea de Crédito</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="descripcion_productos" class="form-label">Descripción de los Productos</label>
            <textarea class="form-control" id="descripcion_productos" name="descripcion_productos" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Generar Factura</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function calcularTotal() {
        var monto = parseFloat(document.getElementById('monto').value) || 0;
        var impuesto = parseFloat(document.getElementById('impuesto').value) || 0;
        var total = monto + (monto * impuesto / 100);
        document.getElementById('total').value = total.toFixed(2);
    }
</script>
</body>
</html>
