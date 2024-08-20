<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Recursos Humanos') {
    header("Location: login.php");
    exit();
}

// Función para registrar un nuevo empleado
function registrarEmpleado($conn, $nombre, $departamento, $posicion, $fechaContratacion, $salario, $status) {
    $sql = "INSERT INTO empleado (Nombre, Departamento, Posicion, Fecha_Contratacion, Salario, Status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $departamento, $posicion, $fechaContratacion, $salario, $status]);
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $posicion = $_POST['posicion'];
    $fechaContratacion = $_POST['fecha_contratacion'];
    $salario = $_POST['salario'];
    $status = $_POST['status'];

    registrarEmpleado($conn, $nombre, $departamento, $posicion, $fechaContratacion, $salario, $status);

    header("Location: gestionar_empleados.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Registrar Empleado</h1>
    <form method="post">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento</label>
            <input type="text" class="form-control" id="departamento" name="departamento" required>
        </div>
        <div class="mb-3">
            <label for="posicion" class="form-label">Posición</label>
            <input type="text" class="form-control" id="posicion" name="posicion" required>
        </div>
        <div class="mb-3">
            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
            <input type="date" class="form-control" id="fecha_contratacion" name="fecha_contratacion" required>
        </div>
        <div class="mb-3">
            <label for="salario" class="form-label">Salario</label>
            <input type="number" step="0.01" class="form-control" id="salario" name="salario" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Activo"></option>
                <option value="ausente"></option>
                <option value="Baja"></option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
