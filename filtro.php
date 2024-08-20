<?php
// Filtro por fecha
$startDate = $_GET['start_date'] ?? '2023-01-01';
$endDate = $_GET['end_date'] ?? '2023-12-31';

// Consulta para obtener datos de actas_administrativas
$sql_actas = "SELECT Fecha_Acta, COUNT(*) as count FROM actas_administrativas WHERE Fecha_Acta BETWEEN '$startDate' AND '$endDate' GROUP BY Fecha_Acta";
$result_actas = $conn->query($sql_actas);
// Repite el proceso para otras consultas
?>
