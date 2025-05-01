<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include("db.php");

$data = json_decode(file_get_contents("php://input"), true);

$nombres = $data['nombres'];
$apellido_paterno = $data['apellido_paterno'];
$apellido_materno = $data['apellido_materno'];
$correo = $data['correo'];
$telefono = $data['telefono'];
$fecha_registro = date('Y-m-d');
$direccion_envio = $data['direccion_envio'];
$numero_pedidos = 1;

$sql = "INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, correo, telefono, fecha_registro, direccion_envio, numero_pedidos)
        VALUES ('$nombres', '$apellido_paterno', '$apellido_materno', '$correo', '$telefono', '$fecha_registro', '$direccion_envio', '$numero_pedidos')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Usuario registrado y pedido contado."]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
