<?php
session_start();
require '../vendor/autoload.php';
include '../config/db.php';
require_once '../config/configuracion_smtp.php';


use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['usuario']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$pago_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario']['id'];

$sql_pago = "SELECT * FROM pagos WHERE id = $pago_id AND usuario_id = $usuario_id";
$result_pago = mysqli_query($conn, $sql_pago);
if (!$result_pago || mysqli_num_rows($result_pago) == 0) {
    die('Pago no encontrado.');
}
$pago = mysqli_fetch_assoc($result_pago);

$sql_detalles = "SELECT pd.*, p.destino 
                 FROM pago_detalle pd
                 INNER JOIN paquetes p ON pd.paquete_id = p.id
                 WHERE pd.pago_id = $pago_id";
$result_detalles = mysqli_query($conn, $sql_detalles);

$numero_factura = 'F-' . str_pad($pago_id, 5, '0', STR_PAD_LEFT);
$html = '<h1 style="text-align:center; color:#003366;">Pasaporte al Mundo</h1>
    <h2 style="text-align:center;">Factura de Pago</h2>
    <hr>
    <p><strong>N° Factura:</strong> ' . $numero_factura . '</p>
    <p><strong>Cliente:</strong> ' . htmlspecialchars($_SESSION['usuario']['nombre']) . '</p>
    <p><strong>Email:</strong> ' . htmlspecialchars($_SESSION['usuario']['email']) . '</p>
    <p><strong>Fecha de Pago:</strong> ' . date('d/m/Y H:i', strtotime($pago['fecha_pago'])) . '</p>
    <hr>
    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr style="background-color: #eeeeee;">
                <th>Destino</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';
while ($detalle = mysqli_fetch_assoc($result_detalles)) {
    $total_detalle = $detalle['precio_unitario'] * $detalle['cantidad'];
    $html .= '<tr>
                <td>' . htmlspecialchars($detalle['destino']) . '</td>
                <td>$' . number_format($detalle['precio_unitario'], 0, ',', '.') . '</td>
                <td>' . $detalle['cantidad'] . '</td>
                <td>$' . number_format($total_detalle, 0, ',', '.') . '</td>
              </tr>';
}
$html .= '</tbody></table>
    <h3 style="text-align:right;">Total Pagado: $' . number_format($pago['monto_total'], 0, ',', '.') . '</h3>
    <div style="margin-top:30px; text-align:center;">
        <img src="../assets/img/ticket.png" style="width:50px; vertical-align:middle;">
        <span style="font-size:24px; color:green; font-weight:bold;">PAGADO</span>
    </div>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdf = $dompdf->output();

$estado = enviarFacturaPorCorreo($_SESSION['usuario']['email'], $_SESSION['usuario']['nombre'], $pdf, $numero_factura);

if ($estado === true) {
    echo "<script>alert('Factura enviada por correo.'); window.location.href='historial_pagos.php';</script>";
} else {
    echo "Error: " . $estado;
}
?>