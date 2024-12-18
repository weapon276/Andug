<?php
    require_once 'modelo/conexion.php';

    // Configuración de paginación y ordenamiento
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'ID_Dolly';
    $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Calcular offset para la paginación
    $offset = ($page - 1) * $per_page;

    // Consulta SQL base con parámetros posicionales
    $sql = "SELECT * FROM dolly 
            WHERE Placas LIKE ? OR Marca LIKE ? OR Modelo LIKE ?
            ORDER BY $sort $order
            LIMIT ? OFFSET ?";

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bindValue(1, $search_term, PDO::PARAM_STR); // Primer marcador
    $stmt->bindValue(2, $search_term, PDO::PARAM_STR); // Segundo marcador
    $stmt->bindValue(3, $search_term, PDO::PARAM_STR); // Tercer marcador
    $stmt->bindValue(4, (int)$per_page, PDO::PARAM_INT); // Cuarto marcador
    $stmt->bindValue(5, (int)$offset, PDO::PARAM_INT); // Quinto marcador
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener total de registros para la paginación
    $total_sql = "SELECT COUNT(*) as total FROM dolly WHERE Placas LIKE ? OR Marca LIKE ? OR Modelo LIKE ?";
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->bindValue(1, $search_term, PDO::PARAM_STR);
    $total_stmt->bindValue(2, $search_term, PDO::PARAM_STR);
    $total_stmt->bindValue(3, $search_term, PDO::PARAM_STR);
    $total_stmt->execute();
    $total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_records / $per_page);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Dolly</title>
    <link rel="stylesheet" href="assets/css/camion.css">
    <link rel="stylesheet" href="assets/css/servicios.css">
    <link rel="stylesheet" href="assets/css/rservicios.css">
    <link rel="stylesheet" href="assets/css/dolly.css">
    <link rel="stylesheet" href="assets/css/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Listado de Dolly</h1>
            <div class="actions">
                <button id="btnNuevoDolly" class="btn btn-primary">Agregar Dolly</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar por placas, marca o modelo..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-truck"></i> Placas</th>
                        <th data-sort="Marca">Marca</th>
                        <th data-sort="Modelo">Modelo</th>
                        <th data-sort="Año">Año</th>
                        <th data-sort="PesoDolly">Peso</th>
                        <th data-sort="Capacidad_Carga">Capacidad de Carga</th>
                        <th data-sort="Dimensiones">Dimensiones</th>
                        <th data-sort="estado">Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($result)) : ?>
                        <?php foreach ($result as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Placas']); ?></td>
                                <td><?php echo htmlspecialchars($row['Marca']); ?></td>
                                <td><?php echo htmlspecialchars($row['Modelo']); ?></td>
                                <td><?php echo htmlspecialchars($row['Año']); ?></td>
                                <td><?php echo htmlspecialchars($row['PesoDolly']); ?></td>
                                <td><?php echo htmlspecialchars($row['Capacidad_Carga']); ?></td>
                                <td><?php echo htmlspecialchars($row['Dimensiones']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $row['estado'])); ?>">
                                        <?php echo htmlspecialchars($row['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn edit-btn" data-id="<?php echo $row['ID_Dolly']; ?>">✏️</button>
                                    <button class="action-btn change-state-btn" data-id="<?php echo $row['ID_Dolly']; ?>">📋</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9">No se encontraron resultados.</td>
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
    </div>    </div>

    <!-- Modal para Nuevo Dolly -->
    <div id="modalNuevoDolly" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Agregar Nuevo Dolly</h2>
            <form id="formNuevoDolly">
                <div class="form-group">
                    <label for="nuevaMarca">Marca:</label>
                    <input type="text" id="nuevaMarca" name="Marca" required>
                </div>
                <div class="form-group">
                    <label for="nuevoModelo">Modelo:</label>
                    <input type="text" id="nuevoModelo" name="Modelo" required>
                </div>
                <div class="form-group">
                    <label for="nuevoAño">Año:</label>
                    <input type="number" id="nuevoAño" name="Año" required>
                </div>
                <div class="form-group">
                    <label for="nuevasPlacas">Placas:</label>
                    <input type="text" id="nuevasPlacas" name="Placas" required>
                </div>
                <div class="form-group">
                    <label for="nuevoPesoDolly">Peso:</label>
                    <input type="number" id="nuevoPesoDolly" name="PesoDolly" step="0.000001" required>
                </div>
                <div class="form-group">
                    <label for="nuevaCapacidadCarga">Capacidad de Carga:</label>
                    <input type="number" id="nuevaCapacidadCarga" name="Capacidad_Carga" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="nuevasDimensiones">Dimensiones:</label>
                    <input type="text" id="nuevasDimensiones" name="Dimensiones" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
        
    </div>

    
    <!-- Modal para Cambiar Estado -->
    <div id="modalCambiarEstado" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Cambiar Estado del Dolly</h2>
            <form id="formCambiarEstado">
                <input type="hidden" id="dollyId" name="ID_Dolly">
                <div class="form-group">
                    <label for="nuevoEstado">Nuevo Estado:</label>
                    <select id="nuevoEstado" name="estado" required>
                        <option value="en servicio">En servicio</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="matenimiento">Mantenimiento</option>
                        <option value="dado de baja">Dado de baja</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
        
    </div>


    <script>

        const modalNuevoDolly = document.getElementById('modalNuevoDolly');
        const modalCambiarEstado = document.getElementById('modalCambiarEstado');
        const btnNuevoDolly = document.getElementById('btnNuevoDolly');
        const closeBtns = document.getElementsByClassName('close');

        btnNuevoDolly.onclick = function() {
            modalNuevoDolly.style.display = "block";
        }

        for (let closeBtn of closeBtns) {
            closeBtn.onclick = function() {
                modalNuevoDolly.style.display = "none";
                modalCambiarEstado.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == modalNuevoDolly) {
                modalNuevoDolly.style.display = "none";
            }
            if (event.target == modalCambiarEstado) {
                modalCambiarEstado.style.display = "none";
            }
        }

        // Handle form submissions
        document.getElementById('formNuevoDolly').onsubmit = function(e) {
            e.preventDefault();
            // Add code here to handle form submission (e.g., AJAX request)
            console.log('Nuevo Dolly form submitted');
            modalNuevoDolly.style.display = "none";
        }

        document.getElementById('formCambiarEstado').onsubmit = function(e) {
            e.preventDefault();
            // Add code here to handle form submission (e.g., AJAX request)
            console.log('Cambiar Estado form submitted');
            modalCambiarEstado.style.display = "none";
        }

        // Handle edit and change state buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = function() {
                const dollyId = this.getAttribute('data-id');
                console.log('Edit Dolly ID:', dollyId);
                // Add code here to handle editing (e.g., open edit modal)
            }
        });

        document.querySelectorAll('.change-state-btn').forEach(btn => {
            btn.onclick = function() {
                const dollyId = this.getAttribute('data-id');
                document.getElementById('dollyId').value = dollyId;
                modalCambiarEstado.style.display = "block";
            }
        });
    </script>
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