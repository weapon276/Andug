<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

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
    
    // Obtener el ID del usuario que está creando la factura
    $id_empleado = $_SESSION['user_id'];

    // Calcular el total con el impuesto aplicado
    $total = $monto + ($monto * $impuesto / 100);

    // Obtener la línea de crédito del cliente
    $sql_credito = "SELECT Linea_Credito FROM cliente WHERE ID_Cliente = :id_cliente";
    $stmt_credito = $conn->prepare($sql_credito);
    $stmt_credito->bindParam(':id_cliente', $id_cliente);
    $stmt_credito->execute();
    $cliente = $stmt_credito->fetch(PDO::FETCH_ASSOC);

    $credito_disponible = $cliente['Linea_Credito'];

    // Verificar y descontar la línea de crédito
    if ($tipo_pago == 'linea_credito') {
        if ($credito_disponible >= $total) {
            $nuevo_credito = $credito_disponible - $total;
        } else {
            $nuevo_credito = 0;
            $diferencia = $total - $credito_disponible;
            echo "El crédito disponible es insuficiente. El resto ($diferencia) se cubrirá con otro tipo de pago.";
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
            VALUES (:id_cotizacion, :id_cliente, :id_empleado, :fecha, :monto, :impuesto, :total, :tipo_cliente, :tipo_pago, :descripcion_productos)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cotizacion', $id_cotizacion);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':id_empleado', $id_empleado);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':impuesto', $impuesto);
    $stmt->bindParam(':total', $total);
    $stmt->bindParam(':tipo_cliente', $tipo_cliente);
    $stmt->bindParam(':tipo_pago', $tipo_pago);
    $stmt->bindParam(':descripcion_productos', $descripcion_productos);
    $stmt->execute();

    header("Location: facturas.php");
    exit();
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
