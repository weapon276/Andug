<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $motivo_baja = $_POST['motivo_baja'];

    $sql = "UPDATE cliente SET Status = 'Baja', Motivo_Baja = :motivo_baja WHERE ID_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':motivo_baja', $motivo_baja);

    if ($stmt->execute()) {
        header("Location: cliente.php?status=success");
        exit();
    } else {
        header("Location: cliente.php?status=error");
        exit();
    }
}
?>
