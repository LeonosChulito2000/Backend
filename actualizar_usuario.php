<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include("db.php");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$nombres = $data['nombres'];
$apellido_paterno = $data['apellido_paterno'];
$apellido_materno = $data['apellido_materno'];
$telefono = $data['telefono'];
$direccion_envio = $data['direccion_envio'];
$numero_pedidos = $data['numero_pedidos'];

$sql = "UPDATE usuarios SET 
            nombres = '$nombres',
            apellido_paterno = '$apellido_paterno',
            apellido_materno = '$apellido_materno',
            telefono = '$telefono',
            direccion_envio = '$direccion_envio',
            numero_pedidos = '$numero_pedidos'
        WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Usuario actualizado y pedido contado."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar: " . $conn->error]);
}

$conn->close();
?>
