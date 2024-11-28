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


// Función para obtener datos del empleado
function obtenerDatosEmpleado($conn) {
    $sql = "SELECT e.Nombre, 
                   SUM(CASE WHEN a.Tipo = 'Ausencia' THEN 1 ELSE 0 END) AS Ausencias,
                   SUM(CASE WHEN a.Tipo = 'Vacaciones' THEN 1 ELSE 0 END) AS Vacaciones,
                   SUM(CASE WHEN a.Tipo = 'Capacitacion' THEN 1 ELSE 0 END) AS Capacitaciones,
                   SUM(CASE WHEN a.Tipo = 'Incapacidad' THEN 1 ELSE 0 END) AS Incapacidades,
                   SUM(CASE WHEN a.Tipo = 'Falta' THEN 1 ELSE 0 END) AS Faltas,
                   COUNT(c.ID_Cambio) AS CambiosSalarios,
                   AVG(d.Calificacion) AS Desempeno,
                   COUNT(act.ID_Acta) AS ActasAdministrativas
            FROM empleado e
            LEFT JOIN ausencias a ON e.ID_Empleado = a.ID_Empleado
            LEFT JOIN cambios_salario c ON e.ID_Empleado = c.ID_Empleado
            LEFT JOIN desempeno d ON e.ID_Empleado = d.ID_Empleado
            LEFT JOIN actas_administrativas act ON e.ID_Empleado = act.ID_Empleado
            GROUP BY e.ID_Empleado";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$empleados = obtenerDatosEmpleado($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Reporte de Empleados</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ausencias</th>
                <th>Vacaciones</th>
                <th>Capacitaciones</th>
                <th>Incapacidades</th>
                <th>Faltas</th>
                <th>Cambios de Salario</th>
                <th>Desempeño</th>
                <th>Actas Administrativas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?php echo htmlspecialchars($empleado['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Ausencias']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Vacaciones']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Capacitaciones']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Incapacidades']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Faltas']); ?></td>
                <td><?php echo htmlspecialchars($empleado['CambiosSalarios']); ?></td>
                <td><?php echo htmlspecialchars(number_format($empleado['Desempeno'], 2)); ?></td>
                <td><?php echo htmlspecialchars($empleado['ActasAdministrativas']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Gráficas</h2>
    <canvas id="empleadosChart" width="400" height="200"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('empleadosChart').getContext('2d');
    var empleados = <?php echo json_encode($empleados); ?>;
    var nombres = empleados.map(function(e) { return e.Nombre; });
    var ausencias = empleados.map(function(e) { return e.Ausencias; });
    var vacaciones = empleados.map(function(e) { return e.Vacaciones; });
    var capacitaciones = empleados.map(function(e) { return e.Capacitaciones; });
    var incapacidades = empleados.map(function(e) { return e.Incapacidades; });
    var faltas = empleados.map(function(e) { return e.Faltas; });
    var cambiosSalarios = empleados.map(function(e) { return e.CambiosSalarios; });
    var desempeno = empleados.map(function(e) { return e.Desempeno; });
    var actasAdministrativas = empleados.map(function(e) { return e.ActasAdministrativas; });

    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: nombres,
            datasets: [
                {
                    label: 'Ausencias',
                    data: ausencias,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Vacaciones',
                    data: vacaciones,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Capacitaciones',
                    data: capacitaciones,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Incapacidades',
                    data: incapacidades,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Faltas',
                    data: faltas,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Cambios de Salario',
                    data: cambiosSalarios,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Desempeño',
                    data: desempeno,
                    backgroundColor: 'rgba(100, 159, 64, 0.2)',
                    borderColor: 'rgba(100, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Actas Administrativas',
                    data: actasAdministrativas,
                    backgroundColor: 'rgba(200, 159, 64, 0.2)',
                    borderColor: 'rgba(200, 159, 64, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
