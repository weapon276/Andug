<?php
include '../modelo/conexion.php';
include '../index.php';

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los datos del formulario
    $id_ruta = $_POST['id_ruta'];
    $estado_origen = $_POST['estado_origen'];
    $municipio_origen = $_POST['municipio_origen'];
    $estado_destino = $_POST['estado_destino'];
    $municipio_destino = $_POST['municipio_destino'];
    $km = $_POST['km'];
    $cmantenimiento = $_POST['cmantenimiento'];
    $ccasetas = $_POST['ccasetas'];
    $cgasolina = $_POST['cgasolina'];

    // Prepara la consulta SQL para actualizar la ruta
    $sql = "UPDATE rutas SET 
                Estado_Origen = :estado_origen,
                Municipio_Origen = :municipio_origen,
                Estado_Destino = :estado_destino,
                Municipio_Destino = :municipio_destino,
                Km = :km,
                CMantenimiento = :cmantenimiento,
                CCasetas = :ccasetas,
                CGasolina = :cgasolina
            WHERE ID_Ruta = :id_ruta";

    // Prepara la consulta utilizando CONN
    $stmt = $conn->prepare($sql);

    // Asigna los valores a los parámetros
    $stmt->bindParam(':estado_origen', $estado_origen);
    $stmt->bindParam(':municipio_origen', $municipio_origen);
    $stmt->bindParam(':estado_destino', $estado_destino);
    $stmt->bindParam(':municipio_destino', $municipio_destino);
    $stmt->bindParam(':km', $km);
    $stmt->bindParam(':cmantenimiento', $cmantenimiento);
    $stmt->bindParam(':ccasetas', $ccasetas);
    $stmt->bindParam(':cgasolina', $cgasolina);
    $stmt->bindParam(':id_ruta', $id_ruta, PDO::PARAM_INT);

    // Ejecuta la consulta
    if ($stmt->execute()) {
        // Si la actualización fue exitosa, redirige con un parámetro de éxito
        header('Location: rutas.php?exito=1');
        exit;
    } else {
        // Si hubo un error, redirige con un mensaje de error
        header('Location: rutas.php?error=1');
        exit;
    }
} else {
    // Si no se accedió mediante POST, redirige a la página principal
    header('Location: rutas.php');
    exit;
}
