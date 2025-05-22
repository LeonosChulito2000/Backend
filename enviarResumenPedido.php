<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

header('Content-Type: application/json');

// Recibir los datos
$data = json_decode(file_get_contents('php://input'), true);

$correo = $data['correo'];
$numeroPedido = $data['numeroPedido'];
$usuario = $data['usuario'];
$productos = $data['productos'];
$fechaEntrega = $data['fechaEntrega'];

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '01shoetrackonline@gmail.com';
    $mail->Password = 'vagh pcur nygh hzaa'; // App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('01shoetrackonline@gmail.com', 'ShoeTrack');
    $mail->addAddress($correo);
    $mail->CharSet = 'UTF-8';
    $mail->addEmbeddedImage('logo.png', 'logo_cid');
    $mail->Subject = 'Resumen de tu Pedido - ShoeTrack';
    $mail->isHTML(true);

    // Generar HTML de productos
    $productosHTML = '';
    foreach ($productos as $item) {
        $productosHTML .= "
          <tr>
            <td>{$item['nombre']}</td>
            <td>{$item['sku']}</td>
            <td>{$item['talla']}</td>
            <td>{$item['color']}</td>
            <td>{$item['material']}</td>
            <td>{$item['cantidad']}</td>
            <td>$ {$item['precio']}</td>
          </tr>";
    }

    $mail->Body = "
    <html>
    <head>
      <meta charset='UTF-8'>
      <style>
        body {
          font-family: 'Josefin Sans', sans-serif;
          background-color: #1b1b2f;
          color: #dfb839;
        }
        .datos {
          font-size: 20px;
          color: #ffffff;
          font-family: 'Josefin Sans', sans-serif;
          margin-bottom: 20px;
        }
        .container {
          max-width: 700px;
          margin: auto;
          padding: 20px;
          background: linear-gradient(145deg, #000010, #1a0033);
          border-radius: 12px;
          box-shadow: 0 0 12px rgba(0,0,0,0.5);
        }
        h1, h2 {
          color: #ffffff;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
          color: #ffffff;
        }
        th, td {
          padding: 10px;
          border: 1px solid #444;
          text-align: center;
        }
                  .logo {
          width: 100px;
          margin: 10px auto 20px;
          display: block;
        }

        .resaltado {
          font-size: 24px;
          margin-top: 30px;
          color: #00ffff;
        }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>
          <h1>ShoeTrack</h1>
          <img src='cid:logo_cid' alt='Logo de ShoeTrack' class='logo' />
        </div>

        <h1>🎉 ¡Gracias por tu compra en ShoeTrack!</h1>
        <h2 class='resaltado'>Pedido #{$numeroPedido}</h2>

        <p class='datos'><strong>Nombre:</strong> {$usuario['nombres']} {$usuario['apellido_paterno']} {$usuario['apellido_materno']}</p>
        <p class='datos'><strong>Dirección:</strong> {$usuario['direccion_envio']}</p>
        <p class='datos'><strong>Teléfono:</strong> {$usuario['telefono']}</p>
        <p class='datos'><strong>Correo:</strong> {$usuario['correo']}</p>
        <p class='datos'><strong>Fecha estimada de entrega:</strong> {$fechaEntrega}</p>

        <h2>Resumen de productos:</h2>
        <table>
          <tr>
            <th>Nombre</th>
            <th>SKU</th>
            <th>Talla</th>
            <th>Color</th>
            <th>Material</th>
            <th>Cantidad</th>
            <th>Precio</th>
          </tr>
          {$productosHTML}
        </table>

        <p style='margin-top: 40px; color: #aaa; font-size: 12px;'>ShoeTrack &copy; " . date('Y') . " - Todos los derechos reservados</p>
      </div>
    </body>
    </html>";

    $mail->send();
    echo json_encode(['success' => true, 'mensaje' => 'Resumen enviado']);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al enviar resumen: ' . $mail->ErrorInfo
    ]);
}
