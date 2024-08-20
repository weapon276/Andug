<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ruta_camion = $_POST['id_ruta_camion'];
    $estado_camion = $_POST['estado_camion'];

    $sql = "UPDATE ruta_camion SET Estado_Camion = :estado_camion WHERE ID_RutaCamion = :id_ruta_camion";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_ruta_camion', $id_ruta_camion);
    $stmt->bindParam(':estado_camion', $estado_camion);

    if ($stmt->execute()) {
        header("Location: gestionar_viajes.php?status=success");
        exit();
    } else {
        header("Location: gestionar_viajes.php?status=error");
        exit();
    }
}
?>
