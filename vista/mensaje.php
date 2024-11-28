<?php

include 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Función para obtener los mensajes recibidos
function obtenerMensajesRecibidos($conn, $id_usuario) {
    $sql = "SELECT m.*, c.Nombre as ClienteNombre
            FROM mensajes m
            JOIN cliente c ON m.ID_Cliente = c.ID_Cliente
            WHERE m.ID_Destinatario = :id_usuario
            ORDER BY m.Fecha_Envio DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para enviar un mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensaje'])) {
    $id_cliente = $_SESSION['id_cliente'];
    $tipo_mensaje = $_POST['tipo_mensaje'];
    $mensaje = $_POST['mensaje'];
    $id_destinatario = 0; // Destinatario por defecto

    // Determinar el destinatario según el tipo de mensaje
    switch ($tipo_mensaje) {
        case 'Facturas':
            $id_destinatario = 1; // ID del usuario de contabilidad
            break;
        case 'Operador':
            $id_destinatario = 2; // ID del usuario de recursos humanos
            break;
        case 'General':
            $id_destinatario = 2; // ID del administrador
            break;
    }

    $sql = "INSERT INTO mensajes (ID_Cliente, Tipo_Mensaje, Mensaje, ID_Destinatario) 
            VALUES (:id_cliente, :tipo_mensaje, :mensaje, :id_destinatario)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->bindParam(':tipo_mensaje', $tipo_mensaje);
    $stmt->bindParam(':mensaje', $mensaje);
    $stmt->bindParam(':id_destinatario', $id_destinatario);
    $stmt->execute();

    header("Location: mensaje.php");
    exit();
}

$id_usuario = $_SESSION['user_type'] == 'cliente' ? $_SESSION['id_cliente'] : $_SESSION['id_usuario'];
$mensajes = obtenerMensajesRecibidos($conn, $id_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Mensajes</h1>
    <form method="POST" action="mensaje.php" class="mb-4">
        <div class="mb-3">
            <label for="tipo_mensaje" class="form-label">Tipo de Mensaje</label>
            <select class="form-select" id="tipo_mensaje" name="tipo_mensaje" required>
                <option value="Facturas">Facturas</option>
                <option value="Operador">Operador</option>
                <option value="General">General</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
        </div>
        <button type="submit" name="enviar_mensaje" class="btn btn-primary">Enviar Mensaje</button>
    </form>

    <h2>Mensajes Recibidos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Tipo de Mensaje</th>
                <th>Mensaje</th>
                <th>Fecha de Envío</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mensajes as $mensaje): ?>
            <tr>
                <td><?php echo htmlspecialchars($mensaje['ClienteNombre']); ?></td>
                <td><?php echo htmlspecialchars($mensaje['Tipo_Mensaje']); ?></td>
                <td><?php echo htmlspecialchars($mensaje['Mensaje']); ?></td>
                <td><?php echo $mensaje['Fecha_Envio']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
