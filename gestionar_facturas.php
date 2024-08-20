<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Función para obtener todas las facturas
function obtenerFacturas($conn) {
    $sql = "SELECT f.*, c.Nombre as ClienteNombre, c.Tipo as TipoCliente, c.ID_Cliente, c.Linea_Credito 
            FROM factura f 
            JOIN cotizacion co ON f.ID_Cotizacion = co.ID_Cotizacion
            JOIN cliente c ON co.ID_Cliente = c.ID_Cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$facturas = obtenerFacturas($conn);

// Función para actualizar el estado de pago y ajustar la línea de crédito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_estado'])) {
    $id_factura = $_POST['id_factura'];
    $nuevo_estado = $_POST['nuevo_estado'];
    $fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : null;

    // Obtener la factura y el cliente correspondiente
    $sql = "SELECT f.*, c.ID_Cliente, c.Linea_Credito, c.Tipo FROM factura f 
            JOIN cotizacion co ON f.ID_Cotizacion = co.ID_Cotizacion
            JOIN cliente c ON co.ID_Cliente = c.ID_Cliente 
            WHERE f.ID_Factura = :id_factura";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_factura', $id_factura);
    $stmt->execute();
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($factura) {
        $id_cliente = $factura['ID_Cliente'];
        $linea_credito = $factura['Linea_Credito'];
        $total = $factura['Total'];

        // Si el nuevo estado es "Pagado", ajustar la línea de crédito del cliente
        if ($nuevo_estado === 'Pagado' && $factura['Tipo_Pago'] === 'linea_credito') {
            $nueva_linea_credito = $linea_credito + $total;

            $sql = "UPDATE cliente SET Linea_Credito = :nueva_linea_credito WHERE ID_Cliente = :id_cliente";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nueva_linea_credito', $nueva_linea_credito);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->execute();
        }

        // Actualizar el estado de la factura
        $sql = "UPDATE factura SET Estado_Pago = :nuevo_estado, fecha_final = :fecha_final WHERE ID_Factura = :id_factura";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado);
        $stmt->bindParam(':fecha_final', $fecha_final);
        $stmt->bindParam(':id_factura', $id_factura);
        $stmt->execute();
    }

    header("Location: gestionar_facturas.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Facturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table tr td {
            vertical-align: middle;
        }
        .estado-pagado {
            background-color: #d4edda;
            color: #155724;
        }
        .estado-no-pagado {
            background-color: #f8d7da;
            color: #721c24;
        }
        .estado-prorroga {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestionar Facturas</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Factura</th>
                <th>Cliente</th>
                <th>Tipo Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th>Tipo Pago</th>
                <th>Estado Pago</th>
                <th>Fecha Compromiso Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facturas as $factura): 
                $estado_class = '';
                if ($factura['Estado_Pago'] == 'Pagado') {
                    $estado_class = 'estado-pagado';
                } elseif ($factura['Estado_Pago'] == 'No Pagado') {
                    $estado_class = 'estado-no-pagado';
                } elseif ($factura['Estado_Pago'] == 'Prorroga') {
                    $estado_class = 'estado-prorroga';
                }
            ?>
            <tr class="<?php echo $estado_class; ?>">
                <td><?php echo $factura['ID_Factura']; ?></td>
                <td><?php echo htmlspecialchars($factura['ClienteNombre']); ?></td>
                <td><?php echo htmlspecialchars($factura['TipoCliente']); ?></td>
                <td><?php echo $factura['Fecha']; ?></td>
                <td><?php echo $factura['Monto']; ?></td>
                <td><?php echo $factura['Impuesto']; ?></td>
                <td><?php echo $factura['Total']; ?></td>
                <td><?php echo $factura['Tipo_Pago']; ?></td>
                <td><?php echo $factura['Estado_Pago']; ?></td>
                <td><?php echo $factura['fecha_final']; ?></td>
                <td>
                    <form method="POST" action="gestionar_facturas.php" style="display: inline;">
                        <input type="hidden" name="id_factura" value="<?php echo $factura['ID_Factura']; ?>">
                        <input type="hidden" name="nuevo_estado" value="Pagado">
                        <button type="submit" name="update_estado" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                    <form method="POST" action="gestionar_facturas.php" style="display: inline;">
                        <input type="hidden" name="id_factura" value="<?php echo $factura['ID_Factura']; ?>">
                        <input type="hidden" name="nuevo_estado" value="Prorroga">
                        <input type="hidden" name="fecha_final" value="<?php echo date('Y-m-d', strtotime('+1 week')); ?>">
                        <button type="submit" name="update_estado" class="btn btn-warning btn-sm">
                            <i class="bi bi-clock-fill"></i>
                        </button>
                    </form>
                    <form method="POST" action="gestionar_facturas.php" style="display: inline;">
                        <input type="hidden" name="id_factura" value="<?php echo $factura['ID_Factura']; ?>">
                        <input type="hidden" name="nuevo_estado" value="No Pagado">
                        <button type="submit" name="update_estado" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
