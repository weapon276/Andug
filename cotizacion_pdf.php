<?php
require('fpdf/fpdf.php');  // Asegúrate de que la ruta a FPDF sea correcta
include 'conexion.php';  // Conexión a la base de datos

// Verificar si se ha pasado un ID de cotización
if (!isset($_GET['id'])) {
    die('ID de cotización no especificado.');
}

$id_cotizacion = $_GET['id'];

// Obtener datos de la cotización
function obtenerCotizacionPorID($conn, $id) {
    $sql = "SELECT c.*, 
                   cl.Nombre AS ClienteNombre, 
                   cl.Direccion AS ClienteDireccion,
                   cl.Email AS ClienteEmail,
                   e.Nombre AS EmpleadoNombre
            FROM cotizacion c
            JOIN cliente cl ON c.ID_Cliente = cl.ID_Cliente
            JOIN empleado e ON c.fk_idEmpleado = e.ID_Empleado
            WHERE c.ID_Cotizacion = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$cotizacion = obtenerCotizacionPorID($conn, $id_cotizacion);

// Verificar si se encontró la cotización
if (!$cotizacion) {
    die('Cotización no encontrada.');
}

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Agregar el logo de la empresa
if (file_exists('img/1.png')) {
    $pdf->Image('img/1.png', 10, 10, 30);
}

// Información de la empresa
$pdf->SetXY(50, 10);
$pdf->MultiCell(0, 5, "Nombre de la Empresa\nDireccion de la Empresa\nTelefono: +123456789\nEmail: info@empresa.com");

// Información del cliente
$pdf->SetXY(130, 10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'Información del Cliente:', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(130, 20);
$pdf->MultiCell(0, 5, "Nombre: " . $cotizacion['ClienteNombre'] . "\nDireccion: " . $cotizacion['ClienteDireccion'] . "\nEmail: " . $cotizacion['ClienteEmail']);

// Título de la cotización
$pdf->SetXY(10, 40);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'COTIZACIÓN', 0, 1, 'C');
$pdf->Ln(5);

// Información de la cotización en tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'ID Cotización', 1);
$pdf->Cell(60, 10, 'Descripción', 1);
$pdf->Cell(30, 10, 'Monto', 1);
$pdf->Cell(30, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Vigencia', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, $cotizacion['ID_Cotizacion'], 1);
$pdf->Cell(60, 10, $cotizacion['Descripcion'], 1);
$pdf->Cell(30, 10, '$' . number_format($cotizacion['Monto'], 2), 1);
$pdf->Cell(30, 10, $cotizacion['Fecha'], 1);
$pdf->Cell(30, 10, $cotizacion['Vigencia'], 1);
$pdf->Ln();

// Añadir más detalles si es necesario
$pdf->Cell(40, 10, 'Punto de Origen', 1);
$pdf->Cell(60, 10, 'Punto de Destino', 1);
$pdf->Cell(30, 10, 'Fecha de Traslado', 1);
$pdf->Cell(30, 10, 'Horario de Carga', 1);
$pdf->Cell(30, 10, 'Horario de Descarga', 1);
$pdf->Ln();

$pdf->Cell(40, 10, $cotizacion['PuntoA_Origen'], 1);
$pdf->Cell(60, 10, $cotizacion['PuntoB_Destino'], 1);
$pdf->Cell(30, 10, $cotizacion['Fecha_Traslado'], 1);
$pdf->Cell(30, 10, $cotizacion['Horario_Carga'], 1);
$pdf->Cell(30, 10, $cotizacion['Horario_Descarga'], 1);
$pdf->Ln();

// Crear una nueva página para las cláusulas
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Cláusulas importantes:', 0, 1);
$pdf->SetFont('Arial', '', 10);
$clausulas = [
    "1.- La tarifa NO incluye IVA.",
    "2.- Solicitar equipo con 48 horas de anticipación.",
    "3.- Tiempo libre para carga o descarga, 8 horas a partir de llegada con cliente.(Solo Contenedores)",
    "4.- Estadías por no descarga se cobran aparte, $5,500 pesos por contenedor por Remolque, aplica en caso de 'JAULA' y 'PERNOTAJE' en puerto por DIAS NATURALES.",
    "5.- Otros cargos operativos adicionales a los detallados en tarifa, serán cobrados aparte.",
    "6.- Tarifa libre de maniobras para el transportista y/o operador.",
    "7.- La carga y contenedor NO viaja asegurada, ES RESPONSABILIDAD DEL CLIENTE.",
    "8.- La presente tarifa NO aplica para mercancía peligrosa, perecedera ni sobre dimencionada (NO CONTNEDORES FLAT RACK NI OPEN TOP).",
    "9.- El peso considerado en esta cotizacion es de 24 toneladas con tara de contenedor incluida.",
    "10.- Sobrepeso debera aplicarse 45% adicional de esta tarifa, exceso de Dimensiones seran cobrados aparte (en caso de aplicar).",
    "11.- La presente tarifa contempla unicamente el equipo de seguridad basico del operador (Casco, Lentes, Chaleco, Botas).",
    "12.- Una vez llegada la unidad a Destino, si no es descargada por causas ajenas al transportista, se cobrara FLETE EN FALSO por el 100%.",
    "13.- La presente tarifa NO contempla recolecciones y/o entregas en diferentes lugares, es servicio DEDICADO, NO CONSOLIDADO.",
    "14.- La presente tarifa contempla unicamente RECOLECCION EN PUNTO 'A' y ENTREGA EN PUNTO 'B' (NO REPARTOS) cualquier otra necesidad del cliente debera ser solicitada con anticipacion y sera cobrado aparte.",
    "15.- A reserva y disponibilidad de equipo.",
    "16.- La reserva de Equipo y CANCELACION del mismo el dia del servicio tendra un costo de $5,500 pesos.",
    "17.- Los dias de credito (si aplica) son a partir del servicio realizado, si por algun motivo se factura despues se especificara en la factura el dia del servicio para su consideracion y programacion de pago a partir de la fecha del servicio (en caso de aplicar).",
    "18.- El INCUMPLIMIENTO DE PAGO de los dias de credito en esta cotizacion se cobrar un 'Recargo' por cada dia de NO PAGO (por factura) (a consideracion del transportista).",
    "19.- La cancelacion de alguna factura tiene un costo de $1,500 peso mas IVA.",
    "20.- La presente tarifa aplica UNICAMENTE para servicios de TRANSPORTE.",
    "21.- Por 'Protocolos de seguridad', no cruzamos o evitamos zonas 'peligrosas' de noche, GDL, CELAYA, PUEBLA entre otros.",
    "22.- '*PISTAS INCLUIDAS' son en base a la ruta usada por el transporte, si el cliente desea una ruta en especifico se cobrar acorde a la ruta de su eleccion.",
    "23.- El cliente debe proporcionar en tiempo y forma informacion correcta y necesaria para elaboracion de 'Coplemento Carta Porte'.",
    "24.- Las tarifas presentadas son unicamente a PARQUES INDUSTRIALES, NO CENTRO DE CIUDAD, NO ZONAS HABITACIONALES NI DE DIMENCIONES ESTRECHAS.",
    "25.- Aplican restricciones de circulacion del estado destino del servicio.",
    "26.- La presente tarifa NO CONTEMPLA Traslados locales ni resguardo de contenedores, si el cliente lo requiere debera cotizarse y cobrado aparte.",
    "27.- *PUE, El pago de los servicios debera liquidarse antes de entregar los contenedores vacios.",
    "28.- Las presentes tarifas podran aplicar ajustes acorde a la direccion excata del domicilio de entrega.",
    "29.- La toma y/o entrega de contenedores llenos o vacios sera de acuerdo con la 'Cita establecida', ya sea por la naviera o el cliente.",
    "30.- Las presentes tarifas estan sujetas a cambios sin previo aviso por el incremento de COMBUSTIBLE o por otros costos indirectos relacionados con el transporte, asi como tambien aplican tarifas por temporada Alta, Naviera y/o Puerto."
];
foreach ($clausulas as $clausula) {
    $pdf->MultiCell(0, 10, utf8_decode($clausula), 0, 'L');
}

// Generar y enviar PDF
$pdf->Output('cotizacion.pdf', 'I');
?>
