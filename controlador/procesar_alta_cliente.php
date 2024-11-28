<?php
include '../modelo/conexion.php';
include '../index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $tipo = $_POST['tipo'];
    $linea_credito = $_POST['linea_credito'];
    $pago_contado = $_POST['pago_contado'];
    $status = $_POST['status'];
    $foto_logo = null;

    if (isset($_FILES['foto_logo']) && $_FILES['foto_logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_logo']['tmp_name'];
        $fileName = $_FILES['foto_logo']['name'];
        $fileSize = $_FILES['foto_logo']['size'];
        $fileType = $_FILES['foto_logo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize the file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check if the file type is allowed
        $allowedfileExtensions = array('jpg', 'gif', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where the file will be saved
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $foto_logo = $newFileName;
            }
        }
    }

    $sql = "INSERT INTO cliente (Nombre, Direccion, Tipo, Linea_Credito, Pago_Contado, Status, Foto_Logo) 
            VALUES (:nombre, :direccion, :tipo, :linea_credito, :pago_contado, :status, :foto_logo)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':linea_credito', $linea_credito);
    $stmt->bindParam(':pago_contado', $pago_contado);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':foto_logo', $foto_logo);
    
    if ($stmt->execute()) {
        header("Location: alta_cliente.php?status=success");
        exit();
    } else {
        header("Location: alta_cliente.php?status=error");
        exit();
    }
}
?>
