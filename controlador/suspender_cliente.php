<?php
include '../modelo/conexion.php';
include '../index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];

    $sql = "UPDATE cliente SET Status = 'Suspendido' WHERE ID_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);

    if ($stmt->execute()) {
        header("Location: cliente.php?status=success");
        exit();
    } else {
        header("Location: cliente.php?status=error");
        exit();
    }
}
?>
