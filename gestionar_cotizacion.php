<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

function obtenerCotizaciones($conn) {
    $sql = "SELECT c.*, 
                   cl.Nombre as ClienteNombre, 
                   e.Nombre as EmpleadoNombre, 
                   cam.Unidad as CamionUnidad, 
                   cam.Placas as CamionPlacas, 
                   cam.Tipo as CamionTipo 
            FROM cotizacion c
            JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente
            JOIN empleado e ON c.fk_idEmpleado = e.ID_Empleado
            JOIN camion cam ON cam.ID_Camion = (SELECT ID_Camion FROM cotizacion_camion WHERE cotizacion_camion.ID_Cotizacion = c.ID_Cotizacion LIMIT 1)";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$cotizaciones = obtenerCotizaciones($conn);

function calcularEstadoVigencia($vigencia) {
    $fecha_actual = new DateTime();
    $fecha_vigencia = new DateTime($vigencia);
    $intervalo = $fecha_actual->diff($fecha_vigencia)->days;
    if ($fecha_actual > $fecha_vigencia) {
        return 'vencido'; // Rojo
    } elseif ($intervalo <= 7) {
        return 'por_vencer'; // Amarillo
    } else {
        return 'vigente'; // Verde
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vencido {
            color: red;
        }
        .por_vencer {
            color: orange;
        }
        .vigente {
            color: green;
        }
    </style>
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Gestión de Cotizaciones</h1>
    <table class="table table-striped">
    <thead>
    <thead>
    <tr>
        <th>ID Cotización</th>
        <th>Cliente</th>
        <th>Empleado</th>
        <th>Descripción</th>
        <th>Monto</th>
        <th>Fecha</th>
        <th>Vigencia</th>
        <th>Estado de Vigencia</th>
        <th>Camión</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($cotizaciones as $cotizacion): 
        $estado_vigencia = calcularEstadoVigencia($cotizacion['Vigencia']);
    ?>
    <tr>
        <td><?php echo htmlspecialchars($cotizacion['ID_Cotizacion'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['ClienteNombre'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['EmpleadoNombre'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['Descripcion'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['Monto'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['Fecha'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($cotizacion['Vigencia'] ?? ''); ?></td>
        <td class="<?php echo $estado_vigencia; ?>">
            <?php 
            if ($estado_vigencia == 'vencido') {
                echo 'Vencido';
            } elseif ($estado_vigencia == 'por_vencer') {
                echo 'Por vencer';
            } else {
                echo 'Vigente';
            }
            ?>
        </td>
        <td><?php echo htmlspecialchars($cotizacion['CamionUnidad'] ?? '') . " - " . htmlspecialchars($cotizacion['CamionPlacas'] ?? ''); ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>


    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
