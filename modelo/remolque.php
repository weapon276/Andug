<?php
include 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté configurada correctamente.
include '../index.php';
session_start(); // Asegúrate de iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Si se envía el formulario para agregar un nuevo remolque
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_remolque'])) {
    $tipo_remolque = $_POST['tipo_remolque'];
    $subtipo_remolque = $_POST['subtipo_remolque'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $año = $_POST['año'];
    $placas = $_POST['placas'];
    $capacidad_carga = $_POST['capacidad_carga'];
    $dimensiones = $_POST['dimensiones'];
    $observaciones = $_POST['observaciones'];

    // Inserta el nuevo remolque en la base de datos usando declaraciones preparadas
    $sql = "INSERT INTO remolque (tipo_remolque, subtipo_remolque, marca, modelo, año, placas, capacidad_carga, dimensiones, estado, observaciones) 
            VALUES (:tipo_remolque, :subtipo_remolque, :marca, :modelo, :año, :placas, :capacidad_carga, :dimensiones, 'en servicio', :observaciones)";

    $stmt = $conn->prepare($sql);
    
    // Vincula los parámetros
    $stmt->bindParam(':tipo_remolque', $tipo_remolque);
    $stmt->bindParam(':subtipo_remolque', $subtipo_remolque);
    $stmt->bindParam(':marca', $marca);
    $stmt->bindParam(':modelo', $modelo);
    $stmt->bindParam(':año', $año, PDO::PARAM_INT);
    $stmt->bindParam(':placas', $placas);
    $stmt->bindParam(':capacidad_carga', $capacidad_carga, PDO::PARAM_INT);
    $stmt->bindParam(':dimensiones', $dimensiones);
    $stmt->bindParam(':observaciones', $observaciones);

    // Ejecutar la consulta y manejar errores
    try {
        if ($stmt->execute()) {
            $mensaje_exito = "Remolque registrado con éxito";
        } else {
            // En caso de que execute() no devuelva true
            echo "Error: No se pudo registrar el remolque.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Muestra el mensaje de error
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Gestión de Remolques</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
 <!-- Margen de tabla y menu lateral -->
 <div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestión de Remolques</h1>

    <!-- Botón para abrir el modal de agregar remolque -->
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAgregarRemolque">
        Agregar nuevo remolque
    </button>

   <!-- Tabla que muestra los remolques -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Subtipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Año</th>
            <th>Placas</th>
            <th>Capacidad de carga</th>
            <th>Dimensiones</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>Fecha de alta</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Consulta para obtener los remolques de la base de datos
        $sql = "SELECT * FROM remolque";
        $stmt = $conn->query($sql);

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Determinar la clase según el estado (Status)
                $statusClass = '';
                switch (strtolower($row['estado'])) { // Aseguramos el manejo insensible a mayúsculas/minúsculas
                    case 'en curso':
                        $statusClass = 'table-primary'; // Azul
                        break;
                    case 'en servicio':
                        $statusClass = 'table-success'; // Verde
                        break;
                    case 'dado de baja':
                        $statusClass = 'table-danger'; // Rojo
                        break;
                    case 'suspendido':
                        $statusClass = 'table-warning'; // Amarillo
                        break;
                    default:
                        $statusClass = ''; // Sin clase adicional
                }
                // Mostrar la fila con la clase asignada
                echo "<tr class='{$statusClass}'>
                        <td>{$row['id_remolque']}</td>
                        <td>{$row['tipo_remolque']}</td>
                        <td>{$row['subtipo_remolque']}</td>
                        <td>{$row['marca']}</td>
                        <td>{$row['modelo']}</td>
                        <td>{$row['año']}</td>
                        <td>{$row['placas']}</td>
                        <td>{$row['capacidad_carga']}</td>
                        <td>{$row['dimensiones']}</td>
                        <td>{$row['estado']}</td>
                        <td>{$row['observaciones']}</td>
                        <td>{$row['fecha_alta']}</td>
                      </tr>";
            }
        } else {
            // En caso de que no haya registros
            echo "<tr><td colspan='12' class='text-center'>No hay remolques registrados</td></tr>";
        }
        ?>
    </tbody>
</table>

</div>

<!-- Modal para agregar un nuevo remolque -->
<div class="modal fade" id="modalAgregarRemolque" tabindex="-1" aria-labelledby="modalAgregarRemolqueLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarRemolqueLabel">Agregar nuevo remolque</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tipo_remolque">Tipo de Remolque</label>
                        <input type="text" class="form-control" id="tipo_remolque" name="tipo_remolque" required>
                    </div>
                    <div class="form-group">
                        <label for="subtipo_remolque">Subtipo de Remolque</label>
                        <input type="text" class="form-control" id="subtipo_remolque" name="subtipo_remolque" required>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="form-group">
                        <label for="año">Año</label>
                        <input type="number" class="form-control" id="año" name="año" required>
                    </div>
                    <div class="form-group">
                        <label for="placas">Placas</label>
                        <input type="text" class="form-control" id="placas" name="placas" required>
                    </div>
                    <div class="form-group">
                        <label for="capacidad_carga">Capacidad de carga (kg)</label>
                        <input type="number" step="0.01" class="form-control" id="capacidad_carga" name="capacidad_carga" required>
                    </div>
                    <div class="form-group">
                        <label for="dimensiones">Dimensiones</label>
                        <input type="text" class="form-control" id="dimensiones" name="dimensiones" required>
                    </div>
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="agregar_remolque" class="btn btn-primary">Registrar Remolque</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de éxito -->
<?php if (isset($mensaje_exito)): ?>
    <div class="modal fade show" id="modalExito" tabindex="-1" role="dialog" aria-labelledby="modalExitoLabel" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExitoLabel">Éxito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $mensaje_exito ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Mostrar el modal de éxito si hay un mensaje -->
<?php if (isset($mensaje_exito)): ?>
<script>
    $('#modalExito').modal('show');
</script>
<?php endif; ?>
</body>
</html>