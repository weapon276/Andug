<?php
require 'conexion.php';
require 'index.php';

if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['userId'];
$sql = "SELECT * FROM viaje";
$result = $conn->query($sql);

// Obtener datos de camiones
$camiones_sql = "SELECT ID_Camion, Unidad FROM camion";
$camiones_result = $conn->query($camiones_sql);

// Obtener datos de operadores
$operadores_sql = "SELECT ID_Operador, Nombre FROM operador";
$operadores_result = $conn->query($operadores_sql);

// Obtener datos de clientes
$clientes_sql = "SELECT ID_Cliente, Nombre FROM cliente";
$clientes_result = $conn->query($clientes_sql);

// Obtener datos de rutas
$rutas_sql = "SELECT ID_Ruta, CONCAT(Estado_Origen, ' - ', Municipio_Origen, ' a ', Estado_Destino, ' - ', Municipio_Destino) AS Ruta FROM rutas";
$rutas_result = $conn->query($rutas_sql);

// Obtener datos de cotizaciones
$cotizaciones_sql = "SELECT ID_Cotizacion, Descripcion FROM cotizacion";
$cotizaciones_result = $conn->query($cotizaciones_sql);

// Verificar si se enviaron los datos del formulario
$id_viaje = isset($_POST['id_viaje']) ? $_POST['id_viaje'] : null;
$id_camion = isset($_POST['id_camion']) ? $_POST['id_camion'] : null;
$id_operador = isset($_POST['id_operador']) ? $_POST['id_operador'] : null;
$id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
$id_ruta = isset($_POST['id_ruta']) ? $_POST['id_ruta'] : null;
$id_cotizacion = isset($_POST['id_cotizacion']) ? $_POST['id_cotizacion'] : null;
$fecha_despacho = isset($_POST['fecha_despacho']) ? $_POST['fecha_despacho'] : null;
$fecha_llegada = isset($_POST['fecha_llegada']) ? $_POST['fecha_llegada'] : null;
$gastos = isset($_POST['gastos']) ? $_POST['gastos'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;
$contenedores = isset($_POST['contenedores']) ? $_POST['contenedores'] : null;

// Procesar la carga del archivo PDF de pedimento
$archivo_pedimento = null;
if (isset($_FILES['archivo_pedimento']) && $_FILES['archivo_pedimento']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['archivo_pedimento']['name'];
    $rutaTemporal = $_FILES['archivo_pedimento']['tmp_name'];
    $rutaDestino = 'pedimentos/' . uniqid() . '_' . $nombreArchivo;

    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        $archivo_pedimento = $rutaDestino;
    }
}

// Verificar que todos los valores obligatorios estén presentes
if ($id_viaje && $id_camion && $id_operador && $id_cliente && $id_ruta && $id_cotizacion && $fecha_despacho && $fecha_llegada && $gastos && $status && $contenedores) {
    // Actualizar datos en la base de datos
    $sql = "UPDATE viaje SET 
        ID_Camion = ?, 
        ID_Operador = ?, 
        ID_Cliente = ?, 
        Fk_IdRutas = ?, 
        Fk_IdCotizacion = ?, 
        Fecha_Despacho = ?, 
        Fecha_Llegada = ?, 
        Contenedores = ?, 
        Gastos = ?, 
        Status = ?";

    // Añadir archivo de pedimento si está presente
    if ($archivo_pedimento !== null) {
        $sql .= ", Archivo_Pedimento = ?";
        $params = [$id_camion, $id_operador, $id_cliente, $id_ruta, $id_cotizacion, $fecha_despacho, $fecha_llegada, $contenedores, $gastos, $status, $archivo_pedimento, $id_viaje];
    } else {
        $params = [$id_camion, $id_operador, $id_cliente, $id_ruta, $id_cotizacion, $fecha_despacho, $fecha_llegada, $contenedores, $gastos, $status, $id_viaje];
    }

    $sql .= " WHERE ID_Viaje = ?";

    // Ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Redirigir después de actualizar
    header("Location: gestion_viajes.php");
    exit();
} else {
    // Manejar el caso en el que faltan datos obligatorios
    echo "Error: Faltan campos obligatorios.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Viajes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
  <!-- Margen de tabla y menu lateral -->
  <div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>

<div class="container mt-4">
    <h2>Gestión de Viajes</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Viaje</th>
                <th>ID Camión</th>
                <th>ID Operador</th>
                <th>ID Cliente</th>
                <th>Ruta</th>
                <th>Cotización</th>
                <th>Fecha Despacho</th>
                <th>Fecha Llegada</th>
                <th>Pedimentos</th>
                <th>Contenedores</th>
                <th>Gastos</th>
                <th>Status</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ID_Viaje'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['ID_Camion'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['ID_Operador'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['ID_Cliente'] ?? ''); ?></td>
                <td>
                    <?php
                    $ruta_id = $row['Fk_IdRutas'];
                    $ruta_sql = "SELECT Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino FROM rutas WHERE ID_Ruta = ?";
                    $stmt = $conn->prepare($ruta_sql);
                    $stmt->execute([$ruta_id]);
                    $ruta_result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($ruta_result) {
                        echo htmlspecialchars($ruta_result['Estado_Origen'] . ' - ' . $ruta_result['Municipio_Origen'] . ' a ' . $ruta_result['Estado_Destino'] . ' - ' . $ruta_result['Municipio_Destino']);
                    } else {
                        echo 'Ruta no encontrada';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $cotizacion_id = $row['Fk_IdCotizacion'];
                    $cotizacion_sql = "SELECT Descripcion FROM cotizacion WHERE ID_Cotizacion = ?";
                    $stmt = $conn->prepare($cotizacion_sql);
                    $stmt->execute([$cotizacion_id]);
                    $cotizacion_result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($cotizacion_result) {
                        echo htmlspecialchars($cotizacion_result['Descripcion']);
                    } else {
                        echo 'Cotización no encontrada';
                    }
                    ?>
                    <td>
    <?php if (!empty($row['Archivo_Pedimento'])): ?>
        <a href="<?php echo $row['Archivo_Pedimento']; ?>" target="_blank">Ver Pedimento</a>
    <?php else: ?>
        No adjunto
    <?php endif; ?>
</td>

                </td>
                <td><?php echo htmlspecialchars($row['Fecha_Despacho'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['Fecha_Llegada'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['Pedimentos'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['Contenedores'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['Gastos'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['Status'] ?? ''); ?></td>
                <td>
                    <!-- Botón para abrir el modal de modificar -->
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modificarModal" 
                        data-id="<?php echo $row['ID_Viaje']; ?>"
                        data-camion="<?php echo htmlspecialchars($row['ID_Camion']); ?>"
                        data-operador="<?php echo htmlspecialchars($row['ID_Operador']); ?>"
                        data-cliente="<?php echo htmlspecialchars($row['ID_Cliente']); ?>"
                        data-fecha-despacho="<?php echo htmlspecialchars($row['Fecha_Despacho']); ?>"
                        data-fecha-llegada="<?php echo htmlspecialchars($row['Fecha_Llegada']); ?>"
                        data-pedimentos="<?php echo htmlspecialchars($row['Pedimentos']); ?>"
                        data-contenedores="<?php echo htmlspecialchars($row['Contenedores']); ?>"
                        data-gastos="<?php echo htmlspecialchars($row['Gastos']); ?>"
                        data-status="<?php echo htmlspecialchars($row['Status']); ?>"
                    >Modificar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal para modificar viaje -->
<div class="modal fade" id="modificarModal" tabindex="-1" aria-labelledby="modificarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modificarModalLabel">Modificar Viaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="actualizar_viaje.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_viaje" id="id_viaje">

                    <div class="mb-3">
                        <label for="id_camion" class="form-label">Camión</label>
                        <select class="form-select" id="id_camion" name="id_camion">
                            <?php while ($camion = $camiones_result->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $camion['ID_Camion']; ?>"><?php echo htmlspecialchars($camion['Unidad']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_operador" class="form-label">Operador</label>
                        <select class="form-select" id="id_operador" name="id_operador">
                            <?php while ($operador = $operadores_result->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $operador['ID_Operador']; ?>"><?php echo htmlspecialchars($operador['Nombre']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_cliente" class="form-label">Cliente</label>
                        <select class="form-select" id="id_cliente" name="id_cliente">
                            <?php while ($cliente = $clientes_result->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $cliente['ID_Cliente']; ?>"><?php echo htmlspecialchars($cliente['Nombre']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_ruta" class="form-label">Ruta</label>
                        <select class="form-select" id="id_ruta" name="id_ruta">
                            <?php while ($ruta = $rutas_result->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $ruta['ID_Ruta']; ?>"><?php echo htmlspecialchars($ruta['Ruta']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_cotizacion" class="form-label">Cotización</label>
                        <select class="form-select" id="id_cotizacion" name="id_cotizacion">
                            <?php while ($cotizacion = $cotizaciones_result->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $cotizacion['ID_Cotizacion']; ?>"><?php echo htmlspecialchars($cotizacion['Descripcion']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_despacho" class="form-label">Fecha Despacho</label>
                        <input type="date" class="form-control" id="fecha_despacho" name="fecha_despacho">
                    </div>

                    <div class="mb-3">
                        <label for="fecha_llegada" class="form-label">Fecha Llegada</label>
                        <input type="date" class="form-control" id="fecha_llegada" name="fecha_llegada">
                    </div>

                    <div class="mb-3">
                        <label for="pedimentos" class="form-label">Adjuntar Pedimento (PDF)</label>
                        <input type="file" class="form-control" id="archivo_pedimento" name="archivo_pedimento" accept="application/pdf">
                    </div>

                    <div class="mb-3">
                        <label for="contenedores" class="form-label">Detalles de Contenedores</label>
                        <textarea class="form-control" id="contenedores" name="contenedores" placeholder="Ejemplo: 2 contenedores de 40 pies, 1 full"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="gastos" class="form-label">Gastos</label>
                        <input type="text" class="form-control" id="gastos" name="gastos">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" name="status">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal de confirmación de actualización exitosa -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmacionLabel">Actualización exitosa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Los cambios en el viaje se han actualizado correctamente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
 var modificarModal = document.getElementById('modificarModal');
modificarModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var idViaje = button.getAttribute('data-id');
    var idCamion = button.getAttribute('data-camion');
    var idOperador = button.getAttribute('data-operador');
    var idCliente = button.getAttribute('data-cliente');
    var fechaDespacho = button.getAttribute('data-fecha-despacho');
    var fechaLlegada = button.getAttribute('data-fecha-llegada');
    var pedimentos = button.getAttribute('data-pedimentos');
    var contenedores = button.getAttribute('data-contenedores');
    var gastos = button.getAttribute('data-gastos');
    var status = button.getAttribute('data-status');

    var idViajeInput = modificarModal.querySelector('#id_viaje');
    var idCamionSelect = modificarModal.querySelector('#id_camion');
    var idOperadorSelect = modificarModal.querySelector('#id_operador');
    var idClienteSelect = modificarModal.querySelector('#id_cliente');
    var fechaDespachoInput = modificarModal.querySelector('#fecha_despacho');
    var fechaLlegadaInput = modificarModal.querySelector('#fecha_llegada');
    var pedimentosInput = modificarModal.querySelector('#pedimentos');
    var contenedoresInput = modificarModal.querySelector('#contenedores');
    var gastosInput = modificarModal.querySelector('#gastos');
    var statusInput = modificarModal.querySelector('#status');

    idViajeInput.value = idViaje;
    idCamionSelect.value = idCamion;
    idOperadorSelect.value = idOperador;
    idClienteSelect.value = idCliente;
    fechaDespachoInput.value = fechaDespacho;
    fechaLlegadaInput.value = fechaLlegada;
    pedimentosInput.value = pedimentos;
    contenedoresInput.value = contenedores;
    gastosInput.value = gastos;
    statusInput.value = status;
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Escuchar el evento de envío del formulario
    $("form").on("submit", function(event) {
        event.preventDefault(); // Prevenir la redirección
        
        // Recopilar los datos del formulario
        var formData = new FormData(this);
        
        // Enviar los datos vía AJAX
        $.ajax({
            url: "actualizar_viaje.php", // Archivo PHP que maneja la actualización
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Abrir el modal de confirmación
                $('#modalConfirmacion').modal('show');
            },
            error: function() {
                alert("Hubo un error al actualizar el viaje. Inténtelo de nuevo.");
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
