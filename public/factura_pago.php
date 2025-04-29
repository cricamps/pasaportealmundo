<?php
session_start();
require '../vendor/autoload.php'; // si usas Composer

use Dompdf\Dompdf;
use Dompdf\Options;
include '../config/db.php';

if (!isset($_SESSION['usuario']) || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$pago_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario']['id'];

// Buscar el pago
$sql_pago = "SELECT * FROM pagos WHERE id = $pago_id AND usuario_id = $usuario_id";
$result_pago = mysqli_query($conn, $sql_pago);

if (!$result_pago || mysqli_num_rows($result_pago) == 0) {
    die('Pago no encontrado.');
}

$pago = mysqli_fetch_assoc($result_pago);

// Buscar detalles
$sql_detalles = "SELECT pd.*, p.destino 
                 FROM pago_detalle pd
                 INNER JOIN paquetes p ON pd.paquete_id = p.id
                 WHERE pd.pago_id = $pago_id";
$result_detalles = mysqli_query($conn, $sql_detalles);

// Generar número de factura (formato F-00001)
$numero_factura = 'F-' . str_pad($pago_id, 5, '0', STR_PAD_LEFT);

// Crear el HTML para el PDF
$html = '
    <h1 style="text-align:center; color:#003366;">Pasaporte al Mundo</h1>
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

$html .= '</tbody>
    </table>
    <h3 style="text-align:right; margin-top:20px;">Total Pagado: $' . number_format($pago['total'], 0, ',', '.') . '</h3>
    <div style="margin-top:50px; text-align:center;">
        <span style="font-size:24px; color:green; font-weight:bold;">✔️ PAGADO</span>
    </div>';

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Formatear nombre para el archivo (sin espacios ni caracteres raros)
$nombre_usuario = preg_replace('/[^A-Za-z0-9]/', '', $_SESSION['usuario']['nombre']);

// Descargar PDF
$dompdf->stream('Factura_' . $numero_factura . '_' . $nombre_usuario . '.pdf', ['Attachment' => true]);
exit;
