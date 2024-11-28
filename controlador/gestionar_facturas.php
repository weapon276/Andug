<?php
include '../modelo/conexion.php';
include '../index.php';
session_start(); // Asegúrate de iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType'])) {
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

// Manejar las actualizaciones de estado de la factura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_estado'])) {
    $id_factura = $_POST['id_factura'];
    $nuevo_estado = $_POST['nuevo_estado'];
    $fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : null;
    $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : null;

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
        $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

        // Actualizar el estado de la factura
        $sql = "UPDATE factura SET Estado_Pago = :nuevo_estado, fecha_final = :fecha_final WHERE ID_Factura = :id_factura";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado);
        $stmt->bindParam(':fecha_final', $fecha_final);
        $stmt->bindParam(':id_factura', $id_factura);
        $stmt->execute();
    
        // Registro en log_movimientos si hay comentario
        if ($comentario) {
            $accion = ($nuevo_estado == 'Cancelado') ? 'Factura Cancelada' : 'Prorroga de Factura';
            $descripcion = $comentario;
            $user_id = $_SESSION['userId']; // Asegúrate de que la sesión está iniciada y el userId está disponible
    
            $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) VALUES (:user_id, :accion, :descripcion)";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bindParam(':user_id', $user_id);
            $stmt_log->bindParam(':accion', $accion);
            $stmt_log->bindParam(':descripcion', $descripcion);
            $stmt_log->execute();
        }
    
    
    }
 // Redirigir después de realizar las operaciones
 exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestionar Facturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    <!-- Campo de búsqueda en tiempo real -->
    <input type="text" id="buscarFactura" class="form-control mb-3" placeholder="Buscar factura por nombre...">

    <!-- Tabla de facturas -->
    <table class="table table-bordered" id="tablaFacturas">
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
  <!-- Botón de Pago -->
<form method="POST" action="gestionar_facturas.php" style="display: inline;">
    <input type="hidden" name="id_factura" value="<?php echo htmlspecialchars($factura['ID_Factura']); ?>">
    <input type="hidden" name="nuevo_estado" value="Pagado">
    <button type="submit" name="update_estado" class="btn btn-success btn-sm" title="Marcar como Pagado">
        <i class="bi bi-credit-card"></i> <!-- Icono de tarjeta de crédito para representar el pago -->
    </button>
</form>

<!-- Botón de Prórroga -->
<button type="button" class="btn btn-warning btn-sm" onclick="abrirModalProrroga('<?php echo $factura['ID_Factura']; ?>')" title="Solicitar Prórroga">
    <i class="bi bi-calendar-plus"></i> <!-- Icono de calendario con un símbolo de más para representar la prórroga -->
</button>

<!-- Botón de Cancelar -->
<button type="button" class="btn btn-danger btn-sm" onclick="abrirModalCancelar('<?php echo $factura['ID_Factura']; ?>')" title="Cancelar Factura">
    <i class="bi bi-x-circle"></i> <!-- Icono de círculo con una cruz para representar la cancelación -->
</button>

<!-- Botón de Descargar PDF -->
<a href="descargar_pdf.php?id_factura=<?php echo $factura['ID_Factura']; ?>" class="btn btn-info btn-sm" title="Descargar PDF">
    <i class="bi bi-file-earmark-pdf"></i> <!-- Icono de archivo PDF para representar la descarga del PDF -->
</a>


            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para Cancelar Factura -->
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="gestionar_facturas.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCancelarLabel">Cancelar Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_factura" id="id_factura_cancelar">
                    <input type="hidden" name="nuevo_estado" value="Cancelado">
                    <div class="mb-3">
                        <label for="comentario_cancelar" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comentario_cancelar" name="comentario"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="update_estado" class="btn btn-danger">Confirmar Cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Prorroga de Factura -->
<div class="modal fade" id="modalProrroga" tabindex="-1" aria-labelledby="modalProrrogaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="gestionar_facturas.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProrrogaLabel">Prorroga de Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_factura" id="id_factura_prorroga">
                    <input type="hidden" name="nuevo_estado" value="Prorroga">
                    <div class="mb-3">
                        <label for="fecha_final" class="form-label">Fecha de Prórroga</label>
                        <input type="date" class="form-control" id="fecha_final" name="fecha_final">
                    </div>
                    <div class="mb-3">
                        <label for="comentario_prorroga" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comentario_prorroga" name="comentario"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="update_estado" class="btn btn-warning">Confirmar Prórroga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    // Función para abrir modal de cancelar factura
    function abrirModalCancelar(idFactura) {
        $('#id_factura_cancelar').val(idFactura);
        $('#modalCancelar').modal('show');
    }

    // Función para abrir modal de prórroga de factura
    function abrirModalProrroga(idFactura) {
        $('#id_factura_prorroga').val(idFactura);
        $('#modalProrroga').modal('show');
    }

    
    $(document).ready(function () {
        // Búsqueda en tiempo real
        $("#buscarFactura").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#tablaFacturas tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });


</script>
</body>
</html>
