<?php
include '../modelo/conexion.php';

// Obtener datos JSON de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);
$rutas = $data['rutas'];

// Preparar la consulta SQL para insertar cada ruta en la tabla "cotizacion"
$stmt = $pdo->prepare("INSERT INTO cotizacion (PuntoA_Origen, PuntoB_Destino, Fecha, Vigencia) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))");

$success = true;

foreach ($rutas as $ruta) {
    $puntoA = $ruta['puntoA'];
    $puntoB = $ruta['puntoB'];

    if (!$stmt->execute([$puntoA, $puntoB])) {
        $success = false;
        break;
    }
}

// Responder al frontend
echo json_encode(['success' => $success]);
?>
