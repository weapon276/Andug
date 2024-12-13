<?php
session_start();
session_unset();
session_destroy();
require 'modelo/conexion.php';

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];

    try {
        $stmt = $conn->prepare("DELETE FROM sesion WHERE user_id = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        session_destroy();
    } catch (PDOException $e) {
        header("Location: error.php");
        exit();
    }
}
header("Location: login.php");
exit();
