<?php
require 'conexion.php'; 
include 'index.php';


// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_camion'])) {
        // Agregar camión
        $placas = $_POST['placas'];
        $peso = $_POST['peso'];
        $unidad = $_POST['unidad'];
        $tipo = $_POST['tipo'];
        $poliza_seguro = $_POST['poliza_seguro'];
        $gps = $_POST['gps'];

        $sql = "INSERT INTO camion (Placas, Peso, Unidad, Tipo, Poliza_Seguro, GPS, Status, fecha_inicio) 
                VALUES (:placas, :peso, :unidad, :tipo, :poliza_seguro, :gps, 'Libre', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':placas', $placas);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':poliza_seguro', $poliza_seguro);
        $stmt->bindParam(':gps', $gps);
        $stmt->execute();

        $mensaje = 'Camión agregado con éxito.';
        $tipo_mensaje = 'success';
    } elseif (isset($_POST['modificar_camion'])) {
        // Modificar camión
        $id_camion = $_POST['id_camion'];
        $placas = $_POST['placas'];
        $peso = $_POST['peso'];
        $unidad = $_POST['unidad'];
        $tipo = $_POST['tipo'];
        $poliza_seguro = $_POST['poliza_seguro'];
        $gps = $_POST['gps'];

        $sql = "UPDATE camion 
                SET Placas = :placas, Peso = :peso, Unidad = :unidad, Tipo = :tipo, Poliza_Seguro = :poliza_seguro, GPS = :gps
                WHERE ID_Camion = :id_camion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':placas', $placas);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':poliza_seguro', $poliza_seguro);
        $stmt->bindParam(':gps', $gps);
        $stmt->bindParam(':id_camion', $id_camion);
        $stmt->execute();

        $mensaje = 'Camión modificado con éxito.';
        $tipo_mensaje = 'success';
    } elseif (isset($_POST['accion_camion'])) {
        // Suspender o dar de baja camión
        $id_camion = $_POST['id_camion'];
        $comentario = $_POST['comentario'];
        $accion = $_POST['accion_camion'];

        if ($accion === 'suspender') {
            $status = 'Suspendido';
        } elseif ($accion === 'dar_de_baja') {
            $status = 'Baja';
        } elseif ($accion === 'mantenimiento') {
            $status = 'Mantenimiento';
        }

        $sql = "UPDATE camion 
                SET Status = :status, fecha_final = NOW()
                WHERE ID_Camion = :id_camion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id_camion', $id_camion);
        $stmt->execute();

        // Insertar comentario
        $sql_comentario = "INSERT INTO comentarios_camion (ID_Camion, Comentario) VALUES (:id_camion, :comentario)";
        $stmt_comentario = $conn->prepare($sql_comentario);
        $stmt_comentario->bindParam(':id_camion', $id_camion);
        $stmt_comentario->bindParam(':comentario', $comentario);
        $stmt_comentario->execute();

        $mensaje = 'Acción realizada con éxito.';
        $tipo_mensaje = 'success';
    }
}


// Código para manejar el mantenimiento del camión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_camion']) && $_POST['accion_camion'] === 'mantenimiento') {
    $id_camion = $_POST['id_camion'];
    $comentario = $_POST['comentario'];
    $fecha_inicio = $_POST['fecha_inicio'];
    
    // Si la fecha de fin no se proporciona, establecerla como NULL
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : NULL;

    // Actualizar el camión para establecer el estado y las fechas de mantenimiento
    $sql = "UPDATE camion 
            SET Status = 'Mantenimiento', FechaIM = :fecha_inicio, FechaFM = :fecha_fin, ComentarioMantenimiento = :comentario
            WHERE ID_Camion = :id_camion";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR); // Asegúrate de pasar el tipo de dato correcto
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':id_camion', $id_camion);
    
    try {
        $stmt->execute();
        // Redirigir a la página de gestión de camiones con un mensaje de éxito
        header("Location: gestionar_camiones.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Mostrar error en caso de fallo
    }
}

// Consultar camiones disponibles
$sql_disponibles = "SELECT * FROM camion WHERE Status = 'Libre' OR Status = 'Ocupado' OR Status = 'Mantenimiento'";
$camiones_disponibles = $conn->query($sql_disponibles)->fetchAll(PDO::FETCH_ASSOC);

// Consultar camiones suspendidos o dados de baja
$sql_no_activos = "SELECT * FROM camion WHERE Status = 'Suspendido' OR Status = 'Baja'";
$camiones_no_activos = $conn->query($sql_no_activos)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Camiones</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
    <div class="container mt-4">
        <h1>Gestión de Camiones</h1>

        <!-- Mensajes -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Botón para agregar camión -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregarCamion">
            Agregar Camión
        </button>

        <!-- Tabla de camiones disponibles -->
        <h3>Camiones Disponibles</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Placas</th>
                    <th>Peso</th>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Poliza de Seguro</th>
                    <th>GPS</th>
                    <th>Status</th>
                    <th>Fecha Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($camiones_disponibles as $camion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($camion['Placas']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Peso']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Unidad']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Tipo']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Poliza_Seguro']); ?></td>
                        <td><?php echo htmlspecialchars($camion['GPS']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Status']); ?></td>
                        <td><?php echo htmlspecialchars($camion['fecha_inicio']); ?></td>
                        <td>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalMantenimientoCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                        <i class="fa fa-tools"></i> 
                    </button>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuspenderCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                                <i class="fa fa-pause"></i>
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDarDeBajaCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                                <i class="fa fa-trash"></i> 
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalModificarCamion" data-id="<?php echo $camion['ID_Camion']; ?>"
                                data-placas="<?php echo $camion['Placas']; ?>" data-peso="<?php echo $camion['Peso']; ?>" data-unidad="<?php echo $camion['Unidad']; ?>"
                                data-tipo="<?php echo $camion['Tipo']; ?>" data-poliza_seguro="<?php echo $camion['Poliza_Seguro']; ?>" data-gps="<?php echo $camion['GPS']; ?>">
                                <i class="fa fa-edit"></i> 
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabla de camiones suspendidos o dados de baja -->
        <h3>Camiones fuera de servicio</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Placas</th>
                    <th>Peso</th>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Poliza de Seguro</th>
                    <th>GPS</th>
                    <th>Status</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($camiones_no_activos as $camion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($camion['Placas']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Peso']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Unidad']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Tipo']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Poliza_Seguro']); ?></td>
                        <td><?php echo htmlspecialchars($camion['GPS']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Status']); ?></td>
                        <td><?php echo htmlspecialchars($camion['fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($camion['fecha_final']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Agregar Camión -->
    <div class="modal fade" id="modalAgregarCamion" tabindex="-1" aria-labelledby="modalAgregarCamionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarCamionLabel">Agregar Camión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gestion_camiones.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="placas" class="form-label">Placas</label>
                            <input type="text" class="form-control" id="placas" name="placas" required>
                        </div>
                        <div class="mb-3">
                            <label for="peso" class="form-label">Peso</label>
                            <input type="number" step="0.01" class="form-control" id="peso" name="peso" required>
                        </div>
                        <div class="mb-3">
                            <label for="unidad" class="form-label">Unidad</label>
                            <input type="text" class="form-control" id="unidad" name="unidad" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo" name="tipo">
                        </div>
                        <div class="mb-3">
                            <label for="poliza_seguro" class="form-label">Poliza de Seguro</label>
                            <input type="text" class="form-control" id="poliza_seguro" name="poliza_seguro">
                        </div>
                        <div class="mb-3">
                            <label for="gps" class="form-label">GPS</label>
                            <input type="text" class="form-control" id="gps" name="gps">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="agregar_camion">Agregar Camión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Modificar Camión -->
    <div class="modal fade" id="modalModificarCamion" tabindex="-1" aria-labelledby="modalModificarCamionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModificarCamionLabel">Modificar Camión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gestion_camiones.php" method="post">
                    <input type="hidden" id="id_camion_modificar" name="id_camion">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="placas_modificar" class="form-label">Placas</label>
                            <input type="text" class="form-control" id="placas_modificar" name="placas" required>
                        </div>
                        <div class="mb-3">
                            <label for="peso_modificar" class="form-label">Peso</label>
                            <input type="number" step="0.01" class="form-control" id="peso_modificar" name="peso" required>
                        </div>
                        <div class="mb-3">
                            <label for="unidad_modificar" class="form-label">Unidad</label>
                            <input type="text" class="form-control" id="unidad_modificar" name="unidad" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_modificar" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo_modificar" name="tipo">
                        </div>
                        <div class="mb-3">
                            <label for="poliza_seguro_modificar" class="form-label">Poliza de Seguro</label>
                            <input type="text" class="form-control" id="poliza_seguro_modificar" name="poliza_seguro">
                        </div>
                        <div class="mb-3">
                            <label for="gps_modificar" class="form-label">GPS</label>
                            <input type="text" class="form-control" id="gps_modificar" name="gps">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="modificar_camion">Modificar Camión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Suspender Camión -->
    <div class="modal fade" id="modalSuspenderCamion" tabindex="-1" aria-labelledby="modalSuspenderCamionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSuspenderCamionLabel">Suspender Camión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gestion_camiones.php" method="post">
                    <input type="hidden" id="id_camion_suspender" name="id_camion">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="comentario_suspender" class="form-label">Comentario</label>
                            <textarea class="form-control" id="comentario_suspender" name="comentario" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-warning" name="accion_camion" value="suspender">Suspender Camión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dar de Baja Camión -->
    <div class="modal fade" id="modalDarDeBajaCamion" tabindex="-1" aria-labelledby="modalDarDeBajaCamionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDarDeBajaCamionLabel">Dar de Baja Camión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gestion_camiones.php" method="post">
                    <input type="hidden" id="id_camion_dar_baja" name="id_camion">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="comentario_dar_baja" class="form-label">Comentario</label>
                            <textarea class="form-control" id="comentario_dar_baja" name="comentario" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger" name="accion_camion" value="dar_de_baja">Dar de Baja Camión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Mantenimiento Camión -->
<div class="modal fade" id="modalMantenimientoCamion" tabindex="-1" aria-labelledby="modalMantenimientoCamionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMantenimientoCamionLabel">Registrar Mantenimiento Camión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="gestion_camiones.php" method="post">
                <input type="hidden" id="id_camion_mantenimiento" name="id_camion">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comentario_mantenimiento" class="form-label">Comentario</label>
                        <textarea class="form-control" id="comentario_mantenimiento" name="comentario" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio_mantenimiento" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio_mantenimiento" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin_mantenimiento" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin_mantenimiento" name="fecha_fin">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-warning" name="accion_camion" value="mantenimiento">Registrar Mantenimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Scripts para los modales
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id_camion = button.getAttribute('data-id');
            var modalBodyInput = modal.querySelector('input[name="id_camion"]');
            if (modalBodyInput) {
                modalBodyInput.value = id_camion;
            }

            var placasInput = modal.querySelector('input[name="placas"]');
            var pesoInput = modal.querySelector('input[name="peso"]');
            var unidadInput = modal.querySelector('input[name="unidad"]');
            var tipoInput = modal.querySelector('input[name="tipo"]');
            var polizaInput = modal.querySelector('input[name="poliza_seguro"]');
            var gpsInput = modal.querySelector('input[name="gps"]');

            if (placasInput) {
                placasInput.value = button.getAttribute('data-placas') || '';
                pesoInput.value = button.getAttribute('data-peso') || '';
                unidadInput.value = button.getAttribute('data-unidad') || '';
                tipoInput.value = button.getAttribute('data-tipo') || '';
                polizaInput.value = button.getAttribute('data-poliza_seguro') || '';
                gpsInput.value = button.getAttribute('data-gps') || '';
            }
        });
    });
</script>
</body>
</html>
