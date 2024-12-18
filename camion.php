<?php
require_once 'modelo/conexion.php';


$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'ID_Camion';
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$offset = ($page - 1) * $per_page;

$sql = "SELECT c.*, e.Nombre as NombreEmpleado 
        FROM camion c 
        LEFT JOIN empleado e ON c.Fk_id_Emplado = e.ID_Empleado
        WHERE c.Placas LIKE :search OR c.Tipo LIKE :search
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
              FROM camion 
              WHERE Placas LIKE :search OR Tipo LIKE :search";

$total_stmt = $conn->prepare($total_sql);
$total_stmt->bindValue(':search', $search_term, PDO::PARAM_STR);
$total_stmt->execute();
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $per_page);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_camion'])) {
        $placas = $_POST['placas'];
        $peso = $_POST['peso'];
        $unidad = $_POST['unidad'];
        $tipo = $_POST['tipo'];
        $poliza_seguro = $_POST['poliza_seguro'];
        $gps = $_POST['gps'];

        $sql = "INSERT INTO camion (Placas, Peso, Unidad, Tipo, Poliza_Seguro, GPS, Status, fecha_inicio) 
                VALUES (:placas, :peso, :unidad, :tipo, :poliza_seguro, :gps, 'Libre', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':placas', $placas);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':poliza_seguro', $poliza_seguro);
        $stmt->bindParam(':gps', $gps);
        $stmt->execute();

        $mensaje = 'Camión agregado con éxito.';
        $tipo_mensaje = 'success';
    } elseif (isset($_POST['modificar_camion'])) {
        $id_camion = $_POST['id_camion'];
        $placas = $_POST['placas'];
        $peso = $_POST['peso'];
        $unidad = $_POST['unidad'];
        $tipo = $_POST['tipo'];
        $poliza_seguro = $_POST['poliza_seguro'];
        $gps = $_POST['gps'];

        $sql = "UPDATE camion 
                SET Placas = :placas, Peso = :peso, Unidad = :unidad, Tipo = :tipo, Poliza_Seguro = :poliza_seguro, GPS = :gps
                WHERE ID_Camion = :id_camion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':placas', $placas);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':unidad', $unidad);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':poliza_seguro', $poliza_seguro);
        $stmt->bindParam(':gps', $gps);
        $stmt->bindParam(':id_camion', $id_camion);
        $stmt->execute();

        $mensaje = 'Camión modificado con éxito.';
        $tipo_mensaje = 'success';
    } elseif (isset($_POST['accion_camion'])) {
        $id_camion = $_POST['id_camion'];
        $comentario = $_POST['comentario'];
        $accion = $_POST['accion_camion'];

        if ($accion === 'suspender') {
            $status = 'Suspendido';
        } elseif ($accion === 'dar_de_baja') {
            $status = 'Baja';
        } elseif ($accion === 'mantenimiento') {
            $status = 'Mantenimiento';
        }

        $sql = "UPDATE camion 
                SET Status = :status, fecha_final = NOW()
                WHERE ID_Camion = :id_camion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id_camion', $id_camion);
        $stmt->execute();

        $sql_comentario = "INSERT INTO comentarios_camion (ID_Camion, Comentario) VALUES (:id_camion, :comentario)";
        $stmt_comentario = $conn->prepare($sql_comentario);
        $stmt_comentario->bindParam(':id_camion', $id_camion);
        $stmt_comentario->bindParam(':comentario', $comentario);
        $stmt_comentario->execute();

        $mensaje = 'Acción realizada con éxito.';
        $tipo_mensaje = 'success';
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_camion']) && $_POST['accion_camion'] === 'mantenimiento') {
    $id_camion = $_POST['id_camion'];
    $comentario = $_POST['comentario'];
    $fecha_inicio = $_POST['fecha_inicio'];
    
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : NULL;

    $sql = "UPDATE camion 
            SET Status = 'Mantenimiento', FechaIM = :fecha_inicio, FechaFM = :fecha_fin, ComentarioMantenimiento = :comentario
            WHERE ID_Camion = :id_camion";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR); 
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':id_camion', $id_camion);
    
    try {
        $stmt->execute();
        header("Location: gestionar_camiones.php?success=1");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); 
    }
}

$sql_disponibles = "SELECT * FROM camion WHERE Status = 'Libre' OR Status = 'Ocupado' OR Status = 'Mantenimiento'";
$camiones_disponibles = $conn->query($sql_disponibles)->fetchAll(PDO::FETCH_ASSOC);

$sql_no_activos = "SELECT * FROM camion WHERE Status = 'Suspendido' OR Status = 'Baja'";
$camiones_no_activos = $conn->query($sql_no_activos)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Camiones</title>
    <link rel="stylesheet" href="assets/css/camion.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Listado de camiones</h1>
            <div class="actions">
                <button class="btn btn-secondary">Filtros</button>
                <a href="" class="btn btn-primary">Nuevo</a>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar por placas o tipo..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                <th><i class="fas fa-truck"></i> Placas</th>
                                <th><i class="fas fa-weight"></i> Peso</th>
                                <th><i class="fas fa-box"></i> Unidad</th>
                                <th><i class="fas fa-tag"></i> Tipo</th>
                                <th><i class="fas fa-file-contract"></i> Póliza de Seguro</th>
                                <th><i class="fas fa-satellite"></i> GPS</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Final</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php foreach ($camiones_disponibles as $camion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($camion['Placas']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Peso']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Unidad']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Tipo']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Poliza_Seguro']); ?></td>
                        <td><?php echo htmlspecialchars($camion['GPS']); ?></td>
                        <td><?php echo htmlspecialchars($camion['Status']); ?></td>
                        <td><?php echo htmlspecialchars($camion['fecha_inicio']); ?></td>
                        <td>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalMantenimientoCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                        <i class="fa fa-tools"></i> 
                    </button>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSuspenderCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                                <i class="fa fa-pause"></i>
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDarDeBajaCamion" data-id="<?php echo $camion['ID_Camion']; ?>">
                                <i class="fa fa-trash"></i> 
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalModificarCamion" data-id="<?php echo $camion['ID_Camion']; ?>"
                                data-placas="<?php echo $camion['Placas']; ?>" data-peso="<?php echo $camion['Peso']; ?>" data-unidad="<?php echo $camion['Unidad']; ?>"
                                data-tipo="<?php echo $camion['Tipo']; ?>" data-poliza_seguro="<?php echo $camion['Poliza_Seguro']; ?>" data-gps="<?php echo $camion['GPS']; ?>">
                                <i class="fa fa-edit"></i> 
                            </button>
                        </td>
                    </tr>
                    
                <?php endforeach; ?>

                        </td>

                    </tr>
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