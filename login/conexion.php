<?php
$servername = "localhost";
$username = "root"; // Cambia esto a tu usuario de MySQL
$password = "";     // Cambia esto a tu contraseña de MySQL
$dbname = "Login";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
