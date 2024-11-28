<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado y es un cliente
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

// Función para obtener los viajes del cliente
function obtenerViajesCliente($conn, $id_cliente) {
    $sql = "SELECT v.*, r.Estado_Origen, r.Municipio_Origen, r.Estado_Destino, r.Municipio_Destino, r.Distancia, c.Placas, c.Peso, c.Tipo, o.Nombre as OperadorNombre, o.Foto as OperadorFoto
            FROM viaje v
            JOIN rutas r ON v.ID_Ruta = r.ID_Ruta
            JOIN camion c ON v.ID_Camion = c.ID_Camion
            JOIN operador o ON v.ID_Operador = o.ID_Operador
            WHERE v.ID_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$id_cliente = $_SESSION['id_cliente'];
$viajes = obtenerViajesCliente($conn, $id_cliente);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Viajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table tr td {
            vertical-align: middle;
        }
        .foto-operador {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Mis Viajes</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Operador</th>
                <th>Foto</th>
                <th>Destino</th>
                <th>Distancia (km)</th>
                <th>Camión</th>
                <th>Toneladas</th>
                <th>Contenedores</th>
                <th>Fecha de Salida</th>
                <th>Tiempo Aproximado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viajes as $viaje): ?>
            <tr>
                <td><?php echo htmlspecialchars($viaje['OperadorNombre']); ?></td>
                <td><img src="fotos_operadores/<?php echo htmlspecialchars($viaje['OperadorFoto']); ?>" alt="Foto del Operador" class="foto-operador"></td>
                <td><?php echo htmlspecialchars($viaje['Estado_Destino']) . ', ' . htmlspecialchars($viaje['Municipio_Destino']); ?></td>
                <td><?php echo $viaje['Distancia']; ?></td>
                <td><?php echo htmlspecialchars($viaje['Placas']) . ' (' . htmlspecialchars($viaje['Tipo']) . ')'; ?></td>
                <td><?php echo $viaje['Toneladas']; ?></td>
                <td><?php echo $viaje['Contenedores']; ?></td>
                <td><?php echo $viaje['Fecha_Salida']; ?></td>
                <td><?php echo $viaje['Tiempo_Aprox']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
