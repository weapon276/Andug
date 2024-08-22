<?php
include 'conexion.php';
require('fpdf/fpdf.php');
session_start();

// Verificar si el parámetro id_factura está presente
if (isset($_GET['id_factura']) && isset($_SESSION['userId'])) {
    $id_factura = $_GET['id_factura'];
    $usuario_id = $_SESSION['userId']; // Asumiendo que el ID del usuario está almacenado en la sesión

    // Obtener los detalles de la factura
    $sql = "SELECT * FROM factura WHERE ID_Factura = :id_factura";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_factura', $id_factura);
    $stmt->execute();
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($factura) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Agregar contenido al PDF
        $pdf->Cell(0, 10, 'Factura ID: ' . $factura['ID_Factura'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Fecha: ' . $factura['Fecha'], 0, 1);
        $pdf->Cell(0, 10, 'Monto: $' . number_format($factura['Monto'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Impuesto: $' . number_format($factura['Impuesto'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Total: $' . number_format($factura['Total'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Tipo de Pago: ' . $factura['Tipo_Pago'], 0, 1);
        $pdf->MultiCell(0, 10, 'Descripción de Productos: ' . htmlspecialchars($factura['Descripcion_Productos']));

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Factura_' . $id_factura . '.pdf"');
        $pdf->Output();
        
        // Registrar la descarga después de enviar el PDF
        $sql_log = "INSERT INTO log_movimientos (user_id, accion, descripcion, fecha) VALUES (:userId, 'Descarga de PDF', :descripcion, NOW())";
        $stmt_log = $conn->prepare($sql_log);
        $descripcion = 'Descargó el PDF de la factura ID ' . $id_factura;
        $stmt_log->bindParam(':userId', $usuario_id);
        $stmt_log->bindParam(':descripcion', $descripcion);
        $stmt_log->execute();

        exit();
    } else {
        http_response_code(404);
        echo 'Factura no encontrada.';
    }
} else {
    http_response_code(400);
    echo 'ID de factura no proporcionado o usuario no autenticado.';
}
?>
