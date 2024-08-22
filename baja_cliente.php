<?php
include 'conexion.php';

session_start(); // Asegúrate de iniciar la sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $motivo_baja = $_POST['motivo_baja'];

    $sql = "UPDATE cliente SET Status = 'Baja', Motivo_Baja = :motivo_baja WHERE ID_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':motivo_baja', $motivo_baja);

    // Crear log del movimiento
    $userId = $_SESSION['userId'];  // Asegúrate de que tienes el ID del usuario en la sesión
    $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion) 
                VALUES (:userId, 'Baja de Cliente', :descripcion)";
    $stmt_log = $conn->prepare($sql_log);
    $descripcion_log = "Cliente ID: $id_cliente dado de baja. Motivo: $motivo_baja.";
    $stmt_log->bindParam(':userId', $userId);
    $stmt_log->bindParam(':descripcion', $descripcion_log);
    $stmt_log->execute();

    if ($stmt->execute()) {
        header("Location: cliente.php?status=success");
        exit();
    } else {
        header("Location: cliente.php?status=error");
        exit();
    }
}
?>
