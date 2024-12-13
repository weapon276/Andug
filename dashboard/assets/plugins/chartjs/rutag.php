<?php
include '../modelo/conexion.php';

// Consulta para obtener los datos
$sql = "
    SELECT 
        MONTH(FechaV) AS mes, 
        COUNT(CASE WHEN Status = 'En Proceso' THEN 1 END) AS en_proceso,
        COUNT(CASE WHEN Status = 'Completado' THEN 1 END) AS finalizados
    FROM viaje
    WHERE YEAR(FechaV) = YEAR(CURDATE())
    GROUP BY MONTH(FechaV)
    ORDER BY mes ASC;
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
