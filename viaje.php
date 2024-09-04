<?php
session_start();
require 'conexion.php'; // Asegúrate de tener un archivo de conexión a la base de datos.
// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Consulta para obtener todos los viajes
$sql = "SELECT * FROM viaje";
$result = $conn->query($sql);
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
                    <td><?php echo htmlspecialchars($row['ID_Viaje']); ?></td>
                    <td><?php echo htmlspecialchars($row['ID_Camion']); ?></td>
                    <td><?php echo htmlspecialchars($row['ID_Operador']); ?></td>
                    <td><?php echo htmlspecialchars($row['ID_Cliente']); ?></td>
                    <td><?php
                        $ruta_id = $row['Fk_IdRutas'];
                        $ruta_sql = "SELECT Estado_Origen, Municipio_Origen, Estado_Destino, Municipio_Destino FROM rutas WHERE ID_Ruta = ?";
                        $stmt = $conn->prepare($ruta_sql);
                        $stmt->execute([$ruta_id]);
                        $ruta_result = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo htmlspecialchars($ruta_result['Estado_Origen'] . ' - ' . $ruta_result['Municipio_Origen'] . ' a ' . $ruta_result['Estado_Destino'] . ' - ' . $ruta_result['Municipio_Destino']);
                        ?></td>
                    <td><?php
                        $cotizacion_id = $row['Fk_IdCotizacion'];
                        $cotizacion_sql = "SELECT Descripcion FROM cotizacion WHERE ID_Cotizacion = ?";
                        $stmt = $conn->prepare($cotizacion_sql);
                        $stmt->execute([$cotizacion_id]);
                        $cotizacion_result = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo htmlspecialchars($cotizacion_result['Descripcion']);
                        ?></td>
                    <td><?php echo htmlspecialchars($row['Fecha_Despacho']); ?></td>
                    <td><?php echo htmlspecialchars($row['Fecha_Llegada']); ?></td>
                    <td><?php echo htmlspecialchars($row['Pedimentos']); ?></td>
                    <td><?php echo htmlspecialchars($row['Contenedores']); ?></td>
                    <td><?php echo htmlspecialchars($row['Gastos']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                    <td>
                        <a href="modificar_viaje.php?id=<?php echo $row['ID_Viaje']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                        <form action="actualizar_viaje.php" method="post" class="d-inline">
                            <input type="hidden" name="id_viaje" value="<?php echo $row['ID_Viaje']; ?>">
                            <button type="submit" name="action" value="Completado" class="btn btn-success btn-sm">Completado</button>
                        </form>
                        <form action="actualizar_viaje.php" method="post" class="d-inline">
                            <input type="hidden" name="id_viaje" value="<?php echo $row['ID_Viaje']; ?>">
                            <button type="submit" name="action" value="Cancelado" class="btn btn-danger btn-sm">Cancelado</button>
                        </form>
                        <form action="#" method="post" class="d-inline">
                            <input type="hidden" name="id_viaje" value="<?php echo $row['ID_Viaje']; ?>">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#suspenderModal">Suspender</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para suspender viaje -->
    <div class="modal fade" id="suspenderModal" tabindex="-1" aria-labelledby="suspenderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suspenderModalLabel">Suspender Viaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="actualizar_viaje.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id_viaje" id="modal_id_viaje">
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" name="action" value="Suspender" class="btn btn-primary">Suspender</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configurar el modal para pasar el ID del viaje
        document.querySelectorAll('form button[data-bs-target="#suspenderModal"]').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('modal_id_viaje').value = button.closest('form').querySelector('input[name="id_viaje"]').value;
            });
        });
    </script>
</body>
</html>
