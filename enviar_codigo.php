<?php
// HABILITAR CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

header('Content-Type: application/json');

// Recibe JSON
$data = json_decode(file_get_contents('php://input'), true);
$destino = $data['destino'];
$codigo = $data['codigo'];

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '01shoetrackonline@gmail.com';
    $mail->Password = 'vagh pcur nygh hzaa'; // App Password de Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('01shoetrackonline@gmail.com', 'ShoeTrack');
    $mail->addAddress($destino);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Tu código de verificación';
    $mail->isHTML(true);

    $mail->Body = "
    <html>
    <head>
      <meta charset='UTF-8'>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap');

        body {
          margin: 0;
          padding: 0;
          backdrop-filter: blur(100px);
          font-family: 'Josefin Sans', sans-serif;
          color: #dfb839;
          font-weight: bold;
        }

        .container {
          max-width: 600px;
          margin: 40px auto;
          background: linear-gradient(145deg, #000010 0%, #0a0a3c 20%, #4b0082 40%, #330066 60%, #4b0082 80%, #800080 100%);
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 4px 20px rgba(0,0,0,0.5);
          padding: 20px;
          text-align: center;
        }

        .header h1 {
          font-size: 36px;
          margin-bottom: 10px;
          color: #FFF;
        }

        .logo {
          width: 100px;
          margin: 10px auto 20px;
          display: block;
        }

        .content p {
          font-size: 18px;
          color: #dfb839;
          margin-bottom: 20px;
        }

        .code {
          font-size: 42px;
          font-weight: bold;
          letter-spacing: 10px;
          background: linear-gradient(
              -180deg,
              rgb(0, 0, 0) 0%,
              rgb(52, 23, 35) 30%,
              rgb(81, 44, 73) 50%,
              rgb(101, 55, 83) 70%,
              rgb(181, 88, 100) 100%
          );
          color: #fff;
          padding: 20px 30px;
          border-radius: 12px;
          display: inline-block;
          box-shadow: inset 0 0 100px rgba(0, 0, 0, 1);
          margin-bottom: 30px;
        }

        .footer {
          font-size: 12px;
          color: #999;
          margin-top: 40px;
        }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>
          <h1>ShoeTrack</h1>
          <img src='cid:logo_cid' alt='Logo de ShoeTrack' class='logo' />
        </div>
        <div class='content'>
          <p>Gracias por preferir <strong>Shoe Track</strong>. Estamos felices de acompañarte en tu camino.</p>
          <p>Tu código de verificación es:</p>
          <div class='code'>$codigo</div>
          <p>Ingresa este código en el sitio web para continuar con tu compra.</p>
        </div>
        <div class='footer'>
          &copy; " . date('Y') . " ShoeTrack. Todos los derechos reservados.
        </div>
      </div>
    </body>
    </html>
    ";

    // Embebemos el logo (logo.png debe estar en el mismo directorio)
    $mail->addEmbeddedImage('logo.png', 'logo_cid');

    $mail->send();
    echo json_encode(['success' => true, 'mensaje' => 'Correo enviado exitosamente']);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo
    ]);
}
