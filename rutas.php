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
    $sql = "INSERT INTO rutas (Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino, Km, CMantenimiento, CCasetas, CGasolina) 
            VALUES (:estado_origen, :municipio_origen, :estado_destino, :municipio_destino, :km, :cmantenimiento, :ccasetas, :cgasolina)";
    
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
                        <td>
                            <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Modificar</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Suspender</button>
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

    <!-- Modal de confirmación de ruta guardada -->
    <div class="modal fade" id="modalRutaGuardada" tabindex="-1" role="dialog" aria-labelledby="modalRutaGuardadaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRutaGuardadaLabel">Confirmación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Ruta guardada con éxito.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Mostrar el modal de confirmación si la ruta fue guardada -->
<?php if ($rutaGuardadaExito): ?>
<script>
    $(document).ready(function(){
        $('#modalRutaGuardada').modal('show');
    });
</script>
<?php endif; ?>

</body>
</html>
