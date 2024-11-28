
<?php

include '../modelo/conexion.php';
include '../index.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dar de Alta Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Dar de Alta Cliente</h1>
    <form action="procesar_alta_cliente.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre (Responsable o Empresa)</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control" id="direccion" name="direccion" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Cliente</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="Fisica">Fisica</option>
                <option value="Moral">Moral</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="linea_credito" class="form-label">Línea de Crédito</label>
            <input type="number" class="form-control" id="linea_credito" name="linea_credito" step="0.01">
        </div>
        <div class="mb-3">
            <label for="pago_contado" class="form-label">Pago Contado</label>
            <select class="form-control" id="pago_contado" name="pago_contado" required>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Activo">Activo</option>
                <option value="Suspendido">Suspendido</option>
                <option value="Baja">Baja</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="foto_logo" class="form-label">Foto o Logo</label>
            <input type="file" class="form-control" id="foto_logo" name="foto_logo" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
