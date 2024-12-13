<?php
include 'modelo/conexion.php';

// Función para obtener todos los clientes
function obtenerClientes($conn) {
    $sql = "SELECT c.*, 
                   (SELECT SUM(monto) FROM factura WHERE ID_Cliente = c.ID_Cliente) AS Factura,
                   (SELECT estado FROM servicios WHERE ID_Cliente = c.ID_Cliente LIMIT 1) AS Estatus_Servicios,
                   (c.Linea_Credito - COALESCE(SUM(f.monto), 0)) AS Saldo_Linea_Credito
            FROM cliente c
            LEFT JOIN factura f ON c.ID_Cliente = f.fk_id_Cliente
            GROUP BY c.ID_Cliente";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todas las facturas de un cliente
function obtenerFacturasCliente($conn, $id_cliente) {
    $sql = "SELECT ID_Factura, Fecha, Monto, Estado_Pago, Total FROM factura WHERE fk_id_Cliente = :id_cliente";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_cliente', $id_cliente);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clientes = obtenerClientes($conn);

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_factura'])) {
    $id_factura = $_GET['id_factura'];
    $sql = "SELECT * FROM factura WHERE ID_Factura = :id_factura";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_factura', $id_factura);
    $stmt->execute();
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($factura);
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_factura']) && isset($_POST['usuario_id'])) {
    $id_factura = $_POST['id_factura'];
    $usuario_id = $_POST['usuario_id'];
    $sql = "INSERT INTO log_movimientos (id_usuario, accion, detalle, fecha) VALUES (:id_usuario, 'Descarga de PDF', :detalle, NOW())";
    $stmt = $conn->prepare($sql);
    $detalle = 'Descargó el PDF de la factura ID ' . $id_factura;
    $stmt->bindParam(':id_usuario', $usuario_id);
    $stmt->bindParam(':detalle', $detalle);
    $stmt->execute();
    echo 'Registro de descarga exitoso.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/clientes.css">
    <link rel="stylesheet" href="assets/css/servicios.css">
    <link rel="stylesheet" href="assets/css/modal.css">
</head>
<body>
    <main class="main-content">
        <div class="header animate-fade-in">
            <h1 class="title">Catálogo de clientes</h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openModal(null)">
                    <span>Nuevo</span>
                </button>
            </div>
        </div>

        <div class="search-bar animate-slide-in">
            <input type="text" class="search-input" placeholder="Buscar..." onkeyup="searchClients()">
        </div>
        <div class="table-container animate-slide-in">
            <table>
                <thead>
                    <tr>
                        <th>ID Cliente</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Tipo</th>
                        <th>Línea de Crédito</th>
                        <th>Pago Contado</th>
                        <th>Status</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): 
                        $estado_class = 'status-activo';
                        if ($cliente['Status'] == 'Suspendido') {
                            $estado_class = 'status-suspendido';
                        } elseif ($cliente['Status'] == 'Baja') {
                            $estado_class = 'status-baja';
                        }
                    ?>
                    <tr>
                        <td><?php echo $cliente['ID_Cliente']; ?></td>
                        <td><?php echo htmlspecialchars($cliente['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Direccion']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Tipo']); ?></td>
                        <td><?php echo $cliente['Linea_Credito']; ?></td>
                        <td><?php echo $cliente['Pago_Contado'] ? 'Sí' : 'No'; ?></td>
                        <td><span class="status-badge <?php echo $estado_class; ?>"><?php echo $cliente['Status']; ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn" onclick="openModal(<?php echo htmlspecialchars(json_encode($cliente)); ?>)">✏️</button>
                                <button class="action-btn" onclick="deleteClient(<?php echo $cliente['ID_Cliente']; ?>)">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span class="page-info">Mostrando 1 a 10 de <?php echo count($clientes); ?> registros</span>
            <div class="page-controls">
                <button class="page-btn" onclick="changePage('prev')">Anterior</button>
                <button class="page-btn">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn" onclick="changePage('next')">Siguiente</button>
            </div>
        </div>
    </main>
    <div id="clienteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-content" id="modalTitle">Editando al cliente</h2>
                <button class="close-button" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="clienteForm">
                    <input type="hidden" id="ID_Cliente" name="ID_Cliente">
                    <div class="form-section">
                        <div class="form-group">
                            <label class="form-label">
                                Nombre
                                <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" required>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                Dirección
                                <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="Direccion" name="Direccion" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Tipo
                                <span class="required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select class="form-control" id="Tipo" name="Tipo" required>
                                    <option value="Fisica">Física</option>
                                    <option value="Moral">Moral</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Tipo de crédito
                                <span class="required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select class="form-control" id="TipoCredito" name="TipoCredito" required>
                                    <option value="Limitado">Limitado</option>
                                    <option value="Ilimitado">Ilimitado</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="lineaCreditoGroup">
                            <label class="form-label">Línea de crédito</label>
                            <input type="number" class="form-control" id="Linea_Credito" name="Linea_Credito" step="0.01">
                        </div>
                        <div class="form-group" id="diasCreditoGroup">
                            <label class="form-label">Días de crédito</label>
                            <input type="number" class="form-control" id="Dias_Credito" name="Dias_Credito">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Pago al contado
                                <span class="required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select class="form-control" id="Pago_Contado" name="Pago_Contado" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                Status
                                <span class="required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select class="form-control" id="Status" name="Status" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Suspendido">Suspendido</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Motivo de baja</label>
                            <textarea class="form-control" id="Motivo_Baja" name="Motivo_Baja"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto/Logo</label>
                            <input type="file" class="form-control" id="Foto_Logo" name="Foto_Logo" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="Email" name="Email">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
                <button class="btn btn-primary" onclick="saveClient()">Guardar</button>
            </div>
        </div>
    </div>
    <script>
        function toggleLoading(button) {
            button.classList.add('loading');
            setTimeout(() => {
                button.classList.remove('loading');
            }, 2000);
        }
        function openModal(cliente) {
            const modal = document.getElementById('clienteModal');
            const form = document.getElementById('clienteForm');
            const modalTitle = document.getElementById('modalTitle');
            if (cliente) {
                modalTitle.textContent = 'Editando al cliente';
                Object.keys(cliente).forEach(key => {
                    const field = document.getElementById(key);
                    if (field) {
                        field.value = cliente[key];
                    }
                });
            } else {
                modalTitle.textContent = 'Nuevo cliente';
                form.reset();
            }
            toggleCreditoFields();
            modal.style.display = 'block';
        }
        function closeModal() {
            const modal = document.getElementById('clienteModal');
            modal.style.display = 'none';
        }
        function saveClient() {
            const form = document.getElementById('clienteForm');
            if (form.checkValidity()) {
                const formData = new FormData(form);
                // Here you would typically send this data to the server
                console.log('Saving client:', Object.fromEntries(formData));
                closeModal();
                // After saving, you should refresh the client list
            } else {
                form.reportValidity();
            }
        }
        function deleteClient(clientId) {
            if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
                // Here you would typically send a request to the server to delete the client
                console.log('Deleting client:', clientId);
                // After deleting, you should refresh the client list
            }
        }
        function toggleCreditoFields() {
            const tipoCredito = document.getElementById('TipoCredito');
            const lineaCreditoGroup = document.getElementById('lineaCreditoGroup');
            const diasCreditoGroup = document.getElementById('diasCreditoGroup');
            if (tipoCredito.value === 'Limitado') {
                lineaCreditoGroup.style.display = 'block';
                diasCreditoGroup.style.display = 'block';
            } else {
                lineaCreditoGroup.style.display = 'none';
                diasCreditoGroup.style.display = 'none';
            }
        }
        function searchClients() {
            const searchTerm = document.querySelector('.search-input').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        function changePage(direction) {
            // Implement pagination logic here
            console.log('Changing page:', direction);
        }
        // Event listeners
        document.getElementById('TipoCredito').addEventListener('change', toggleCreditoFields);
        document.querySelector('.search-input').addEventListener('input', searchClients);
        // Initial setup
        toggleCreditoFields();
    </script>
</body>
</html>