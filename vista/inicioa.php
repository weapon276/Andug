<?php
include 'conexion.php';
include 'index.php';
// Consulta para obtener datos de actas_administrativas
$sql_actas = "SELECT Fecha_Acta, COUNT(*) as count FROM actas_administrativas GROUP BY Fecha_Acta";
$result_actas = $conn->query($sql_actas);
$actas_data = [];
while($row = $result_actas->fetch(PDO::FETCH_ASSOC)) {
    $actas_data[] = $row;
}

// Consulta para obtener datos de ausencias
$sql_ausencias = "SELECT Fecha_Inicio, COUNT(*) as count FROM ausencias GROUP BY Fecha_Inicio";
$result_ausencias = $conn->query($sql_ausencias);
$ausencias_data = [];
while($row = $result_ausencias->fetch(PDO::FETCH_ASSOC)) {
    $ausencias_data[] = $row;
}

// Consulta para obtener datos de cambios_salario
$sql_cambios = "SELECT Fecha_Cambio, COUNT(*) as count FROM cambios_salario GROUP BY Fecha_Cambio";
$result_cambios = $conn->query($sql_cambios);
$cambios_data = [];
while($row = $result_cambios->fetch(PDO::FETCH_ASSOC)) {
    $cambios_data[] = $row;
}

// Consulta para obtener datos de desempeno
$sql_desempeno = "SELECT Fecha_Evaluacion, COUNT(*) as count FROM desempeno GROUP BY Fecha_Evaluacion";
$result_desempeno = $conn->query($sql_desempeno);
$desempeno_data = [];
while($row = $result_desempeno->fetch(PDO::FETCH_ASSOC)) {
    $desempeno_data[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Administrativo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
    <h1>Dashboard Administrativo</h1>
    
    <!-- Gráfico de Actas Administrativas -->
    <h2>Actas Administrativas</h2>
    <canvas id="actasChart"></canvas>
    <script>
        var ctx = document.getElementById('actasChart').getContext('2d');
        var actasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($actas_data as $data) { echo '"' . $data['Fecha_Acta'] . '",'; } ?>],
                datasets: [{
                    label: 'Actas Administrativas',
                    data: [<?php foreach ($actas_data as $data) { echo $data['count'] . ','; } ?>],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <!-- Gráfico de Ausencias -->
    <h2>Ausencias</h2>
    <canvas id="ausenciasChart"></canvas>
    <script>
        var ctx = document.getElementById('ausenciasChart').getContext('2d');
        var ausenciasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($ausencias_data as $data) { echo '"' . $data['Fecha_Inicio'] . '",'; } ?>],
                datasets: [{
                    label: 'Ausencias',
                    data: [<?php foreach ($ausencias_data as $data) { echo $data['count'] . ','; } ?>],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <!-- Gráfico de Cambios de Salario -->
    <h2>Cambios de Salario</h2>
    <canvas id="cambiosChart"></canvas>
    <script>
        var ctx = document.getElementById('cambiosChart').getContext('2d');
        var cambiosChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($cambios_data as $data) { echo '"' . $data['Fecha_Cambio'] . '",'; } ?>],
                datasets: [{
                    label: 'Cambios de Salario',
                    data: [<?php foreach ($cambios_data as $data) { echo $data['count'] . ','; } ?>],
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <!-- Gráfico de Desempeño -->
    <h2>Desempeño</h2>
    <canvas id="desempenoChart"></canvas>
    <script>
        var ctx = document.getElementById('desempenoChart').getContext('2d');
        var desempenoChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php foreach ($desempeno_data as $data) { echo '"' . $data['Fecha_Evaluacion'] . '",'; } ?>],
                datasets: [{
                    label: 'Desempeño',
                    data: [<?php foreach ($desempeno_data as $data) { echo $data['count'] . ','; } ?>],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
