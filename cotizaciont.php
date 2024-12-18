<?php
require_once 'modelo/conexion.php';

$per_page = $_GET['per_page'] ?? 10;
$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'ID_Cotizacion';
$order = $_GET['order'] ?? 'DESC';

$allowed_sort_columns = ['ID_Cotizacion', 'NombreCliente', 'NombreEmpleado'];
$allowed_order = ['ASC', 'DESC'];

$sort = in_array($sort, $allowed_sort_columns) ? $sort : 'ID_Cotizacion';
$order = in_array(strtoupper($order), $allowed_order) ? strtoupper($order) : 'DESC';

$offset = ($page - 1) * $per_page;

// Consulta principal
$sql = "SELECT c.*, cl.Nombre as NombreCliente, e.Nombre as NombreEmpleado 
        FROM cotizacion c 
        LEFT JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente 
        LEFT JOIN empleado e ON c.fk_idEmpleado = e.ID_Empleado 
        WHERE cl.Nombre LIKE :search OR c.ID_Cotizacion LIKE :search
        ORDER BY $sort $order 
        LIMIT :per_page OFFSET :offset";

$stmt = $conn->prepare($sql);
$search_term = "%$search%";
$stmt->bindValue(':search', $search_term, PDO::PARAM_STR);
$stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_sql = "SELECT COUNT(*) as total 
              FROM cotizacion c 
              LEFT JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente 
              WHERE cl.Nombre LIKE :search OR c.ID_Cotizacion LIKE :search";

$total_stmt = $conn->prepare($total_sql);
$total_stmt->bindValue(':search', $search_term, PDO::PARAM_STR);
$total_stmt->execute();
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $per_page);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Cotizaciones</title>
    <link rel="stylesheet" href="assets/css/cotizaciont.css">

</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Listado de cotizaciones</h1>
            <div class="actions">
                <button class="btn btn-secondary">Filtros</button>
                <a href="cotizacion.php" class="btn btn-primary">Nueva</a>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar...">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th data-sort="id">ID</th>
                        <th data-sort="cliente">Cliente</th>
                        <th data-sort="fecha">Fecha y hora</th>
                        <th data-sort="vigencia">Vigencia</th>
                        <th>Emitido por</th>
                        <th data-sort="monto">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                
    <?php if ($result): ?>
        <?php foreach ($result as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['ID_Cotizacion']) ?></td>
                <td><?= htmlspecialchars($row['NombreCliente']) ?></td>
                <td><?= htmlspecialchars($row['Fecha']) ?></td>
                <td><?= htmlspecialchars($row['Vigencia']) ?></td>
                <td><?= htmlspecialchars($row['NombreEmpleado']) ?></td>
                <td><?= htmlspecialchars($row['Monto']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">No se encontraron registros.</td>
        </tr>
    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="pagination">
            <div class="records-per-page">
                Mostrar
                <select>
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                registros
            </div>
            <div class="pagination-info">
                Mostrando 1 a 10 de 255 registros
            </div>
            <div class="pagination-controls">
                <button class="page-btn">«</button>
                <button class="page-btn">‹</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">›</button>
                <button class="page-btn">»</button>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('th[data-sort]').forEach(th => {
            th.addEventListener('click', () => {
                const sortBy = th.dataset.sort;
                const isAsc = !th.classList.contains('sorted-asc');
                
                document.querySelectorAll('th').forEach(header => {
                    header.classList.remove('sorted-asc', 'sorted-desc');
                });
                
                th.classList.add(isAsc ? 'sorted-asc' : 'sorted-desc');
                
   
            });
        });

        const searchInput = document.querySelector('.search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
        
            }, 500);
        });

        document.querySelector('.records-per-page select').addEventListener('change', (e) => {
      
        });

        document.querySelectorAll('.pagination-controls .page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!btn.classList.contains('active')) {
                    document.querySelector('.page-btn.active').classList.remove('active');
                    btn.classList.add('active');
          
                }
            });
        });
    </script>
</body>
</html>