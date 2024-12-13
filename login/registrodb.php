<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $contrasena = $password;

    $stmt = $conn->prepare("INSERT INTO username (username, password, Contraseña) VALUES (:username, :password, :contrasena)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':contrasena', $contrasena);

    try {
        $stmt->execute();

        header("Location: Login.php?username=" . urlencode($username) . "&password=" . urlencode($password));
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
