<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Función para obtener todas las cotizaciones
function obtenerCotizaciones($conn) {
    $sql = "SELECT c.*, cl.Nombre as ClienteNombre FROM cotizacion c 
            JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente";
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
            <tr>
                <th>ID Cotización</th>
                <th>Cliente</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Vigencia</th>
                <th>Estado de Vigencia</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cotizaciones as $cotizacion): 
                $estado_vigencia = calcularEstadoVigencia($cotizacion['Vigencia']);
            ?>
            <tr>
                <td><?php echo $cotizacion['ID_Cotizacion']; ?></td>
                <td><?php echo htmlspecialchars($cotizacion['ClienteNombre']); ?></td>
                <td><?php echo htmlspecialchars($cotizacion['Descripcion']); ?></td>
                <td><?php echo $cotizacion['Monto']; ?></td>
                <td><?php echo $cotizacion['Fecha']; ?></td>
                <td><?php echo $cotizacion['Vigencia']; ?></td>
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
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
