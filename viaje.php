<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Función para obtener todas las rutas y los camiones asignados
function obtenerRutasCamiones($conn) {
    $sql = "SELECT rc.ID_RutaCamion, r.Estado_Origen, r.Municipio_Origen, r.Estado_Destino, r.Municipio_Destino, r.Distancia, c.Placas, c.Tipo, rc.Estado_Camion
            FROM ruta_camion rc
            JOIN rutas r ON rc.ID_Ruta = r.ID_Ruta
            JOIN camion c ON rc.ID_Camion = c.ID_Camion";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$rutas_camiones = obtenerRutasCamiones($conn);

?>

<!DOCTYPE html>
<html lang="es">
<head>
        <!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Viajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table tr td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Gestionar Viajes</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ruta</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Distancia</th>
                <th>Camión</th>
                <th>Tipo</th>
                <th>Estado del Camión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rutas_camiones as $ruta_camion): ?>
            <tr>
                <td><?php echo $ruta_camion['ID_RutaCamion']; ?></td>
                <td><?php echo htmlspecialchars($ruta_camion['Estado_Origen']) . ', ' . htmlspecialchars($ruta_camion['Municipio_Origen']); ?></td>
                <td><?php echo htmlspecialchars($ruta_camion['Estado_Destino']) . ', ' . htmlspecialchars($ruta_camion['Municipio_Destino']); ?></td>
                <td><?php echo $ruta_camion['Distancia']; ?> km</td>
                <td><?php echo htmlspecialchars($ruta_camion['Placas']); ?></td>
                <td><?php echo htmlspecialchars($ruta_camion['Tipo']); ?></td>
                <td><?php echo $ruta_camion['Estado_Camion']; ?></td>
                <td>
                    <form method="POST" action="actualizar_estado_camion.php" style="display: inline;">
                        <input type="hidden" name="id_ruta_camion" value="<?php echo $ruta_camion['ID_RutaCamion']; ?>">
                        <input type="hidden" name="estado_camion" value="Perfecto">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                    </form>
                    <form method="POST" action="actualizar_estado_camion.php" style="display: inline;">
                        <input type="hidden" name="id_ruta_camion" value="<?php echo $ruta_camion['ID_RutaCamion']; ?>">
                        <input type="hidden" name="estado_camion" value="Problema">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </button>
                    </form>
                    <form method="POST" action="actualizar_estado_camion.php" style="display: inline;">
                        <input type="hidden" name="id_ruta_camion" value="<?php echo $ruta_camion['ID_RutaCamion']; ?>">
                        <input type="hidden" name="estado_camion" value="Taller">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
