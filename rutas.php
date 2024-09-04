<?php
require 'conexion.php';
require 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$user_type_id = $_SESSION['userType'];
$username = $_SESSION['username'];
$usuario_id = $_SESSION['userId'];

// Inicializar una variable para determinar si la ruta fue guardada con éxito
$rutaGuardadaExito = false;

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado_origen = $_POST['estado_origen'];
    $municipio_origen = $_POST['municipio_origen'];
    $estado_destino = $_POST['estado_destino'];
    $municipio_destino = $_POST['municipio_destino'];
    $km = $_POST['km'];
    $cmantenimiento = $_POST['cmantenimiento'];
    $ccasetas = $_POST['ccasetas'];
    $cgasolina = $_POST['cgasolina'];

    // Preparar y ejecutar la consulta para insertar la ruta en la base de datos
    $sql = "INSERT INTO rutas (Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino, Km, CMantenimiento, CCasetas, CGasolina, Estatus) 
            VALUES (:estado_origen, :municipio_origen, :estado_destino, :municipio_destino, :km, :cmantenimiento, :ccasetas, :cgasolina, 'Disponible')";
    
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute([
            ':estado_origen' => $estado_origen,
            ':municipio_origen' => $municipio_origen,
            ':estado_destino' => $estado_destino,
            ':municipio_destino' => $municipio_destino,
            ':km' => $km,
            ':cmantenimiento' => $cmantenimiento,
            ':ccasetas' => $ccasetas,
            ':cgasolina' => $cgasolina
        ]);
        $rutaGuardadaExito = true; // Marcar como éxito
    } catch (PDOException $e) {
        echo "Error al agregar la ruta: " . $e->getMessage();
    }
}

// Verificar si se ha solicitado modificar una ruta
if (isset($_GET['action']) && $_GET['action'] === 'modificar' && isset($_GET['id'])) {
    $id_ruta = $_GET['id'];

    // Obtener los datos de la ruta específica para precargar en el formulario de modificación
    $sql = "SELECT * FROM rutas WHERE ID_Ruta = :id_ruta";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_ruta' => $id_ruta]);
    $ruta = $stmt->fetch(PDO::FETCH_ASSOC);
}


// Verificar si se ha solicitado modificar o cambiar el estatus de una ruta
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id_ruta = $_GET['id'];

    if ($action === 'modificar') {
        // Aquí puedes agregar la lógica para modificar una ruta
        // Podrías mostrar un formulario de edición similar al de agregar una nueva ruta
    } elseif (in_array($action, ['suspender', 'cancelar', 'disponible'])) {
        $nuevoEstatus = ($action === 'suspender') ? 'Suspender' : (($action === 'cancelar') ? 'Cancelado' : 'Disponible');
        $sql = "UPDATE rutas SET Estatus = :nuevoEstatus WHERE ID_Ruta = :id_ruta";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->execute([':nuevoEstatus' => $nuevoEstatus, ':id_ruta' => $id_ruta]);
        } catch (PDOException $e) {
            echo "Error al cambiar el estatus de la ruta: " . $e->getMessage();
        }
    }
}

// Obtener todas las rutas de la base de datos
$sql = "SELECT * FROM rutas";
$rutas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rutas</title>
    <!-- Estilos de Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
    <div class="container">
        <h1>Gestión de Rutas</h1>

        <!-- Botón para agregar una nueva ruta -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAgregarRuta">
            <i class="fas fa-plus"></i> Agregar Nueva Ruta
        </button>

        <!-- Tabla de rutas -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Estado de Origen</th>
                    <th>Municipio de Origen</th>
                    <th>Estado de Destino</th>
                    <th>Municipio de Destino</th>
                    <th>Kilómetros</th>
                    <th>Costo Mantenimiento</th>
                    <th>Costo Casetas</th>
                    <th>Costo Gasolina</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            
            <tbody>
    <?php foreach ($rutas as $ruta): ?>
        <tr>
            <td><?= htmlspecialchars($ruta['Estado_Origen']); ?></td>
            <td><?= htmlspecialchars($ruta['Municipio_Origen']); ?></td>
            <td><?= htmlspecialchars($ruta['Estado_Destino']); ?></td>
            <td><?= htmlspecialchars($ruta['Municipio_Destino']); ?></td>
            <td><?= htmlspecialchars($ruta['Km']); ?></td>
            <td><?= htmlspecialchars($ruta['CMantenimiento']); ?></td>
            <td><?= htmlspecialchars($ruta['CCasetas']); ?></td>
            <td><?= htmlspecialchars($ruta['CGasolina']); ?></td>
            <td><?= htmlspecialchars($ruta['Estatus']); ?></td>
            <td>
                <button type="button" class="btn btn-warning btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalModificarRuta" 
                        data-id="<?= $ruta['ID_Ruta']; ?>" 
                        data-estado-origen="<?= htmlspecialchars($ruta['Estado_Origen']); ?>"
                        data-municipio-origen="<?= htmlspecialchars($ruta['Municipio_Origen']); ?>"
                        data-estado-destino="<?= htmlspecialchars($ruta['Estado_Destino']); ?>"
                        data-municipio-destino="<?= htmlspecialchars($ruta['Municipio_Destino']); ?>"
                        data-km="<?= htmlspecialchars($ruta['Km']); ?>"
                        data-cmantenimiento="<?= htmlspecialchars($ruta['CMantenimiento']); ?>"
                        data-ccasetas="<?= htmlspecialchars($ruta['CCasetas']); ?>"
                        data-cgasolina="<?= htmlspecialchars($ruta['CGasolina']); ?>">
                    <i class="fas fa-edit"></i> 
                </button>
                <a href="?action=suspender&id=<?= $ruta['ID_Ruta']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> </a>
                <a href="?action=disponible&id=<?= $ruta['ID_Ruta']; ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i> </a>
                <a href="?action=cancelar&id=<?= $ruta['ID_Ruta']; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> </a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
        
    </div>

    <!-- Modal para agregar una nueva ruta -->
    <div class="modal fade" id="modalAgregarRuta" tabindex="-1" aria-labelledby="modalAgregarRutaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarRutaLabel">Agregar Nueva Ruta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="estado_origen">Estado de Origen:</label>
                            <input type="text" class="form-control" name="estado_origen" id="estado_origen" required>
                        </div>
                        <div class="form-group">
                            <label for="municipio_origen">Municipio de Origen:</label>
                            <input type="text" class="form-control" name="municipio_origen" id="municipio_origen" required>
                        </div>
                        <div class="form-group">
                            <label for="estado_destino">Estado de Destino:</label>
                            <input type="text" class="form-control" name="estado_destino" id="estado_destino" required>
                        </div>
                        <div class="form-group">
                            <label for="municipio_destino">Municipio de Destino:</label>
                            <input type="text" class="form-control" name="municipio_destino" id="municipio_destino" required>
                        </div>
                        <div class="form-group">
                            <label for="km">Kilómetros:</label>
                            <input type="number" step="0.01" class="form-control" name="km" id="km" required>
                        </div>
                        <div class="form-group">
                            <label for="cmantenimiento">Costo de Mantenimiento:</label>
                            <input type="number" step="0.01" class="form-control" name="cmantenimiento" id="cmantenimiento" required>
                        </div>
                        <div class="form-group">
                            <label for="ccasetas">Costo de Casetas:</label>
                            <input type="number" step="0.01" class="form-control" name="ccasetas" id="ccasetas" required>
                        </div>
                        <div class="form-group">
                            <label for="cgasolina">Costo de Gasolina:</label>
                            <input type="number" step="0.01" class="form-control" name="cgasolina" id="cgasolina" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Ruta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para modificar una ruta existente -->
<div class="modal fade" id="modalModificarRuta" tabindex="-1" aria-labelledby="modalModificarRutaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarRutaLabel">Modificar Ruta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="ruta_modificar.php">
                <div class="modal-body">
                    <input type="hidden" name="id_ruta" id="idRutaModificar">
                    <div class="mb-3">
                        <label for="estado_origen_modificar" class="form-label">Estado de Origen:</label>
                        <input type="text" class="form-control" name="estado_origen" id="estado_origen_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="municipio_origen_modificar" class="form-label">Municipio de Origen:</label>
                        <input type="text" class="form-control" name="municipio_origen" id="municipio_origen_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado_destino_modificar" class="form-label">Estado de Destino:</label>
                        <input type="text" class="form-control" name="estado_destino" id="estado_destino_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="municipio_destino_modificar" class="form-label">Municipio de Destino:</label>
                        <input type="text" class="form-control" name="municipio_destino" id="municipio_destino_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="km_modificar" class="form-label">Kilómetros:</label>
                        <input type="number" step="0.01" class="form-control" name="km" id="km_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="cmantenimiento_modificar" class="form-label">Costo de Mantenimiento:</label>
                        <input type="number" step="0.01" class="form-control" name="cmantenimiento" id="cmantenimiento_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="ccasetas_modificar" class="form-label">Costo de Casetas:</label>
                        <input type="number" step="0.01" class="form-control" name="ccasetas" id="ccasetas_modificar" required>
                    </div>
                    <div class="mb-3">
                        <label for="cgasolina_modificar" class="form-label">Costo de Gasolina:</label>
                        <input type="number" step="0.01" class="form-control" name="cgasolina" id="cgasolina_modificar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($_GET['exito'])): ?>
    <!-- Modal de éxito -->
    <div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExitoLabel">Éxito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Ruta modificada con éxito.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Script para mostrar el modal automáticamente -->
    <script>
        var exitoModal = new bootstrap.Modal(document.getElementById('modalExito'));
        exitoModal.show();
    </script>
<?php endif; ?>

    <!-- Mensaje de éxito al agregar una ruta -->
    <?php if ($rutaGuardadaExito): ?>
        <script>
            alert('Ruta agregada con éxito.');
        </script>
    <?php endif; ?>
</div>
<script>
// Código JavaScript para llenar el modal con los datos de la ruta seleccionada
document.addEventListener('DOMContentLoaded', function () {
    var modalModificarRuta = document.getElementById('modalModificarRuta');
    
    modalModificarRuta.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Botón que activó el modal
        var idRuta = button.getAttribute('data-id');
        var estadoOrigen = button.getAttribute('data-estado-origen');
        var municipioOrigen = button.getAttribute('data-municipio-origen');
        var estadoDestino = button.getAttribute('data-estado-destino');
        var municipioDestino = button.getAttribute('data-municipio-destino');
        var km = button.getAttribute('data-km');
        var cmantenimiento = button.getAttribute('data-cmantenimiento');
        var ccasetas = button.getAttribute('data-ccasetas');
        var cgasolina = button.getAttribute('data-cgasolina');

        // Actualiza los valores en el formulario del modal
        modalModificarRuta.querySelector('#idRutaModificar').value = idRuta;
        modalModificarRuta.querySelector('#estado_origen_modificar').value = estadoOrigen;
        modalModificarRuta.querySelector('#municipio_origen_modificar').value = municipioOrigen;
        modalModificarRuta.querySelector('#estado_destino_modificar').value = estadoDestino;
        modalModificarRuta.querySelector('#municipio_destino_modificar').value = municipioDestino;
        modalModificarRuta.querySelector('#km_modificar').value = km;
        modalModificarRuta.querySelector('#cmantenimiento_modificar').value = cmantenimiento;
        modalModificarRuta.querySelector('#ccasetas_modificar').value = ccasetas;
        modalModificarRuta.querySelector('#cgasolina_modificar').value = cgasolina;
    });
});

</script>


<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
