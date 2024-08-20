<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $tipo = $_POST['tipo'];
    $linea_credito = $_POST['linea_credito'];
    $pago_contado = $_POST['pago_contado'];
    $status = $_POST['status'];

    $sql = "UPDATE cliente SET Nombre = :nombre, Direccion = :direccion, Tipo = :tipo, Linea_Credito = :linea_credito, Pago_Contado = :pago_contado, Status = :status WHERE ID_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':linea_credito', $linea_credito);
    $stmt->bindParam(':pago_contado', $pago_contado);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        header("Location: cliente.php?status=success");
        exit();
    } else {
        header("Location: cliente.php?status=error");
        exit();
    }
}
?>
