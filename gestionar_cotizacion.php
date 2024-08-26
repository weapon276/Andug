<?php

include 'conexion.php';
include 'index.php';
session_start(); // Asegúrate de iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType']) || !isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];

// Función para obtener las cotizaciones
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

// Obtener las cotizaciones
$cotizaciones = obtenerCotizaciones($conn);

// Función para calcular el estado de vigencia de la cotización
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
    
    <!-- Cuadro de búsqueda -->
    <input class="form-control mb-3" id="busqueda" type="text" placeholder="Buscar cotización...">
    
    <table class="table table-striped" id="tabla-cotizaciones">
        <thead>
            <tr>
                <th>ID Cotización</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Vigencia</th>
                <th>Estatus</th>
                <th>Camión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cotizaciones as $cotizacion): 
                $estado_vigencia = calcularEstadoVigencia($cotizacion['Vigencia']);
                // Ocultar las cotizaciones vencidas
                if ($estado_vigencia == 'vencido') continue;
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
                    if ($estado_vigencia == 'por_vencer') {
                        echo 'Por vencer';
                    } else {
                        echo 'Vigente';
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($cotizacion['CamionUnidad'] ?? '') . " - " . htmlspecialchars($cotizacion['CamionPlacas'] ?? ''); ?></td>
                <td>
                    <!-- Botón para descargar el PDF de la cotización -->
                    <a href="cotizacion_pdf.php?id=<?php echo $cotizacion['ID_Cotizacion']; ?>" class="btn btn-primary btn-sm">Dscargar PDF</a>
                    <i class="bi bi-file-earmark-pdf"></i> 
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script para búsqueda en tiempo real
    $(document).ready(function() {
        $("#busqueda").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tabla-cotizaciones tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
</body>
</html>
