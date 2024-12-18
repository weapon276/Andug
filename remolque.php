<?php
require_once 'modelo/conexion.php';

// Configuración de paginación y ordenamiento
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_remolque';
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Calcular offset para la paginación
$offset = ($page - 1) * $per_page;

// Consulta SQL base
$sql = "SELECT * FROM remolque 
        WHERE placas LIKE :search OR tipo_remolque LIKE :search OR subtipo_remolque LIKE :search
        ORDER BY $sort $order
        LIMIT :per_page OFFSET :offset";

// Preparar y ejecutar la consulta
$search_term = "%$search%";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', $search_term, PDO::PARAM_STR);
$stmt->bindValue(':per_page', (int)$per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener total de registros para la paginación
$total_sql = "SELECT COUNT(*) as total 
              FROM remolque 
              WHERE placas LIKE :search OR tipo_remolque LIKE :search OR subtipo_remolque LIKE :search";

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
    <title>Listado de Remolques</title>
    <link rel="stylesheet" href="assets/css/camion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Listado de Remolques</h1>
            <div class="actions">
                <button class="btn btn-secondary">Filtros</button>
                <a href="nuevo-remolque.php" class="btn btn-primary">Nuevo</a>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar por placas, tipo o subtipo..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th data-sort="placas">Placas</th>
                        <th data-sort="tipo_remolque">Tipo</th>
                        <th data-sort="marca">Marca</th>
                        <th data-sort="modelo">Modelo</th>
                        <th data-sort="año">Año</th>
                        <th data-sort="PesoR">Peso</th>
                        <th data-sort="capacidad_carga">Capacidad de Carga</th>
                        <th data-sort="estado">Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    <?php if (!empty($result)) : ?>
        <?php foreach ($result as $row) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['placas']); ?></td>
                <td><?php echo htmlspecialchars($row['tipo_remolque']); ?></td>
                <td><?php echo htmlspecialchars($row['marca']); ?></td>
                <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                <td><?php echo htmlspecialchars($row['año']); ?></td>
                <td><?php echo htmlspecialchars($row['PesoR']); ?></td>
                <td><?php echo htmlspecialchars($row['capacidad_carga']); ?></td>
                <td>
                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $row['estado'])); ?>">
                        <?php echo htmlspecialchars($row['estado']); ?>
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalModificarRemolque" data-id="<?php echo $row['id_remolque']; ?>">
                        <i class="fa fa-edit"></i> 
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuspenderRemolque" data-id="<?php echo $row['id_remolque']; ?>">
                        <i class="fa fa-pause"></i>
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDarDeBajaRemolque" data-id="<?php echo $row['id_remolque']; ?>">
                        <i class="fa fa-trash"></i> 
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="11">No se encontraron resultados.</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>

        <div class="pagination">
            <div>
                Mostrando <?php echo $offset + 1; ?> a 
                <?php echo min($offset + $per_page, $total_records); ?> 
                de <?php echo $total_records; ?> registros
            </div>
            <div class="pagination-controls">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>" 
                       class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        // Búsqueda en tiempo real
        const searchInput = document.querySelector('.search-input');
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                window.location.href = `?search=${e.target.value}&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>`;
            }, 500);
        });

        // Ordenamiento por columnas
        document.querySelectorAll('th[data-sort]').forEach(th => {
            th.addEventListener('click', () => {
                const sortBy = th.dataset.sort;
                const currentOrder = new URLSearchParams(window.location.search).get('order') || 'DESC';
                const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
                window.location.href = `?sort=${sortBy}&order=${newOrder}&search=<?php echo urlencode($search); ?>`;
            });
        });
    </script>
</body>
</html>