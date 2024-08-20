<?php

include 'conexion.php';
include 'index.php';

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

// Función para obtener todos los datos de una tabla específica
function obtenerDatos($conn, $tabla) {
    $sql = "SELECT * FROM $tabla";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para generar un archivo XML
function generarXML($datos, $nombreArchivo) {
    $dom = new DOMDocument('1.0', 'utf-8');
    $root = $dom->createElement('data');
    foreach ($datos as $fila) {
        $item = $dom->createElement('item');
        foreach ($fila as $clave => $valor) {
            $element = $dom->createElement($clave, htmlspecialchars($valor));
            $item->appendChild($element);
        }
        $root->appendChild($item);
    }
    $dom->appendChild($root);
    $dom->save($nombreArchivo);
}

// Función para generar un archivo PDF usando FPDF
function generarPDF($datos, $nombreArchivo, $titulo) {
    require('pdf/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $titulo, 0, 1, 'C');

    // Crear encabezados
    $pdf->SetFont('Arial', 'B', 10);
    foreach (array_keys($datos[0]) as $columna) {
        $pdf->Cell(40, 10, $columna, 1);
    }
    $pdf->Ln();

    // Crear filas
    $pdf->SetFont('Arial', '', 10);
    foreach ($datos as $fila) {
        foreach ($fila as $valor) {
            $pdf->Cell(40, 10, $valor, 1);
        }
        $pdf->Ln();
    }
    $pdf->Output('F', $nombreArchivo);
}

// Función para obtener el historial completo
function obtenerHistorial($conn) {
    $tablas = ['cliente', 'viaje', 'operador', 'camion', 'usuarios', 'factura', 'liquidacion'];
    $historial = [];
    foreach ($tablas as $tabla) {
        $historial[$tabla] = obtenerDatos($conn, $tabla);
    }
    return $historial;
}

// Verificar las acciones del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo_reporte = $_POST['tipo_reporte'];
    $formato = $_POST['formato'];

    if ($tipo_reporte == 'historial') {
        $datos = obtenerHistorial($conn);
    } else {
        $datos = obtenerDatos($conn, $tipo_reporte);
    }

    $timestamp = date('Ymd_His');
    if ($formato == 'xml') {
        $nombreArchivo = "reporte_{$tipo_reporte}_{$timestamp}.xml";
        generarXML($datos, $nombreArchivo);
    } elseif ($formato == 'pdf') {
        $nombreArchivo = "reporte_{$tipo_reporte}_{$timestamp}.pdf";
        generarPDF($datos, $nombreArchivo, "Reporte de " . ucfirst($tipo_reporte));
    }

    header("Content-Disposition: attachment; filename=$nombreArchivo");
    header("Content-Type: application/octet-stream");
    readfile($nombreArchivo);
    unlink($nombreArchivo); // Eliminar el archivo después de la descarga
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generar Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Margen de tabla y menu lateral -->
<div class="w3-main" style="margin-left:320px;margin-top:60px;">
<!--Fin de margen -->
<body>
<div class="container mt-5">
    <h1>Generar Reportes</h1>
    <form method="post">
        <div class="mb-3">
            <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
            <select class="form-select" id="tipo_reporte" name="tipo_reporte" required>
                <option value="cliente">Clientes</option>
                <option value="viaje">Viajes</option>
                <option value="operador">Operadores</option>
                <option value="camion">Camiones</option>
                <option value="usuarios">Usuarios</option>
                <option value="factura">Facturas</option>
                <option value="liquidacion">Liquidaciones</option>
                <option value="historial">Historial Completo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="formato" class="form-label">Formato</label>
            <select class="form-select" id="formato" name="formato" required>
                <option value="xml">XML</option>
                <option value="pdf">PDF</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Generar Reporte</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
