<?php
include 'conexion.php';
require('fpdf/fpdf.php');
session_start();

// Verificar si el parámetro id_factura está presente y el usuario está autenticado
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
        // Obtener los detalles del cliente utilizando el ID_Cliente de la factura
        $sql_cliente = "SELECT * FROM cliente WHERE ID_Cliente = :id_cliente";
        $stmt_cliente = $conn->prepare($sql_cliente);
        $stmt_cliente->bindParam(':id_cliente', $factura['fk_id_Cliente']); // Supone que la columna ID_Cliente existe en factura
        $stmt_cliente->execute();
        $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

        // Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Agregar el logo de la empresa
        if (file_exists('img/1.png')) {
            $pdf->Image('img/1.png', 10, 10, 30); // Ajusta la ruta y tamaño del logo
        }

        // Título de la factura
        $pdf->Cell(190, 10, 'FACTURA', 0, 1, 'C');
        $pdf->Ln(10); // Espacio

        // Información de la empresa
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(100, 5, 'Nombre de la Empresa', 0, 1);
        $pdf->Cell(100, 5, 'Direccion de la Empresa', 0, 1);
        $pdf->Cell(100, 5, 'Telefono: +123456789', 0, 1);
        $pdf->Cell(100, 5, 'Email: info@empresa.com', 0, 1);

        $pdf->Ln(10); // Espacio

        // Información del cliente
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(100, 5, 'Informacion del Cliente:', 0, 1);
        if ($cliente) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(100, 5, 'Nombre: ' . $cliente['Nombre'], 0, 1);
            $pdf->Cell(100, 5, 'Direccion: ' . $cliente['Direccion'], 0, 1);
            $pdf->Cell(100, 5, 'Tipo: ' . $cliente['Tipo'], 0, 1);

        } else {
            $pdf->Cell(100, 5, 'Información no disponible.', 0, 1);
        }

        $pdf->Ln(10); // Espacio

        // Detalles de la factura
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 7, 'Fecha:', 0, 0);
        $pdf->Cell(50, 7, $factura['Fecha'], 0, 1);
        $pdf->Cell(50, 7, 'Factura ID:', 0, 0);
        $pdf->Cell(50, 7, $factura['ID_Factura'], 0, 1);

        $pdf->Ln(5); // Espacio

        // Productos y Totales
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 7, 'Descripcion de Productos', 1);
        $pdf->Cell(30, 7, 'Monto', 1);
        $pdf->Cell(30, 7, 'Impuesto', 1);
        $pdf->Cell(30, 7, 'Total', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(80, 7, htmlspecialchars($factura['Descripcion_Productos']), 1);
        $pdf->Cell(30, 7, '$' . number_format($factura['Monto'], 2), 1);
        $pdf->Cell(30, 7, '$' . number_format($factura['Impuesto'], 2), 1);
        $pdf->Cell(30, 7, '$' . number_format($factura['Total'], 2), 1);

        // Enviar el PDF al navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Factura_' . $id_factura . '.pdf"');
        $pdf->Output('D', 'Factura_' . $id_factura . '.pdf');

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
