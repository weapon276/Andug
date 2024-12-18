<?php
include 'modelo/conexion.php';

// Fetch data from the database
$sql = "SELECT v.*, c.Unidad AS NombreCamion, o.Nombre AS NombreOperador, cl.Nombre AS NombreCliente, r.Nombrer AS NombreRuta, co.ID_Cotizacion AS NumeroCotizacion
        FROM viaje v
        LEFT JOIN camion c ON v.ID_Camion = c.ID_Camion
        LEFT JOIN operador o ON v.ID_Operador = o.ID_Operador
        LEFT JOIN cliente cl ON v.ID_Cliente = cl.ID_Cliente
        LEFT JOIN rutas r ON v.Fk_IdRutas = r.ID_Ruta
        LEFT JOIN cotizacion co ON v.Fk_IdCotizacion = co.ID_Cotizacion
        ORDER BY v.fecha_inicio DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    // Obtener resultados como un array asociativo
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones y Viajes en Curso</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/clientes.css">
    <link rel="stylesheet" href="assets/css/servicios.css">
    <link rel="stylesheet" href="assets/css/modal.css">
    <style>
   
    </style>
</head>
<body>
    <main class="main-content">
        <div class="header">
            <h1 class="title"><i class="fas fa-truck"></i> Cotizaciones y Viajes en Curso</h1>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar..." onkeyup="searchQuotations()">
        </div>

        <div class="table-container">
        <table>
    <thead>
    <tr>
                        <th><i class="fas fa-hashtag"></i> ID Viaje</th>
                        <th><i class="fas fa-truck"></i> Camión</th>
                        <th><i class="fas fa-user"></i> Operador</th>
                        <th><i class="fas fa-building"></i> Cliente</th>
                        <th><i class="fas fa-route"></i> Ruta</th>
                        <th><i class="fas fa-file-invoice-dollar"></i> Cotización</th>
                        <th><i class="fas fa-calendar-alt"></i> Fecha Despacho</th>
                        <th><i class="fas fa-calendar-check"></i> Fecha Llegada</th>
                        <th><i class="fas fa-file-alt"></i> Pedimentos</th>
                        <th><i class="fas fa-box"></i> Contenedores</th>
                        <th><i class="fas fa-weight"></i> Toneladas</th>
                        <th><i class="fas fa-money-bill-wave"></i> Gastos</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cogs"></i> Acciones</th>
                    </tr>
    </thead>
    <tbody>
        <?php if (count($rows) > 0): ?>
            <?php foreach ($rows as $row): ?>
                <tr>
    <td><?= htmlspecialchars($row["ID_Viaje"]) ?></td>
    <td><?= htmlspecialchars($row["NombreCamion"]) ?></td>
    <td><?= htmlspecialchars($row["NombreOperador"]) ?></td>
    <td><?= htmlspecialchars($row["NombreCliente"]) ?></td>
    <td><?= htmlspecialchars($row["NombreRuta"]) ?></td>
    <td><?= htmlspecialchars($row["NumeroCotizacion"]) ?></td>
    <td><?= htmlspecialchars($row["Fecha_Despacho"]) ?></td>
    <td><?= htmlspecialchars($row["Fecha_Llegada"]) ?></td>
    <td><?= htmlspecialchars($row["Pedimentos"]) ?></td>
    <td><?= htmlspecialchars($row["Contenedores"]) ?></td>
    <td><?= htmlspecialchars($row["Toneladas"]) ?></td>
    <td>$<?= number_format(htmlspecialchars($row["Gastos"]), 2) ?></td>
</tr>
          <span class='status-badge status-<?= strtolower(str_replace(' ', '-', $row["Status"])) ?>'>
                            <?= htmlspecialchars($row["Status"]) ?>
                        </span>
                 
                    <td>
                        <div class='action-buttons'>
                            <button class='action-btn' onclick='editQuotation(<?= $row["ID_Viaje"] ?>)'>
                                <i class='fas fa-edit'></i>
                            </button>
                            <button class='action-btn' onclick='deleteQuotation(<?= $row["ID_Viaje"] ?>)'>
                                <i class='fas fa-trash-alt'></i>
                            </button>
                        </div>
                    </td>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="14">No se encontraron registros</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
        </div>
    </main>

    <script>
        function searchQuotations() {
            const searchTerm = document.querySelector('.search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.quotations-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        function editQuotation(id) {
            console.log('Editing quotation:', id);
            // Implement edit functionality
        }

        function deleteQuotation(id) {
            if (confirm('¿Está seguro de que desea eliminar este viaje?')) {
                console.log('Deleting quotation:', id);
                // Implement delete functionality
            }
        }

        function changePage(direction) {
            console.log('Changing page:', direction);
            // Implement pagination functionality
        }
    </script>
</body>
</html>