<?php
include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

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



$clientes = obtenerClientes($conn);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table tr td {
            vertical-align: middle;
        }
        .estado-suspendido {
            background-color: #fff3cd;
            color: #856404;
        }
        .estado-baja {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Clientes</h1>
    <table class="table table-bordered">
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
                $estado_class = '';
                if ($cliente['Status'] == 'Suspendido') {
                    $estado_class = 'estado-suspendido';
                } elseif ($cliente['Status'] == 'Baja') {
                    $estado_class = 'estado-baja';
                }
            ?>
            <tr class="<?php echo $estado_class; ?>">
            <td>
    <a href="#" data-bs-toggle="modal" data-bs-target="#infoClienteModal<?php echo $cliente['ID_Cliente']; ?>">
        <?php echo $cliente['ID_Cliente']; ?>
    </a>
</td>
                <td><?php echo htmlspecialchars($cliente['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($cliente['Direccion']); ?></td>
                <td><?php echo htmlspecialchars($cliente['Tipo']); ?></td>
                <td><?php echo $cliente['Linea_Credito']; ?></td>
                <td><?php echo $cliente['Pago_Contado'] ? 'Sí' : 'No'; ?></td>
                <td><?php echo $cliente['Status']; ?></td>
                <td>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modificarClienteModal<?php echo $cliente['ID_Cliente']; ?>">
                        <i class="bi bi-book"></i>
                    </button>
                    <form method="POST" action="suspender_cliente.php" style="display: inline;">
                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </button>
                    </form>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#bajaClienteModal<?php echo $cliente['ID_Cliente']; ?>">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </td>
            </tr>

            <!-- Modal Modificar Cliente -->
            <div class="modal fade" id="modificarClienteModal<?php echo $cliente['ID_Cliente']; ?>" tabindex="-1" aria-labelledby="modificarClienteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="modificar_cliente.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modificarClienteModalLabel">Modificar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['Nombre']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="3" required><?php echo htmlspecialchars($cliente['Direccion']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Cliente</label>
                                    <select class="form-control" id="tipo" name="tipo" required>
                                        <option value="Fisica" <?php echo $cliente['Tipo'] == 'Fisica' ? 'selected' : ''; ?>>Fisica</option>
                                        <option value="Moral" <?php echo $cliente['Tipo'] == 'Moral' ? 'selected' : ''; ?>>Moral</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="linea_credito" class="form-label">Línea de Crédito</label>
                                    <input type="number" class="form-control" id="linea_credito" name="linea_credito" step="0.01" value="<?php echo $cliente['Linea_Credito']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="pago_contado" class="form-label">Pago Contado</label>
                                    <select class="form-control" id="pago_contado" name="pago_contado" required>
                                        <option value="1" <?php echo $cliente['Pago_Contado'] ? 'selected' : ''; ?>>Sí</option>
                                        <option value="0" <?php echo !$cliente['Pago_Contado'] ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="Activo" <?php echo $cliente['Status'] == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="Suspendido" <?php echo $cliente['Status'] == 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                                        <option value="Baja" <?php echo $cliente['Status'] == 'Baja' ? 'selected' : ''; ?>>Baja</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Dar de Baja Cliente -->
            <div class="modal fade" id="bajaClienteModal<?php echo $cliente['ID_Cliente']; ?>" tabindex="-1" aria-labelledby="bajaClienteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="baja_cliente.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bajaClienteModalLabel">Dar de Baja Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['ID_Cliente']; ?>">
                                <div class="mb-3">
                                    <label for="motivo_baja" class="form-label">Motivo de Baja</label>
                                    <textarea class="form-control" id="motivo_baja" name="motivo_baja" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-danger">Dar de Baja</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Clientes Dados de Baja</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Cliente</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Tipo</th>
                <th>Línea de Crédito</th>
                <th>Pago Contado</th>
                <th>Status</th>
                <th>Motivo de Baja</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): 
                if ($cliente['Status'] == 'Baja'):
            ?>
            <tr class="estado-baja">
                <td><?php echo $cliente['ID_Cliente']; ?></td>
                <td><?php echo htmlspecialchars($cliente['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($cliente['Direccion']); ?></td>
                <td><?php echo htmlspecialchars($cliente['Tipo']); ?></td>
                <td><?php echo $cliente['Linea_Credito']; ?></td>
                <td><?php echo $cliente['Pago_Contado'] ? 'Sí' : 'No'; ?></td>
                <td><?php echo $cliente['Status']; ?></td>
                <td><?php echo htmlspecialchars($cliente['Motivo_Baja']); ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Modal Información del Cliente -->
<div class="modal fade" id="infoClienteModal<?php echo $cliente['ID_Cliente']; ?>" tabindex="-1" aria-labelledby="infoClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoClienteModalLabel">Información del Cliente: <?php echo $cliente['Nombre']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Dirección:</h6>
                <p><?php echo htmlspecialchars($cliente['Direccion']); ?></p>
                
                <h6>Tipo:</h6>
                <p><?php echo htmlspecialchars($cliente['Tipo']); ?></p>

                <h6>Línea de Crédito:</h6>
                <p><?php echo $cliente['Linea_Credito']; ?></p>

                <h6>Saldo de la Línea de Crédito:</h6>
                <p><?php echo $cliente['Saldo_Linea_Credito']; // Ajusta este campo según tus datos ?></p>

                <h6>Estatus de Servicios:</h6>
                <p><?php echo $cliente['Estatus_Servicios']; // Ajusta este campo según tus datos ?></p>

                <h6>Facturas:</h6>
                <p><?php echo $cliente['Factura']; // Ajusta este campo según tus datos ?></p>

                <h6>Pago Contado:</h6>
                <p><?php echo $cliente['Pago_Contado'] ? 'Sí' : 'No'; ?></p>

                <h6>Status:</h6>
                <p><?php echo $cliente['Status']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
