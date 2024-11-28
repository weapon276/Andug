<?php
include 'conexion.php';
session_start(); // Asegúrate de iniciar la sesión

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

    $userId = $_SESSION['userId'];  // Asegúrate de que tienes el ID del usuario en la sesión

    // Crear descripción para el log
    $descripcion_log = "Cliente ID: $id_cliente actualizado. ";
    $descripcion_log .= "Nombre: $nombre, Direccion: $direccion, Tipo: $tipo, Linea de Crédito: $linea_credito, Pago Contado: $pago_contado, Status: $status.";

    // Preparar el SQL para el log
    $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) 
                VALUES (:userId, 'Actualización de Cliente', :descripcion)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bindParam(':userId', $userId);
    $stmt_log->bindParam(':descripcion', $descripcion_log);

    // Ejecutar actualización del cliente y log en transacción
    try {
        $conn->beginTransaction(); // Iniciar la transacción

        if ($stmt->execute() && $stmt_log->execute()) {
            $conn->commit(); // Confirmar la transacción
            header("Location: cliente.php?status=success");
        } else {
            $conn->rollBack(); // Revertir la transacción en caso de error
            header("Location: cliente.php?status=error");
        }
        exit();
    } catch (Exception $e) {
        $conn->rollBack(); // Revertir en caso de excepción
        header("Location: cliente.php?status=error");
        exit();
    }
}
?>
