<?php
include 'conexion.php';
include 'index.php';
session_start();

// Verificar si el usuario está autenticado y tiene el tipo adecuado
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['Administrador', 'Contabilidad'])) {
    $mensaje = 'Por el momento no cuentas con mensajes.';
    $mostrarMensaje = true;
} else {
    $user_id = $_SESSION['user_id'];

    // Función para obtener las notificaciones del usuario
    function obtenerNotificaciones($conn, $user_id) {
        $sql = "SELECT m.ID_Mensaje, m.Mensaje, m.Fecha_Envio, c.Nombre AS Cliente
                FROM mensajes m
                JOIN cliente c ON m.ID_Cliente = c.ID_Cliente
                WHERE m.ID_Destinatario = :user_id
                ORDER BY m.Fecha_Envio DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $notificaciones = obtenerNotificaciones($conn, $user_id);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Notificaciones</h1>
    <?php if (isset($mostrarMensaje) && $mostrarMensaje): ?>
        <div class="alert alert-info" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php elseif (count($notificaciones) > 0): ?>
        <ul class="list-group">
            <?php foreach ($notificaciones as $notificacion): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($notificacion['Mensaje']); ?></strong>
                    <br>
                    <small class="text-muted">Cliente: <?php echo htmlspecialchars($notificacion['Cliente']); ?> - <?php echo htmlspecialchars($notificacion['Fecha_Envio']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            No tienes notificaciones.
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>